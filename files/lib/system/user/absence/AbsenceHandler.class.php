<?php
namespace wcf\system\user\absence;
use wcf\data\user\User;
use wcf\system\cache\runtime\UserRuntimeCache;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Handles absences.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.absence
 */
class AbsenceHandler extends SingletonFactory {
	/**
	 * Returns true if the current user is absent.
	 */
	public function isAbsent($user = null) {
		if ($user === null) $user = WCF::getUser();
		if (!$user->userID) return false;
		
		if ($user->absentFrom < TIME_NOW && $user->absentTo > TIME_NOW) return true;
		return false;
	}
	
	/**
	 * Returns true if the current user is absent in the future.
	 */
	public function isAbsentFuture($user = null) {
		if ($user === null) $user = WCF::getUser();
		if (!$user->userID) return false;
		
		if ($user->absentFrom > TIME_NOW && $user->absentTo > TIME_NOW) return true;
		return false;
	}
	
	/**
	 * Returns true if the current user can view absences.
	 */
	public function canViewAbsence() {
		if (!MODULE_ABSENCE) return false;
		if (!WCF::getSession()->getPermission('user.profile.canViewAbsence')) return false;
		return true;
	}
	
	/**
	 * Return user object of representative
	 */
	public function getRep($userID = null) {
		// not enabled
		if (!MODULE_ABSENCE) return null;
		if (!ABSENCE_REP_ENABLE) return null;
		
		// user and permission
		if ($userID === null) {
			$user = WCF::getUser();
			if (!WCF::getSession()->getPermission('user.profile.canUseAbsenceRep')) return null;
		}
		else {
			$user = UserProfileRuntimeCache::getInstance()->getObject($userID);
			if ($user === null) return null;
			if (!$user->getPermission('user.profile.canUseAbsenceRep')) return null;
		}
		
		if (!$user->absentRepID) return null;
		
		return UserRuntimeCache::getInstance()->getObject($user->absentRepID);
	}
}
