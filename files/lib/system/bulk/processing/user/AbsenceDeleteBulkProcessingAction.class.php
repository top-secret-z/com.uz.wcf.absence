<?php
namespace wcf\system\bulk\processing\user;
use wcf\data\DatabaseObjectList;
use wcf\data\user\UserList;
use wcf\system\cache\builder\AbsentMembersBoxCacheBuilder;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Bulk processing action implementation for deleting absences.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.absence
 */
class AbsenceDeleteBulkProcessingAction extends AbstractUserBulkProcessingAction {
	/**
	 * @inheritDoc
	 */
	public function executeAction(DatabaseObjectList $objectList) {
		if (!($objectList instanceof UserList)) return;
		
		$userIDs = $objectList->getObjectIDs();
		
		if (!empty($userIDs)) {
			$conditions = new PreparedStatementConditionBuilder();
			$conditions->add("userID IN (?)", [$userIDs]);
			
			$sql = "UPDATE	wcf".WCF_N."_user
					SET absentFrom = 0, absentTo = 0, absentReason = '', absentAuto = 0, absentRepID = null
				".$conditions;
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute($conditions->getParameters());
			
			// reset absence cache
			AbsentMembersBoxCacheBuilder::getInstance()->reset();
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function getObjectList() {
		$userList = parent::getObjectList();
		
		// only users with absence
		$userList->getConditionBuilder()->add("(user_table.absentFrom > 0 OR user_table.absentTo > 0 OR user_table.absentReason != '' OR user_table.absentAuto > 0)");
		
		return $userList;
	}
}
