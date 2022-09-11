<?php
namespace wcf\system\box;
use wcf\data\user\AbsenceUserProfileList;
use wcf\system\event\EventHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Shows absent members.
 *
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.absence
 */
class AbsentMembersBoxController extends AbstractDatabaseObjectListBoxController {
	/**
	 * @inheritDoc
	 */
	protected static $supportedPositions = ['footerBoxes', 'sidebarLeft', 'sidebarRight'];
	
	/**
	 * @inheritDoc
	 */
	public $validSortFields = [
			'username',
			'absentFrom',
			'absentTo'
	];
	
	/**
	 * @inheritDoc
	 */
	protected $sortFieldLanguageItemPrefix = 'wcf.user.absence.box.sort';
	
	/**
	 * @inheritDoc
	 */
	public function getLink() {
		return LinkHandler::getInstance()->getLink('AbsentMembersList');
	}
	
	/**
	 * @inheritDoc
	 */
	public function hasLink() {
		return true;
	}
	
	/**
	 * @inheritDoc
	 */
	public function hasContent() {
		if (!MODULE_ABSENCE || !WCF::getSession()->getPermission('user.profile.canViewAbsence')) {
			return false;
		}
		
		return parent::hasContent();
	}
	
	/**
	 * @inheritDoc
	 */
	protected function getObjectList() {
		return new AbsenceUserProfileList();
	}
	
	/**
	 * @inheritDoc
	 */
	protected function readObjects() {
		EventHandler::getInstance()->fireAction($this, 'readObjects');
		
		if ($this->box->sortOrder && $this->box->sortField && isset($this->realSortFields[$this->box->sortField])) {
			$this->objectList->sqlOrderBy = implode(' '.$this->box->sortOrder.',', $this->realSortFields[$this->box->sortField]).' '.$this->box->sortOrder;
		}
		
		parent::readObjects();
	}
	
	/**
	 * @inheritDoc
	 */
	protected function getTemplate() {
		$templateName = 'boxAbsentMembersFooter';
		if ($this->box->position === 'sidebarLeft' || $this->box->position === 'sidebarRight') {
			$templateName = 'boxAbsentMembers';
		}
		
		// check for more than limit
		$total = count($this->objectList);
		
		return WCF::getTPL()->fetch($templateName, 'wcf', [
				'userProfiles' => $this->objectList,
				'total' => $total
		]);
	}
}
