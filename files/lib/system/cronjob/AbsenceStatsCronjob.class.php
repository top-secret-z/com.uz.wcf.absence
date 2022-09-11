<?php
namespace wcf\system\cronjob;
use wcf\data\cronjob\Cronjob;
use wcf\system\WCF;

/**
 * Stats cronjob for Absence
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.absence
 */
class AbsenceStatsCronjob extends AbstractCronjob {
	/**
	 * @inheritDoc
	 */
	public function execute(Cronjob $cronjob) {
		parent::execute($cronjob);
		
		// only if configured
		if (!MODULE_ABSENCE) return;
		
		$sql = "SELECT	COUNT(*)
				FROM	wcf".WCF_N."_user
				WHERE	UNIX_TIMESTAMP() BETWEEN absentFrom AND absentTo";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		$count = intval($statement->fetchColumn());
		
		$sql = "INSERT INTO	wcf".WCF_N."_stat_absence
				(count, time) VALUES (?, ?)";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$count, TIME_NOW]);
	}
}
