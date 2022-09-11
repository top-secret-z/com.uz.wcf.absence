<?php
namespace wcf\system\event\listener;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\user\absence\AbsenceHandler;

/**
 * Adds absence search fields to MembersList.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.absence
 */
class AbsentMembersListPageListener implements IParameterizedEventListener {
	/**
	 * @inheritDoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		// only if can view
		if (AbsenceHandler::getInstance()->canViewAbsence()) {
			$eventObj->validSortFields[] = 'absentFrom';
			$eventObj->validSortFields[] = 'absentTo';
		}
	}
}
