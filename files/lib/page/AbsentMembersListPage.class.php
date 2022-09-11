<?php
namespace wcf\page;
use wcf\data\user\User;
use wcf\data\user\UserProfileList;
use wcf\system\request\LinkHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * Shows page which lists all users who are absent.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.absence
 */
class AbsentMembersListPage extends SortablePage {
	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['user.profile.canViewAbsence'];
	
	/**
	 * @inheritDoc
	 */
	public $neededModules = ['MODULE_ABSENCE'];
	
	/**
	 * @inheritDoc
	 */
	public $itemsPerPage = MEMBERS_LIST_USERS_PER_PAGE;
	
	/**
	 * @inheritDoc
	 */
	public $defaultSortField = 'username';
	
	/**
	 * @inheritDoc
	 */
	public $defaultSortOrder = 'ASC';
	
	/**
	 * @inheritDoc
	 */
	public $validSortFields = ['username', 'registrationDate', 'activityPoints', 'likesReceived', 'lastActivityTime', 'absentFrom', 'absentTo'];
	
	/**
	 * available letters
	 */
	public static $availableLetters = '#ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	
	/**
	 * letter
	 */
	public $letter = '';
	
	/**
	 * @inheritDoc
	 */
	public $objectListClassName = UserProfileList::class;
	
	public function readParameters() {
		parent::readParameters();
		
		// letter
		if (isset($_REQUEST['letter']) && mb_strlen($_REQUEST['letter']) == 1 && mb_strpos(self::$availableLetters, $_REQUEST['letter']) !== false) {
			$this->letter = $_REQUEST['letter'];
		}
		
		if (!empty($_POST)) {
			$parameters = [];
			$url = http_build_query($_POST, '', '&');
			HeaderUtil::redirect(LinkHandler::getInstance()->getLink('AbsentMembersList', $parameters, $url));
			exit;
		}
	}
	
	/**
	 * @inheritDoc
	 */
	protected function initObjectList() {
		parent::initObjectList();
		
		$this->objectList->getConditionBuilder()->add('user_table.absentFrom < ?', [TIME_NOW]);
		$this->objectList->getConditionBuilder()->add('user_table.absentTo > ?', [TIME_NOW]);
		if (ABSENCE_DISPLAY_HIDEAUTO) {
			$this->objectList->getConditionBuilder()->add('user_table.absentAuto = ?', [0]);
		}
		
		if (!empty($this->letter)) {
			if ($this->letter == '#') {
				$this->objectList->getConditionBuilder()->add("SUBSTRING(username,1,1) IN ('0', '1', '2', '3', '4', '5', '6', '7', '8', '9')");
			}
			else {
				$this->objectList->getConditionBuilder()->add("username LIKE ?", [$this->letter.'%']);
			}
		}
	}
	
	/**
	 * @inheritDoc
	 */
	protected function readObjects() {
		parent::readObjects();
		
		$userIDs = [];
		foreach ($this->objectList as $user) {
			$userIDs[] = $user->userID;
		}
		
		if (!empty($userIDs)) {
			UserStorageHandler::getInstance()->loadStorage($userIDs);
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
				'letters' => str_split(self::$availableLetters),
				'letter' => $this->letter,
				'validSortFields' => $this->validSortFields,
		]);
		
		if (count($this->objectList) === 0) {
			@header('HTTP/1.0 404 Not Found');
		}
	}
}
