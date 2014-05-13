<?php
namespace bright\core\factories;

use bright\core\auth\Authorization;
use bright\core\model\Model;
use bright\core\model\vo\Page;
use bright\core\Utils;
use bright\core\utils\Logger;

class PageFactory extends AbstractContentFactory {

    /**
     * Gets the item by it's contentId
     * @param int $id
     * @return int|null
     */
    public static function GetContentById($id) {
        return self::_GetContentById($id, 'contentId');
    }

    /**
     * Gets the item by it's pageId
     * @param $id
     * @return mixed
     */
    public static function GetContentByPageId($id) {
        return self::_GetContentById($id, 'pageId');
    }
	
	public static function SetContent($content) {
		$content = parent::SetContent($content);
		
		$pageColumns =  ['pageId', 'contentId', 'parentId', 'label', 'publicationdate', 'expirationdate', 'alwayspublished', 'showinnavigation', 'idx', 'felogin', 'lft', 'rgt'];
		$sqlColumns = implode(',', $pageColumns);
		
		$data = [];
		
		foreach($pageColumns as $prop) {
			$data[] = $content -> $prop; 
		}
		
		if($content -> parentId == 0)
			$content -> parentId = null;

        if(!$content -> parentId) {
            $content = self::_SetContentPosition($content);
        }

		
		$sql = "INSERT INTO pages ($sqlColumns) VALUES
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
		
		$pid = Model::GetInstance() -> updateRow($sql, $data);
		$content -> pageId = $pid;
		
		return $content;
		
	}
	
	public static function GetPagesForBE() {
		$mountPoints = self::_GetMountPoints();
	
		$params = array();
		$wheres = array();
		foreach($mountPoints as $mountPoint) {
			$wheres[] = '(lft BETWEEN ? and ?)';
			$params[] = $mountPoint -> lft;
			$params[] = $mountPoint -> rgt;
		}
	
		$where = implode(' OR ', $wheres);
	
		$sql = "SELECT p.*,
				c.*,
				t.icon,
				(SELECT count(pageId)
				FROM pages pc WHERE parentId=p.pageId) AS numchildren
				FROM pages p
				INNER JOIN content c ON c.contentId = p.contentId
				LEFT JOIN templates t ON c.templateId = t.templateId
				WHERE $where
				ORDER BY lft ASC";
	
		$pages = Model::GetInstance() -> getRows($sql, $params, '\bright\core\model\vo\Page');
	//Logger::log($sql, $pages);
		$ap = array_values($pages);
		$tree = Utils::createTree($ap);
		// 			$tree= null;
		foreach($pages as &$page) {
			$page -> parent = null;
		}
		return (object) array('tree' => $tree, 'list' => $pages);
	
	}
	
	private static function _GetMountPoints() {
		$bu = Authorization::getBEUser();
		
		if(count($bu -> page_mountpoints) == 0)
			throw new PageException('NO_MOUNTPOINTS', PageException::NO_MOUNTPOINTS);
		
		$pids = implode(',', $bu -> page_mountpoints);
		$mountPoints = Model::GetInstance() -> getRows("SELECT lft, rgt FROM pages WHERE lft IN ($pids)");
		
		return $mountPoints;
	}

    private static function _GetContentById($id, $identifier) {
        if(!$id)
            return new Page();
        // Get the type specific fields
        $sql = "SELECT p.*, c.*, t.icon, (SELECT count(pageId) FROM pages pc WHERE parentId=p.pageId) AS numchildren
				FROM pages p
				INNER JOIN content c ON p.contentId = c.contentId
				LEFT JOIN templates t ON c.templateId = t.templateId
				WHERE p.{$identifier}=?";

        $page = Model::GetInstance() -> getRow($sql, array($id), '\bright\core\model\vo\Page');

        // Get the content
        $page = parent::GetContent($page);
        return $page;
    }

    private static function _SetContentPosition($content) {
        $beUser = Authorization::GetBEUser();

        // Check if we are a super user, if so, set new root node
        // and add it to the mountpoints
        if(Authorization::UserIsInGroup(Authorization::GR_SU)) {
            $content = self::_SetContentAsRoot($content);
            Authorization::AddMountPoint($content -> lft);

        }

        return $content;
    }

    private static function _SetContentAsRoot($content) {
        $mRgt = Model::GetInstance() -> getField("SELECT MAX(rgt) FROM pages");

        $content -> lft = $mRgt + 1;
        $content -> rgt = $mRgt + 2;

        return $content;
    }

    /**
     * Gets the correct lft value for the given position
     * @param $parentId The parent Id
     * @param $idx The index of the new page
     * @return array|mixed|string
     */
    private static function _GetLft($parentId, $idx) {
        $col = ($idx < 0) ? 'rgt' : 'lft';
        $sql = "SELECT $col FROM pages WHERE pageId=?";

        return Model::GetInstance() -> getField($sql, $parentId);

    }
}