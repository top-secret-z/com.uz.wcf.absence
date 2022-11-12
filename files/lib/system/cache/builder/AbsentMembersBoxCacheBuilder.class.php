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
namespace wcf\system\cache\builder;

use wcf\data\option\OptionAction;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Caches absent members.
 */
class AbsentMembersBoxCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    protected $maxLifetime = 600;

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters)
    {
        $data = [];

        $conditions = new PreparedStatementConditionBuilder();
        $conditions->add('absentFrom < ?', [TIME_NOW]);
        $conditions->add('absentTo > ?', [TIME_NOW]);
        if (ABSENCE_DISPLAY_HIDEAUTO) {
            $conditions->add('absentAuto = 0');
        }

        $sql = "SELECT        userID
                FROM        wcf" . WCF_N . "_user
                " . $conditions;
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute($conditions->getParameters());

        while ($row = $statement->fetchArray()) {
            $data[] = $row['userID'];
        }

        // set record
        $count = \count($data);
        if ($count > ABSENCE_DISPLAY_RECORD_COUNT) {
            $optionAction = new OptionAction([], 'import', [
                'data' => [
                    'absence_display_record_count' => $count,
                    'absence_display_record_time' => TIME_NOW,
                ],
            ]);
            $optionAction->executeAction();
        }

        return $data;
    }
}
