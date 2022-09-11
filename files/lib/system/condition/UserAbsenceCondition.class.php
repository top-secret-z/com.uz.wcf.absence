<?php
namespace wcf\system\condition;
use wcf\data\condition\Condition;
use wcf\data\user\User;
use wcf\data\user\UserList;
use wcf\data\DatabaseObjectList;
use wcf\system\exception\ParentClassException;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

/**
 * Condition implementation for the absence of a user.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.absence
 */
class UserAbsenceCondition extends AbstractSingleFieldCondition implements IContentCondition, IObjectListCondition, IUserCondition {
	use TObjectListUserCondition;
	
	/**
	 * @inheritDoc
	 */
	protected $label = 'wcf.user.condition.absence';
	
	/**
	 * true if the the user is absent / not absent
	 */
	protected $userIsAbsent = 0;
	protected $userIsNotAbsent = 0;
	
	/**
	 * @inheritDoc
	 */
	public function addObjectListCondition(DatabaseObjectList $objectList, array $conditionData) {
		if (!($objectList instanceof UserList)) {
			throw new ParentClassException(get_class($objectList), UserList::class);
		}
		
		if (isset($conditionData['userIsAbsent'])) {
			$objectList->getConditionBuilder()->add('(user_table.absentFrom < ? AND user_table.absentTo > ?)', [TIME_NOW, TIME_NOW]);
		}
		if (isset($conditionData['userIsNotAbsent'])) {
			$objectList->getConditionBuilder()->add('((user_table.absentFrom < ? AND user_table.absentTo < ?) OR (user_table.absentFrom > ? AND user_table.absentTo > ?))', [TIME_NOW, TIME_NOW, TIME_NOW, TIME_NOW]);
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function checkUser(Condition $condition, User $user) {
		$isAbsent = false;
		if ($user->absentFrom < TIME_NOW && $user->absentTo > TIME_NOW) $isAbsent = true;
		
		if ($condition->userIsAbsent !== null && !$isAbsent) return false;
		if ($condition->userIsNotAbsent !== null && $isAbsent) return false;
		
		return true;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getData() {
		$data = [];
		
		if ($this->userIsAbsent) {
			$data['userIsAbsent'] = 1;
		}
		if ($this->userIsNotAbsent) {
			$data['userIsNotAbsent'] = 1;
		}
		
		if (!empty($data)) {
			return $data;
		}
		
		return null;
	}
	
	/**
	 * Returns the "checked" attribute for an input element.
	 */
	protected function getCheckedAttribute($propertyName) {
		if ($this->$propertyName) {
			return ' checked';
		}
		
		return '';
	}
	
	/**
	 * @inheritDoc
	 */
	protected function getFieldElement() {
		$userIsNotAbsent = WCF::getLanguage()->get('wcf.user.condition.absence.isNotAbsent');
		$userIsAbsent = WCF::getLanguage()->get('wcf.user.condition.absence.isAbsent');
		
		return <<<HTML
<label><input type="checkbox" name="userIsAbsent" value="1"{$this->getCheckedAttribute('userIsAbsent')}> {$userIsAbsent}</label>
<label><input type="checkbox" name="userIsNotAbsent" value="1"{$this->getCheckedAttribute('userIsNotAbsent')}> {$userIsNotAbsent}</label>
HTML;
	}
	
	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		if (isset($_POST['userIsAbsent'])) $this->userIsAbsent = 1;
		if (isset($_POST['userIsNotAbsent'])) $this->userIsNotAbsent = 1;
	}
	
	/**
	 * @inheritDoc
	 */
	public function reset() {
		$this->userIsAbsent = 0;
		$this->userIsNotAbsent = 0;
	}
	
	/**
	 * @inheritDoc
	 */
	public function setData(Condition $condition) {
		if ($condition->userIsAbsent !== null) {
			$this->userIsAbsent = $condition->userIsAbsent;
		//	$this->userIsNotAbsent = !$condition->userIsAbsent;
		}
		
		if ($condition->userIsNotAbsent !== null) {
			$this->userIsNotAbsent = $condition->userIsNotAbsent;
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function validate() {
		if ($this->userIsAbsent && $this->userIsNotAbsent) {
			$this->errorMessage = 'wcf.user.condition.absence.isAbsent.error.conflict';
			
			throw new UserInputException('userIsAbsent', 'conflict');
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function showContent(Condition $condition) {
		if (!WCF::getUser()->userID) return false;
		
		return $this->checkUser($condition, WCF::getUser());
	}
}
