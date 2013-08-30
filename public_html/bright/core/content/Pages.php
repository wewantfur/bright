<?php
namespace bright\core\content;

use bright\core\exceptions\PageException;

use bright\core\auth\Authorization;

use bright\core\Utils;

use bright\core\model\Model;

use bright\core\model\vo\Page;

class Pages extends Content {
	
	private static $_tree;
	
	public static function getChildren($parentId) {
		$sql = "SELECT p.*, (SELECT count(pageId) FROM pages pc WHERE parentId=p.pageId) AS numchildren, t.icon 
				FROM pages p 
				LEFT JOIN templates t ON p.templateId = t.templateId
				WHERE parentId=$parentId";
		return Model::getInstance() -> getRows($sql, array($parentId), '\bright\core\model\vo\Page');//$this -> db -> getRows($sql);
	}
	
	public static function getHomepage() {
		$sql = "SELECT p.*, t.label as template FROM pages p
				INNER JOIN content c ON p.contentId = c.contentId 
				LEFT JOIN templates t ON c.templateId = t.templateId
				WHERE parentId = 0";
		$page = Model::getInstance() -> getRow($sql, null, '\bright\core\model\vo\Page');
		if($page) { 
			$page = self::getContent($page -> contentId);
		}
		return $page;
	}
	
	/**
	 * Gets the root ID for the entire tree (most of the time '1')
	 * @return int
	 */
	public static function getBERoot() {
		return Model::getInstance() -> getField("SELECT p.pageId FROM pages p WHERE lft=1");
	}
	
	/**
	 * Returns all the pages
	 */
	public static function getPages($astree = false) {
		$result = Model::getInstance() -> getRow('SELECT lft, rgt FROM pages WHERE lft=1');
		$sql = "SELECT p.*, c.*, t.icon, (SELECT count(pageId) FROM pages pc WHERE parentId=p.pageId) AS numchildren
				FROM pages p
				INNER JOIN content c ON c.contentId = p.contentId
				LEFT JOIN templates t ON c.templateId = t.templateId 
				WHERE lft BETWEEN ? AND ?
				ORDER BY lft ASC";
		
		$pages = Model::getInstance() -> getRows($sql, array($result->lft, $result->rgt), '\bright\core\model\vo\Page');
		if($astree) {
			$ap = array_values($pages);
			$tree = Utils::createTree($ap);
// 			$tree= null;
			foreach($pages as &$page) {
				$page -> parent = null;
			}
			return (object) array('tree' => $tree, 'list' => $pages);
		}
		return $pages;
	}
	
	public static function getPagesForBE() {
		$bu = Authorization::getBEUser();

		if(count($bu -> page_mountpoints) == 0)
			throw new PageException('NO_MOUNTPOINTS', PageException::NO_MOUNTPOINTS);
		
		$pids = implode(',', $bu -> page_mountpoints);
		$mountpoints = Model::getInstance() -> getRows("SELECT lft, rgt FROM pages WHERE pageId IN ($pids)");
		
		$params = array();
		$wheres = array();
		foreach($mountpoints as $mountpoint) {
			$wheres[] = '(lft BETWEEN ? and ?)';
			$params[] = $mountpoint -> lft;
			$params[] = $mountpoint -> rgt;
		}
		
		$where = implode(' OR ', $wheres);
		
		$sql = "SELECT p.*, c.*, t.icon, (SELECT count(pageId) FROM pages pc WHERE parentId=p.pageId) AS numchildren
				FROM pages p
				INNER JOIN content c ON c.contentId = p.contentId
				LEFT JOIN templates t ON c.templateId = t.templateId
				WHERE $where
				ORDER BY lft ASC";
		
		$pages = Model::getInstance() -> getRows($sql, $params, '\bright\core\model\vo\Page');
		
		$ap = array_values($pages);
		$tree = Utils::createTree($ap);
		// 			$tree= null;
		foreach($pages as &$page) {
			$page -> parent = null;
		}
		return (object) array('tree' => $tree, 'list' => $pages);
		
	}
	
	public static function getPage($id) {
		$sql = "SELECT p.*, c.*, t.icon, (SELECT count(pageId) FROM pages pc WHERE parentId=p.pageId) AS numchildren
				FROM pages p
				INNER JOIN content c ON p.contentId = c.contentId
				LEFT JOIN templates t ON c.templateId = t.templateId
				WHERE p.pageId=?";
		$page = Model::getInstance() -> getRow($sql, array($id), '\bright\core\model\vo\Page');
		$cclass = new Content();
		$content = $cclass -> getContent($page -> contentId);
		return $content;
	}
	
	/**
	 * Gets the path of the given page
	 * @todo implement
	 * @param int $id
	 * @return string
	 */
	public static function getPath($id) {
		return '';
	}
	
	public static function movePages($pages) {
		
	}
	
	public static function moveToTrash($pages) {
		
	}
	
	public static function setPage($page) {
		self::setContent($page); 
		
		$fields = array('pageId' => 'i','contentId' => 'i','parentId' => 'i','label' => 's','publicationdate' => 's','expirationdate' => 's', 
						'alwayspublished' => 'i','showinnavigation' => 'i','index' => 'i', 'felogin'=>'s');
		$keys = array_keys($fields);
		// Add backticks to prevent reserved-word errors
		array_walk($keys, array('\bright\core\content\Content', '_addBackticks'));
		
		// Get all table fields
		$keyvalues = implode(',',$keys);
		
		// Create a question mark for each of them
		$qmarks = str_repeat('?,', count($keys));
		// Remove last comma
		$qmarks = substr($qmarks, 0, -1);
		
		$sql = "INSERT INTO pages ($keyvalues) 
				VALUES ($qmarks)
				ON DUPLICATE KEY UPDATE \r\n";
		
		// Remove first 2 keys (pageId & contentId), don't update them
		$updatekeys = array_slice($keys, 2);
		foreach($updatekeys as $key) {
			$sql .= "$key = VALUES($key), \r\n";
		}
		$sql .="pageId = LAST_INSERT_ID(pageId)";
		
		$param_arr = array($sql, implode($fields));
		foreach($fields as $key => $value) {
			$param_arr[] = $page -> $key;
		}
		
		$page = Model::getInstance() -> updateRow($sql, $param_arr);
		//call_user_func_array(array($this -> db, 'insertPrepared' ), $param_arr);
		
		return self::getPages();
	}

	private function _createTree() {

		$return = array_shift(self::_tree);

		if ($return && $return -> lft + 1 != $return -> rgt) {
			foreach (self::_tree as $key => $result) {
				// 				$rgt = 0;
				if ($result -> lft > $return -> rgt) //not a child
					break;
				if (@$rgt > $result -> lft) //not a top-level child
					continue;
				
				$return -> children[] = self::_createTree();
				foreach (self::$_tree as $child_key => $child) {
					if ($child -> rgt < $result -> rgt)
						unset(self::$_tree[$child_key]);
				}
				$rgt = $result -> rgt;
				unset(self::$_tree[$key]);
			}
		}

    	return $return;

	}
}