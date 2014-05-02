<?php
namespace bright\core\factories;

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
}