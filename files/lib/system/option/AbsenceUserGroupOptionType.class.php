<?php

/*
 * Copyright by Udo Zaydowicz.
 * Modified by SoftCreatR.dev.
 *
 * License: http://opensource.org/licenses/lgpl-license.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
namespace wcf\system\option;

use wcf\data\option\Option;
use wcf\data\user\group\UserGroup;
use wcf\system\exception\UserInputException;
use wcf\system\option\user\group\IUserGroupOptionType;
use wcf\util\ArrayUtil;
use wcf\util\StringUtil;

use const SORT_NUMERIC;

/**
 * User group option type implementation for a user group select list.
 */
class AbsenceUserGroupOptionType extends AbstractOptionType implements IUserGroupOptionType
{
    /**
     * @inheritDoc
     */
    public function getFormElement(Option $option, $value)
    {
        // get selected group
        $selectedGroups = \explode(',', $value);

        // get all groups
        $groups = UserGroup::getGroupsByType([], [
            UserGroup::EVERYONE,
            UserGroup::GUESTS,
            UserGroup::USERS,
        ]);

        // generate html
        $html = '';
        foreach ($groups as $group) {
            $html .= '<label><input type="checkbox" name="values[' . StringUtil::encodeHTML($option->optionName) . '][]" value="' . $group->groupID . '"' . (\in_array($group->groupID, $selectedGroups) ? ' checked' : '') . '> ' . $group->getName() . '</label>';
        }

        return $html;
    }

    /**
     * @inheritDoc
     */
    public function validate(Option $option, $newValue)
    {
        // get all groups
        $groups = UserGroup::getGroupsByType([], [
            UserGroup::EVERYONE,
            UserGroup::GUESTS,
            UserGroup::USERS,
        ]);

        // get new value
        if (!\is_array($newValue)) {
            $newValue = [];
        }
        $selectedGroups = ArrayUtil::toIntegerArray($newValue);

        // check groups
        foreach ($selectedGroups as $groupID) {
            if (!isset($groups[$groupID])) {
                throw new UserInputException($option->optionName, 'validationFailed');
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getData(Option $option, $newValue)
    {
        if (!\is_array($newValue)) {
            $newValue = [];
        }
        $newValue = ArrayUtil::toIntegerArray($newValue);
        \sort($newValue, SORT_NUMERIC);

        return \implode(',', $newValue);
    }

    /**
     * @inheritDoc
     */
    public function merge($defaultValue, $groupValue)
    {
        $defaultValue = empty($defaultValue) ? [] : \explode(',', StringUtil::unifyNewlines($defaultValue));
        $groupValue = empty($groupValue) ? [] : \explode(',', StringUtil::unifyNewlines($groupValue));

        return \implode(',', \array_unique(\array_merge($defaultValue, $groupValue)));
    }

    /**
     * @inheritDoc
     */
    public function compare($value1, $value2)
    {
        $value1 = $value1 ? \explode(',', $value1) : [];
        $value2 = $value2 ? \explode(',', $value2) : [];

        // check if value1 contains more elements than value2
        $diff = \array_diff($value1, $value2);
        if (!empty($diff)) {
            return 1;
        }

        // check if value1 contains less elements than value2
        $diff = \array_diff($value2, $value1);
        if (!empty($diff)) {
            return -1;
        }

        // both lists are equal
        return 0;
    }
}
