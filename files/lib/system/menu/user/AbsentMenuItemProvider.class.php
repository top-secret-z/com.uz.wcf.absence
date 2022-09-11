<?php
namespace wcf\system\menu\user;
use wcf\system\WCF;

/**
 * UserMenuItemProvider for Absence.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.absence
 */
class AbsentMenuItemProvider extends DefaultUserMenuItemProvider {
	public function isVisible() {
		if (!WCF::getSession()->getPermission('user.profile.canUseAbsence')) return false;
		return true;
	}
}
