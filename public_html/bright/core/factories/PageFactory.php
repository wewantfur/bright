<?php
namespace bright\core\factories;

use bright\core\utils\Logger;

use bright\core\model\Model;

use bright\core\model\vo\Page;

use bright\core\interfaces\IContent;

use bright\core\factories\ContentFactory;

class PageFactory extends ContentFactory {
	
	public static function setContent($content) {
		$content = parent::setContent($content);
		
		
		$pagecolumns =  ['pageId', 'contentId', 'parentId', 'label', 'publicationdate', 'expirationdate', 'alwayspublished', 'showinnavigation', 'idx', 'felogin', 'lft', 'rgt'];
		$sqlcolumns = implode(',', $pagecolumns);
		
		$data = [];
		
		foreach($pagecolumns as $prop) {
			$data[] = $content -> $prop; 
		}
		
		if($content -> parentId == 0)
			$content -> parentId = null;
		
		
		$sql = "INSERT INTO pages ($sqlcolumns) VALUES
									(?,?,?,?,?,?,?,?,?,?,?,?)
									ON DUPLICATE KEY UPDATE
									parentId = VALUES(parentId),
									label = VALUES(label),
									publicationdate = VALUES(publicationdate),
									expirationdate = VALUES(expirationdate),
									alwayspublished = VALUES(alwayspublished),
									showinnavigation = VALUES(showinnavigation),
									idx = VALUES(idx),
									felogin = VALUES(felogin),
									lft = VALUES(lft),
									rgt = VALUES(rgt)";
		
		$pid = Model::getInstance() -> updateRow($sql, $data);
		$content -> pageId = $pid;
		
		return $content;
		
	}
}