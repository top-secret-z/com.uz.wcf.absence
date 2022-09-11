<?php
namespace wcf\data\user;
use wcf\data\user\UserAction;
use wcf\system\cache\builder\AbsentMembersBoxCacheBuilder;
use wcf\system\WCF;

/**
 * Actions for absences.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.absence
 */
class AbsenceAction extends UserAction {
	/**
	 * Absent user.
	 */
	protected $absentUser = null;
	
	/**
	 * Validates the absenceDelete action
	 */
	public function validateAbsenceDelete() {
		if (WCF::getUser()->userID != $this->objectIDs[0]) {
			WCF::getSession()->checkPermissions(['mod.absence.canDeleteAbsence']);
		}
	}
	
	/**
	 * Executes the absenceDelete action
	 */
	public function absenceDelete() {
		$data = [
				'absentFrom' => 0,
				'absentTo' => 0,
				'absentReason' => '',
				'absentAuto' => 0,
				'absentRepID' => null
		];
		
		$userAction = new UserAction([$this->objectIDs[0]], 'update', ['data' => $data]);
		$userAction->executeAction();
		
		AbsentMembersBoxCacheBuilder::getInstance()->reset();
	}
}
