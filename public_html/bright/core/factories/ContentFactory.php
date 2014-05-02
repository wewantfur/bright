<?php
namespace bright\core\factories;

use bright\core\auth\Authorization;

use bright\core\interfaces\IContent;

use bright\core\exceptions\ContentException;

use bright\core\model\Model;

use bright\core\interfaces\IContentFactory;

abstract class ContentFactory implements IContentFactory {
	
	/**
	 * Checks if the given template is in use
	 * @param int $templateId
	 */
	public static function contentOfTemplateExists($templateId) {
		return Model::getInstance() -> getField("SELECT COUNT(contentId) FROM content WHERE templateId=?", $templateId) > 0;
	}
	
	/**
	 * Deletes content from the database, no going back from here
	 */
	public static function deleteContent($contentId) {
		
	}
	
	/**
	 * Marks content as 'deleted', but does not remove it from the database
	 */
	public static function trashContent($contentId) {
		
	}
	
	/**
	 * Stores the content in the database
	 * @param IContent $content
	 * @return IContent $content The stored content
	 */
	public static function setContent($content) {
		$beuser = Authorization::getBEUser();
		
		if(!$content -> templateId)
			throw new ContentException('NO_TEMPLATE', ContentException::NO_TEMPLATE);
		
		// Get the template, this also checks if the given templateId is valid
		$template = TemplateFactory::getTemplate($content -> templateId);
		
		$contentId = self::_insertIntoContent($content, $beuser);
		
		if(!$contentId)
			throw new ContentException('CANNOT_SAVE', ContentException::CANNOT_SAVE);

		$content -> contentId = $contentId;
		
		self::_insertFields($content, $template);
		
		return $content;
	}
	
	/**
	 * Creates or updates the content table
	 * @param IContent $content
	 * @param \bright\core\model\vo\BEUser $beuser
	 */
	private static function _insertIntoContent($content, $beuser) {
		$id = Model::getInstance() -> updateRow("INSERT INTO `content` (contentId, templateId, creationdate, modificationdate, createdby, modifiedby, UID, GID) VALUES
				(?, ?, NOW(), NULL, ?, NULL, ?, ?)
				ON DUPLICATE KEY UPDATE templateId=VALUES(templateId),
				modificationdate=NOW(),
				modifiedby=?,
				contentId=LAST_INSERT_ID(contentId)",
				array($content -> contentId,
						$content -> templateId,
						$beuser -> UID,
						$beuser -> UID,
						$beuser -> default_GID,
						$beuser -> UID));
		
		return $id;
	}
	
	private static function _insertFields($content, $template) {
		$fields = array();
		// First, mark everything as deleted.
		Model::getInstance() -> updateRow("UPDATE fields SET deleted=1 WHERE contentId=?", array($content -> contentId));
		
			
		foreach($template -> fields as $field) {
			// Check if field is set
			if(!isset($content -> content -> {$field -> label}))
				continue;
		
			self::_insertLocalizedFields($content, $field);
		}
		
	}
	
	private static function _insertLocalizedFields($content, $field) {
		
		$langs = explode(',', AVAILABLELANG);
		$label = $field -> label;
		
		foreach($langs as $lang) {
			// Loop over languages
			if(!isset($content -> content -> $label -> $lang))
				continue;
			
			switch($field -> fieldtype) {
				case 'list':
					// Special
					$i = 0;
					foreach($content -> content -> $label -> $lang as &$listitem) {
						$listitem -> parentId = $content -> contentId;
						$listitem -> idx = $i++;
						self::setContent($listitem);
						Model::getInstance() -> updateRow("INSERT INTO plugin_list (contentId, parentId, idx) VALUES (?,?,?)", array($listitem -> contentId, $listitem -> parentId, $listitem -> idx));
					}
				default:
					$item = $content -> content -> $label -> $lang;
					
					if(is_scalar($item)) {
						// 	$item = mysqli_real_escape_string($item);
						$fieldsql = "INSERT INTO fields (contentId, lang, field, deleted) 
											VALUES (?,?,?, 0) 
											ON DUPLICATE KEY UPDATE deleted=0, 
											id=LAST_INSERT_ID(id)";
						
						$id = Model::getInstance() -> updateRow($fieldsql, array($content -> contentId, $lang, $label));
						
						$pluginsql = "INSERT INTO plugin_{$field -> fieldtype} (fieldId, `{$field -> fieldtype}`) 
										VALUES (?,?) 
										ON DUPLICATE KEY UPDATE 
										`{$field -> fieldtype}`=VALUES(`{$field -> fieldtype}`)";
						
						Model::getInstance() -> updateRow($pluginsql, array($id, $item));
					}
	
			}
		}
		
	}
}