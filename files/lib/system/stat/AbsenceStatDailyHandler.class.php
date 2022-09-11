<?php
namespace wcf\system\stat;
use wcf\system\WCF;

/**
 * Stat handler implementation for absence stats.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.absence
 */
class AbsenceStatDailyHandler extends AbstractStatDailyHandler {
	/**
	 * @inheritDoc
	 */
	public function getData($date) {
		$sql = "SELECT	COUNT(*)
				FROM	wcf".WCF_N."_user
				WHERE	UNIX_TIMESTAMP() BETWEEN absentFrom AND absentTo";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		$counter = intval($statement->fetchColumn());
		
		$sql = "SELECT	SUM(count) as total
				FROM	wcf".WCF_N."_stat_absence
				WHERE	time < ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$date + 86400]);
		$total = intval($statement->fetchColumn());
		
		return [
				'counter' => $counter,
				'total' => $total
		];
	}
}
