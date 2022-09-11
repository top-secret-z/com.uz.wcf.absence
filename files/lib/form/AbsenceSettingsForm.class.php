<?php
namespace wcf\form;
use wcf\data\user\User;
use wcf\data\user\UserAction;
use wcf\system\cache\builder\AbsentMembersBoxCacheBuilder;
use wcf\system\cache\runtime\UserRuntimeCache;
use wcf\system\exception\UserInputException;
use wcf\system\menu\user\UserMenu;
use wcf\system\user\activity\event\UserActivityEventHandler;
use wcf\system\WCF;
use wcf\util\DateUtil;
use wcf\util\StringUtil;

/**
 * Shows the absence setting form.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.absence
 */
class AbsenceSettingsForm extends AbstractForm {
	/**
	 * @inheritDoc
	 */
	public $loginRequired = true;
	
	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['user.profile.canUseAbsence'];
	
	/**
	 * absence data
	 */
	public $absentFrom = '';
	public $absentFromObj = false;
	public $absentReason = '';
	public $absentTo = '';
	public $absentToObj = false;
	public $absentRepID = null;
	public $absentRepName = '';
	
	/**
	 * affected user
	 */
	public $user = null;
	
	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();
		
		$this->user = WCF::getUser();
		
		// set times and reason
		$this->absentFrom = '';
		if ($this->user->absentFrom) {
			$dateTime = DateUtil::getDateTimeByTimestamp($this->user->absentFrom);
			$dateTime->setTimezone($this->user->getTimeZone());
			$this->absentFrom = $dateTime->format('c');
		}
		$this->absentTo = '';
		if ($this->user->absentTo) {
			$dateTime = DateUtil::getDateTimeByTimestamp($this->user->absentTo);
			$dateTime->setTimezone($this->user->getTimeZone());
			$this->absentTo = $dateTime->format('c');
		}
		
		$this->absentReason = $this->user->absentReason;
		
		// set rep
		$this->absentRepName = '';
		$this->absentRepID = $this->user->absentRepID;
		if ($this->absentRepID) {
			$user = UserRuntimeCache::getInstance()->getObject($this->absentRepID);
			if ($user !== null) {
				$this->absentRepName = $user->username;
			}
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['absentFrom'])) $this->absentFrom = $_POST['absentFrom'];
		if (isset($_POST['absentReason'])) $this->absentReason = StringUtil::trim($_POST['absentReason']);
		if (isset($_POST['absentTo'])) $this->absentTo = $_POST['absentTo'];
		
		$this->absentFromObj = \DateTime::createFromFormat('Y-m-d\TH:i:sP', $this->absentFrom);
		$this->absentToObj = \DateTime::createFromFormat('Y-m-d\TH:i:sP', $this->absentTo);
		
		if (isset($_POST['absentRepName'])) $this->absentRepName = StringUtil::trim($_POST['absentRepName']);
	}
	
	/**
	 * @inheritDoc
	 */
	public function validate() {
		parent::validate();
		
		// either both or none
		if (!empty($this->absentFrom) && empty($this->absentTo)) {
			throw new UserInputException('absentTo', 'empty');
		}
		if (empty($this->absentFrom) && !empty($this->absentTo)) {
			throw new UserInputException('absentFrom', 'empty');
		}
		
		// only if times are set
		if (!empty($this->absentFrom)) {
			// must be valid
			if ($this->absentFromObj === false) throw new UserInputException('absentFrom', 'invalid');
			if ($this->absentToObj === false) throw new UserInputException('absentTo', 'invalid');
			
			// from before to
			if ($this->absentToObj->getTimestamp() < $this->absentFromObj->getTimestamp()) {
				throw new UserInputException('absentTo', 'toBeforeFrom');
			}
			
			// to in past
			if ($this->absentToObj->getTimestamp() < TIME_NOW) {
				throw new UserInputException('absentTo', 'inPast');
			}
		}
		
		// reason max. 255 chars
		if (!empty($this->absentReason) && mb_strlen($this->absentReason) > 255) {
			throw new UserInputException('absentReason', 'tooLong');
		}
		
		// representation
		if (!ABSENCE_REP_ENABLE || empty($this->absentRepName) || !WCF::getSession()->getPermission('user.profile.canUseAbsenceRep')) {
			$this->absentRepName = '';
			$this->absentRepID = null;
		}
		
		if (!empty($this->absentRepName)) {
			$user = User::getUserByUsername($this->absentRepName);
			if (!$user->userID) {
				throw new UserInputException('absentRepName', 'notFound');
			}
			if ($user->userID == WCF::getUser()->userID) {
				throw new UserInputException('absentRepName', 'notSelf');
			}
			
			$this->absentRepID = $user->userID;
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
				'user' => $this->user,
				'absentFrom' => $this->absentFrom,
				'absentReason' => $this->absentReason,
				'absentTo' => $this->absentTo,
				'absentRepName' => $this->absentRepName
		]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function show() {
		// set active tab
		UserMenu::getInstance()->setActiveMenuItem('wcf.user.menu.settings.absence');
		
		parent::show();
	}
	
	/**
	 * @inheritDoc
	 */
	public function save() {
		parent::save();
		
		$from = $this->absentFromObj ? $this->absentFromObj->getTimestamp() : 0;
		$to = $this->absentToObj ? $this->absentToObj->getTimestamp() : 0;
		
		$data = array_merge($this->additionalFields, [
				'absentFrom' => $from,
				'absentTo' => $to,
				'absentReason' => $this->absentReason,
				'absentAuto' => 0,
				'absentRepID' => $this->absentRepID,
		]);
		
		$userAction = new UserAction([$this->user], 'update', [
				'data' => $data
		]);
		$userAction->executeAction();
		
		// recent activity
		if (MODULE_ABSENCE_ACTIVITY) {
			if ($from > 0) {
				// delete first then creeate
				UserActivityEventHandler::getInstance()->removeEvents('com.uz.wcf.absence.recentActivityEvent.submit', [$this->user->userID]);
				UserActivityEventHandler::getInstance()->fireEvent('com.uz.wcf.absence.recentActivityEvent.submit', $this->user->userID);
			}
			else {
				UserActivityEventHandler::getInstance()->removeEvents('com.uz.wcf.absence.recentActivityEvent.submit', [$this->user->userID]);
			}
		}
		
		// reset box cache
		AbsentMembersBoxCacheBuilder::getInstance()->reset();
		
		$this->saved();
		
		// show success message
		WCF::getTPL()->assign('success', true);
		
		// reset variables
		$this->absentFrom = '';
		if ($from) {
			$dateTime = DateUtil::getDateTimeByTimestamp($from);
			$dateTime->setTimezone($this->user->getTimeZone());
			$this->absentFrom = $dateTime->format('c');
		}
		
		$this->absentTo = '';
		if ($to) {
			$dateTime = DateUtil::getDateTimeByTimestamp($to);
			$dateTime->setTimezone($this->user->getTimeZone());
			$this->absentTo = $dateTime->format('c');
		}
	}
}
