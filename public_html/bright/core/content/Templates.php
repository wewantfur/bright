<?php
namespace bright\core\content;

/**
 * Creates and updates templates
 * @author Ids
 *
 */
use bright\core\utils\StringUtils;

use bright\core\model\vo\Template;

use bright\core\auth\Authorization;

use bright\core\exceptions\Exception;

use bright\core\exceptions\TemplateException;

use bright\core\exceptions\TypeException;

use bright\core\model\vo\TemplateField;

use bright\core\Utils;

use bright\core\model\Model;

class Templates {
	
	const TYPE_PAGE = 1;
	const TYPE_LIST = 2;
	const TYPE_EVENT = 3;
	const TYPE_MARKER = 4;
	const TYPE_USER = 5;
	
	
	/**
	 * @todo implement
	 * @param unknown_type $templateId
	 */
	public static function deleteTemplate($templateId) {
		throw new Exception('Exception::NOT_IMPLEMENTED',Exception::NOT_IMPLEMENTED);
	}
	
	/**
	 * Gets a single template, with all the templatefields
	 * @param int $templateId
	 * @return \bright\core\model\vo\Template The template
	 */
	public static function getTemplate($templateId) {
		return self::_getTemplate($templateId, 'templateId');
	}
	
	/**
	 * Returns all the templates
	 */
	public static function getTemplates() {
		$sql = "SELECT * FROM templates ORDER BY `displaylabel`";
		return Model::getInstance() -> getRows($sql, null, '\bright\core\model\vo\Template');
	}
	
	public static function getTemplateByContentId($contentId) {
		$sql = "SELECT t.*,
				tf.label as `fieldlabel`,
				tf.displaylabel as `fielddisplaylabel`,
				tf.`index`,
				tf.fieldtype,
				tf.data
				FROM templates t
				LEFT JOIN templatefields tf ON t.templateId = tf.templateId
				INNER JOIN content c ON c.templateId=t.templateId AND c.contentId=?
				ORDER BY tf.`index`";
		$fields = Model::getInstance() -> getRows($sql, array($contentId), '\bright\core\model\vo\Template');
		return self::_createTemplate($fields);
	}
	
	public static function getTemplateByLabel($label) {
		return self::_getTemplate($label, 'label');
	}
	
	public static function setTemplate(Template $template) {
		$au = Authorization::getBEUser();
		
// 		$tprops = get_class_vars('\bright\core\model\vo\Template');
// 		$template -> label = StringUtils::sanitizeLabel($template -> label);
		
// 		$sql = "INSERT INTO templates ";
// 		$fields = array_keys($tprops);
// 		$sql .= '(`' . join('`, `', $fields) . '`) 
// 				VALUES (' . str_repeat('?,', count($fields)-1) . '?)
// 				ON DUPLICATE KEY UPDATE ';
// 		$varr = array();
// 		foreach($tprops as $key => $type) {
// 			$varr[] = $template -> $key;
// 			if($key != 'templateId') {
// 				$sql .= "`$key` = VALUES(`$key`),\r\n";
// 			}
// 		}
// 		$sql .= 'templateId = LAST_INSERT_ID(templateId)';
		
// 		try {
// 			$id = Model::getInstance() -> updateRow($sql, $varr);
// 		} catch(\Exception $e) {
// 			Utils::log($e-> getCode(), $e -> getMessage());//$e -> getTraceAsString());
// 		}

		try {
			$id = Model::getInstance() -> updateObject('templates', $template, 'templateId');
		} catch(Exception $e) {
			throw new TemplateException('TemplateException::CANNOT_SAVE_TEMPLATE', TemplateException::CANNOT_SAVE_TEMPLATE);
		}
		
		if($id > 0) {
			if(isset($template -> fields)) {
				$i=0;
				foreach($template -> fields as &$tfield) {
					$tfield -> templateId = $id;
					$tfield -> index = $i++;
				}
				Utils::log($template);
				Model::getInstance() -> updateObject('templatefields', $template -> fields, 'fieldId');
			}
		}
		
// 		throw new Exception('Exception::NOT_IMPLEMENTED',Exception::NOT_IMPLEMENTED);
	}
	
	private static function _createTemplate($fields) {
		if($fields) {
			$template = clone $fields[0];
	
			switch($template -> type) {
				case Templates::TYPE_PAGE:
					$template -> fields[] = (object) array('label' => 'title', 'displaylabel' => 'Title', 'type' => 'string', 'data' => (object) array('required' => true));
			}
				
			$tnames = array('templateId','label','displaylabel','icon','type','parser','enabled','maxchildren','allowedparents','allowedchildren','groups','index');
			$fnames = array('fieldlabel', 'fielddisplaylabel','index','fieldtype','data');
				
			foreach($fnames as $name)
				unset($template -> $name);
	
			foreach($fields as $field) {
	
				foreach($tnames as $name) {
					unset($field -> $name);
				}
	
				if($field -> fieldtype != null) {
					$field -> label = $field -> fieldlabel;
					$field -> displaylabel = $field -> fielddisplaylabel;
					$field -> type = $field -> fieldtype;
					if($field -> data != null)
						$field -> data = json_decode($field -> data);
						
					unset($field -> fieldtype);
					unset($field -> fieldlabel);
					unset($field -> fielddisplaylabel);
					$template -> fields[] = $field;
				}
			}
				
			return $template;
		}
		return null;
	}
	
	private static function _getTemplate($identifier, $field) {
		$field = filter_var($field, FILTER_SANITIZE_STRING);
		switch($field) {
			case 'templateId':
				$identifier = filter_var($identifier, FILTER_VALIDATE_INT);
				break;
			case 'label':
				$identifier = filter_var($identifier, FILTER_SANITIZE_STRING);
				break;
			default:
				throw new TypeException($msg, $code, $previous, 'templateId or label', $field);
				
		}
		if($identifier !== false && $field !== false) {
			$sql = "SELECT t.*,
			tf.fieldId as `field.fieldId`,
			tf.label as `field.label`,
			tf.displaylabel as `field.displaylabel`,
			tf.fieldtype as `field.fieldtype`,
			tf.data as `field.data`
			FROM templates t
			LEFT JOIN templatefields tf ON tf.templateId = t.templateId
			WHERE t.$field=?
			ORDER BY tf.`index` ASC";
			$results =  Model::getInstance() -> getRows($sql, array($identifier), '\bright\core\model\vo\Template');
			if($results) {
				$tpl = Utils::stripVO($results[0]);
				$tpl -> fields = array();
		
				switch($tpl -> type) {
					case self::TYPE_PAGE:
					case self::TYPE_MARKER:
					case self::TYPE_EVENT:
						// Add default title field
						$tpl -> fields[] = new TemplateField('title', 'Title', 'string');
						break;
				}
		
				foreach($results as $result) {
					$field = new TemplateField(	$result -> {'field.label'},
					$result -> {'field.displaylabel'},
					$result -> {'field.fieldtype'});
					if($result -> {'field.data'} != null) {
						$field -> data = json_decode($result -> {'field.data'});
					}
					$tpl -> fields[] = $field;
				}
				return $tpl;
			}
		}
		throw new TemplateException('TemplateException::TEMPLATE_NOT_FOUND', TemplateException::TEMPLATE_NOT_FOUND);
	}
}