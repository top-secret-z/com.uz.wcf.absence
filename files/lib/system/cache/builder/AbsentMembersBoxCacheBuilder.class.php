<?php
namespace wcf\system\cache\builder;
use wcf\data\option\OptionAction;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Caches absent members.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.absence
 */
class AbsentMembersBoxCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @inheritDoc
	 */
	protected $maxLifetime = 600;
	
	/**
	 * @inheritDoc
	 */
	protected function rebuild(array $parameters) {
		$data = [];
		
		$conditions = new PreparedStatementConditionBuilder();
		$conditions->add('absentFrom < ?', [TIME_NOW]);
		$conditions->add('absentTo > ?', [TIME_NOW]);
		if (ABSENCE_DISPLAY_HIDEAUTO) {
			$conditions->add('absentAuto = 0');
		}
		
		$sql = "SELECT		userID
				FROM		wcf".WCF_N."_user
				".$conditions;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditions->getParameters());
		
		while ($row = $statement->fetchArray()) {
			$data[] = $row['userID'];
		}
		
		// set record
		$count = count($data);
		if ($count > ABSENCE_DISPLAY_RECORD_COUNT) {
			$optionAction = new OptionAction([], 'import', [
					'data' => [
							'absence_display_record_count' => $count,
							'absence_display_record_time' => TIME_NOW
					]
			]);
			$optionAction->executeAction();
		}
		
		return $data;
	}
}
