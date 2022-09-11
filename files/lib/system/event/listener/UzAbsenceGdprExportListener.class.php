<?php
namespace wcf\system\event\listener;
use wcf\system\event\listener\IParameterizedEventListener;

/**
 * Exports user data iwa Gdpr.
 *
 * @author		2018-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.absence
 */
class UzAbsenceGdprExportListener implements IParameterizedEventListener {
	/**
	 * @inheritDoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		// add absence data in user
		$eventObj->exportUserPropertiesIfNotEmpty[] = 'absentFrom';
		$eventObj->exportUserPropertiesIfNotEmpty[] = 'absentTo';
		$eventObj->exportUserPropertiesIfNotEmpty[] = 'absentReason';
	}
}
