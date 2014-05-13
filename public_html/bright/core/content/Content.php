<?php
namespace bright\core\content;

use bright\core\factories\TemplateFactory;

use bright\core\auth\Authorization;

use bright\core\Utils;

use bright\core\model\Model;

class Content {
/**
	 * Gets all the content of a contentitem
	 * @param int $contentId The id of the contentitem
	 */
	public final static function getContent($contentId) {
		
		$template = TemplateFactory::getTemplateByContentId($contentId);
		$ctype = '\bright\core\model\vo\Page';
		switch($template -> type) {
			case TemplateFactory::TYPE_PAGE:
				$joins[] = "INNER JOIN pages ttype ON ttype.contentId = c.contentId";
				break;
		}
		foreach($template -> fields as $field) {
			// @todo store this in db
			$select[] = "field_{$field -> label}.fieldId as `content.{$field -> label}.id`";
			switch($field -> type) {
				case 'image':
					$select[] = "field_{$field -> label}.path as `content.{$field -> label}.path`";
					$select[] = "field_{$field -> label}.description as `content.{$field -> label}.description`";
					$select[] = "field_{$field -> label}.caption as `content.{$field -> label}.caption`";
					$select[] = "field_{$field -> label}.url as `content.{$field -> label}.url`";
					$fields[$field -> label] = array('path','description','caption','url');
					break;
				case 'string':
					$select[] = "field_{$field -> label}.string as `content.{$field -> label}.string`";
					$fields[$field -> label] = array('string');
					break;
				case 'list':
					$select[] = "field_{$field -> label}.items as `content.{$field -> label}.items`";
					$fields[$field -> label] = array('list');
					break;
			}
			$joins[] = "LEFT JOIN plugin_{$field -> type} field_{$field -> label} ON field_{$field -> label}.fieldId=f.id";
		}
		$sfields = implode(',', $select);
		$jfields = implode("\r\n", $joins);
		$sql = "SELECT ttype.*, 
						c.*,
						$sfields, f.lang as `field.lang`, f.field as `field.field` FROM content c
				LEFT JOIN fields f ON c.contentId = f.contentId $jfields
				WHERE c.contentId=?";
		$result = Model::GetInstance() -> getRows($sql, $contentId, $ctype);
		if(!$result)
			return null;
		
		$content = Utils::StripVO($result[0]);
		$content -> template = $template -> label;
		foreach($result[0] as $key => $value) {
			if(strpos($key, 'content') !== 0 && strpos($key, 'field') !== 0) {
				$content -> $key = $value;
			}
		}
		foreach($result as $row) {
			if($row -> {'field.field'} != null && $row -> {'content.' . $row -> {'field.field'} . '.id'} != null) {
// 				if(count($fields[$row -> {'field.field'}]) == 1) {
// 					$content -> content -> {$row -> {'field.field'}}[$row -> {'field.lang'}] = $row -> {'content.' . $row -> {'field.field'} . '.' . $fields[$row -> {'field.field'}][0]};
// 				} else {
					$o = new \StdClass();
					foreach($fields[$row -> {'field.field'}] as $field) {
						$o -> $field = $row -> {'content.' . $row -> {'field.field'} . '.' . $field};
					}
					$content -> content -> {$row -> {'field.field'}}[$row -> {'field.lang'}] = $o;
// 				}
			}
		}
		return $content;
	}
	
	/**
	 * Sets the content of an item
	 * @param unknown_type $content
	 */
	public static function setContent(&$content) {
		// @todo sanitize
		
		$beuser = Authorization::GetBEUser();
		$template = TemplateFactory::getTemplate($content -> templateId);
		$id = Model::GetInstance() -> updateRow("INSERT INTO `content` (contentId, templateId, creationdate, modificationdate, createdby, modifiedby, UID, GID) VALUES
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
		
		if($id > 0) {
			$content -> contentId = $id;
			$fields = array();
			$langs = explode(',', AVAILABLELANG);
			
			Model::GetInstance() -> updateRow("UPDATE fields SET deleted=1 WHERE contentId=?", array($content -> contentId));
			
			
			foreach($template -> fields as $field) {
				$label = $field -> label;
				// Check if field is set
				if(isset($content -> content -> $label)) {
					foreach($langs as $lang) {
						// Loop over languages
						if(isset($content -> content -> $label -> $lang)) {
							switch($field -> fieldtype) {
								case 'list':
									// Special
									$i = 0;
									foreach($content -> content -> $label -> $lang as &$listitem) {
										$listitem -> parentId = $content -> contentId;
										$listitem -> idx = $i++;
										self::setContent($listitem);
										Model::GetInstance() -> updateRow("INSERT INTO plugin_list (contentId, parentId, idx) VALUES (?,?,?)", array($listitem -> contentId, $listitem -> parentId, $listitem -> idx));
									}
								default:
									$item = $content -> content -> $label -> $lang;
									if(is_scalar($item)) {
// 										$item = mysqli_real_escape_string($item);
										$fieldsql = "INSERT INTO fields (contentId, lang, field, deleted) VALUES (?,?,?, 0) ON DUPLICATE KEY UPDATE deleted=0, id=LAST_INSERT_ID(id)";
										$id = Model::GetInstance() -> updateRow($fieldsql, array($content -> contentId, $lang, $label));
										Model::GetInstance() -> updateRow("INSERT INTO plugin_{$field -> fieldtype} (fieldId, `{$field -> fieldtype}`) VALUES (?,?) ON DUPLICATE KEY UPDATE `{$field -> fieldtype}`=VALUES(`{$field -> fieldtype}`)", array($id, $item));
									}
							}
						}
					}
				}
			}
		}
		return $content;
	}
	
	protected static function setItem($item, $type) {
		switch($type) {
			case TemplateFactory::TYPE_PAGE:
				$fields = array('pageId' => 'i', 'contentId' => 'i','parentId' => 'i','label' => 's','publicationdate' => 't','expirationdate' => 't',
								'alwayspublished' => 'i','showinnavigation' => 'i','idx' => 'i','locked' => 'i', 'group' => 'i','chmod' => 'i', 'felogin'=>'s');
				$table = 'pages';
				$identifier = 'pageId';
				break;
			case TemplateFactory::TYPE_MARKER:
				$table = 'markers';
				$identifier = 'markerId';
				break;
		}
		
		// Add backticks to prevent reserved-word errors
		array_walk($keys, array(self, '_addBackticks'));
		
		// Get all table fields
		$keyvalues = implode(',',$keys);
		
		// Create a question mark for each of them
		$qa = array();
		foreach($fields as $type) {
			switch($type) {
				case 'i': //integer
				case 's': //string
					$qa[] = '?';
				case 't': //timestamp
					$qa[] = 'FROM_UNIXTIME(?)';
			}
		}
		$qmarks = implode(',', $qa);
		
		// Generate insert statement
		$sql = "INSERT INTO $table ($keyvalues)
				VALUES ($qmarks)
				ON DUPLICATE KEY UPDATE \r\n";
		
		// Remove first 2 keys ($identifier & contentId), don't update them
		$updatekeys = array_slice($keys, 2);
		foreach($updatekeys as $key) {
			$sql .= "$key = VALUES($key), \r\n";
		}
		$sql .="$identifier = LAST_INSERT_ID($identifier)";
		
		$param_arr = array();
		foreach($fields as $key => $value) {
			$param_arr[] = $page -> $key;
		}
		
		return Model::GetInstance() -> updateRow($sql, implode('',$fields), $param_arr);
		
	}
	
	protected static function _addBackticks(&$item, $key) {
		$item = "`$item`";
	}
	

}