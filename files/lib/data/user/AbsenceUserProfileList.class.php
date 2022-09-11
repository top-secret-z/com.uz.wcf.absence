<?php
namespace wcf\data\user;
use wcf\system\cache\builder\AbsentMembersBoxCacheBuilder;
use wcf\system\WCF;

/**
 * Represents a list of absent user profiles.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.absence
 */
class AbsenceUserProfileList extends UserProfileList {
	
	/**
	 * Creates a new AbsenceUserProfileList object.
	 */
	public function __construct() {
		parent::__construct();
		
		// get users from cache
		$userIDs = AbsentMembersBoxCacheBuilder::getInstance()->getData();
		if (!empty($userIDs)) $this->getConditionBuilder()->add("user_table.userID IN (?)", [$userIDs]);
		else $this->getConditionBuilder()->add("1=0");
		
		// must have permission
		if (!WCF::getSession()->getPermission('user.profile.canViewAbsence')) {
			$this->getConditionBuilder()->add("1=0");
		}
	}
}
