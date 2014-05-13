<?php
namespace bright\core\factories;

use bright\core\exceptions\TemplateException;

use bright\core\exceptions\Exception;

use bright\core\exceptions\TypeException;
use bright\core\utils\Logger;

use bright\core\auth\Authorization;

use bright\core\model\vo\Template;

use bright\core\model\vo\TemplateField;

use bright\core\Utils;

use bright\core\model\Model;

class TemplateFactory {
	const TYPE_PAGE = 1;
	const TYPE_LIST = 2;
	const TYPE_EVENT = 3;
	const TYPE_MARKER = 4;
	const TYPE_USER = 5;

	/**
	 * Deletes a template
	 * @param int $templateId
	 * @throws Exception
	 * @throws TemplateException Throws a TemplateException when the template is still in use
	 * @return The number of deleted templates
	 */
	public static function DeleteTemplate($templateId) {
		$templateId = filter_var($templateId, FILTER_VALIDATE_INT);
		if(!$templateId)
			throw new Exception('Exception::INCORRECT_PARAM_INT', Exception::INCORRECT_PARAM_INT);
		
		if(AbstractContentFactory::contentOfTemplateExists($templateId))
			throw new TemplateException('TemplateException::TEMPLATE_IN_USE', TemplateException::TEMPLATE_IN_USE);
		
		return Model::GetInstance() -> deleteRow("DELETE FROM templates WHERE templateId=?", $templateId);
	}
	
	/**
	 * Deletes a template by label
	 * @param unknown_type $label
	 */
	public static function DeleteTemplateByLabel($label) {
		$tpl = self::_GetTemplate($label, 'label');
		return self::DeleteTemplate($tpl -> templateId);
	}

	/**
	 * Gets a single template, with all the templatefields
	 * @param int $templateId
	 * @return \bright\core\model\vo\Template The template
	 */
	public static function GetTemplate($templateId) {
		return self::_GetTemplate($templateId, 'templateId');
	}

	/**
	 * Returns all the templates
	 */
	public static function GetTemplates() {
		$sql = "SELECT * FROM templates ORDER BY displaylabel";
		return Model::GetInstance() -> getRows($sql, null, '\bright\core\model\vo\Template');
	}

	/**
	 * Gets a template of the given contentitem
	 * @param int $contentId The id of the contentitem
	 * @return \bright\core\model\vo\Template
	 */
	public static function GetTemplateByContentId($contentId) {
		$sql = "SELECT t.*,
		tf.label as fieldlabel,
		tf.displaylabel as fielddisplaylabel,
		tf.idx,
		tf.fieldtype,
		tf.data
		FROM templates t
		LEFT JOIN templatefields tf ON t.templateId = tf.templateId
		INNER JOIN content c ON c.templateId=t.templateId AND c.contentId=?
		ORDER BY tf.idx";
		
		$fields = Model::GetInstance() -> getRows($sql, array($contentId), '\bright\core\model\vo\Template');
		
		return self::_CreateTemplate($fields);
	}

	/**
	 * Gets a template by it's label
	 * @param string $label The label of the template
	 * @return \bright\core\model\vo\Template
	 */
	public static function GetTemplateByLabel($label) {
		return self::_GetTemplate($label, 'label');
	}

	public static function SetTemplate(Template $template) {
		$au = Authorization::GetBEUser();

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
		// 			$id = Model::GetInstance() -> updateRow($sql, $varr);
		// 		} catch(\Exception $e) {
		// 			Utils::log($e-> getCode(), $e -> getMessage());//$e -> getTraceAsString());
		// 		}

		try {
			$id = Model::GetInstance() -> updateObject('templates', $template, 'templateId');
		
		} catch(Exception $e) {
			throw new TemplateException('TemplateException::CANNOT_SAVE_TEMPLATE', TemplateException::CANNOT_SAVE_TEMPLATE);
		}

		if($id > 0) {
			if(isset($template -> fields)) {
				$i=0;
				foreach($template -> fields as &$tfield) {
					$tfield -> templateId = $id;
					$tfield -> idx = $i++;
				}
				Model::GetInstance() -> updateObject('templatefields', $template -> fields, 'fieldId');
			}
		}
	}

	private static function _CreateTemplate($fields) {
		if($fields) {
			$template = clone $fields[0];

			switch($template -> type) {
				case TemplateFactory::TYPE_PAGE:
					$template -> fields[] = (object) array('label' => 'title', 'displaylabel' => 'Title', 'type' => 'string', 'data' => (object) array('required' => true));
			}

			$tnames = array('templateId','label','displaylabel','icon','type','parser','enabled','maxchildren','allowedparents','allowedchildren','groups','idx');
			$fnames = array('fieldlabel', 'fielddisplaylabel','idx','fieldtype','data');

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

	/**
	 * Gets the template identified by the field
	 * @param mixed $identifier
	 * @param string $field
	 * @throws TypeException
	 * @throws TemplateException
	 * @return \bright\core\model\VO\Template
	 */
	private static function _GetTemplate($identifier, $field) {
		$field = filter_var($field, FILTER_SANITIZE_STRING);
		switch($field) {
			case 'templateId':
				$identifier = filter_var($identifier, FILTER_VALIDATE_INT);
				break;
			case 'label':
				$identifier = filter_var($identifier, FILTER_SANITIZE_STRING);
				break;
			default:
				throw new TypeException(TypeException::INCORRECT_PARAM_INT, TypeException::INCORRECT_PARAM_INT, null, 'templateId or label', $field);

		}
		if($identifier == false || $field == false)
			throw new TemplateException('TemplateException::TEMPLATE_NOT_FOUND', TemplateException::TEMPLATE_NOT_FOUND);
			
		$sql = "SELECT t.*,
				tf.fieldId as `field.fieldId`,
				tf.label as `field.label`,
				tf.displaylabel as `field.displaylabel`,
				tf.fieldtype as `field.fieldtype`,
				tf.data as `field.data`
				FROM templates t
				LEFT JOIN templatefields tf ON tf.templateId = t.templateId
				WHERE t.$field=?
				ORDER BY tf.idx ASC";
		
		$results =  Model::GetInstance() -> getRows($sql, array($identifier), '\bright\core\model\vo\Template');
		if($results) {
			$tpl = Utils::StripVO($results[0]);
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
}