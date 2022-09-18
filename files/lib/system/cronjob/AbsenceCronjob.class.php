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
namespace wcf\system\cronjob;

use wcf\data\cronjob\Cronjob;
use wcf\data\user\UserAction;
use wcf\data\user\UserList;
use wcf\system\language\LanguageFactory;

/**
 * Cronjob for Absence
 */
class AbsenceCronjob extends AbstractCronjob
{
    /**
     * @inheritDoc
     */
    public function execute(Cronjob $cronjob)
    {
        parent::execute($cronjob);

        // only if configured
        if (!MODULE_ABSENCE) {
            return;
        }

        // remove expired auto absences
        $userList = new UserList();
        $userList->getConditionBuilder()->add('user_table.absentFrom > 0 AND user_table.absentTo < ?', [TIME_NOW]);
        $userList->getConditionBuilder()->add('user_table.absentAuto > 0');
        $userList->sqlLimit = 250;
        $userList->readObjectIDs();
        $userIDs = $userList->getObjectIDs();

        if (\count($userIDs)) {
            // update users
            $userAction = new UserAction($userIDs, 'update', [
                'data' => [
                    'absentFrom' => 0,
                    'absentTo' => 0,
                    'absentAuto' => 0,
                    'absentReason' => '',
                    'absentRepID' => null,
                ],
            ]);
            $userAction->executeAction();
        }

        // remove auto absences if user is online again
        $userList = new UserList();
        $userList->getConditionBuilder()->add('user_table.absentFrom > 0 AND user_table.absentTo > ?', [TIME_NOW]);
        $userList->getConditionBuilder()->add('user_table.absentAuto > 0');
        $userList->getConditionBuilder()->add('user_table.lastActivityTime > ?', [TIME_NOW - 84600]);
        $userList->sqlLimit = 250;
        $userList->readObjectIDs();
        $userIDs = $userList->getObjectIDs();

        if (\count($userIDs)) {
            // update users
            $userAction = new UserAction($userIDs, 'update', [
                'data' => [
                    'absentFrom' => 0,
                    'absentTo' => 0,
                    'absentAuto' => 0,
                    'absentReason' => '',
                    'absentRepID' => null,
                ],
            ]);
            $userAction->executeAction();
        }

        // create automatic absences
        if (ABSENCE_AUTO_DAYS) {
            $excluded = ABSENCE_AUTO_EXCLUDE;

            // get inactive user matching days and excluded groups; only activated and not banned users
            $userList = new UserList();
            if (!empty($excluded)) {
                $userList->getConditionBuilder()->add('user_table.userID NOT IN (SELECT userID from wcf' . WCF_N . '_user_to_group WHERE groupID IN (?))', [\explode(',', $excluded)]);
            }
            $userList->getConditionBuilder()->add('user_table.absentAuto = ?', [0]);
            $userList->getConditionBuilder()->add('user_table.activationCode = ?', [0]);
            $userList->getConditionBuilder()->add('user_table.banned = ?', [0]);
            $userList->getConditionBuilder()->add('user_table.registrationDate < ?', [TIME_NOW - ABSENCE_AUTO_DAYS * 86400]);
            $userList->getConditionBuilder()->add('user_table.lastActivityTime < ?', [TIME_NOW - ABSENCE_AUTO_DAYS * 86400]);
            $userList->sqlLimit = 250;
            $userList->readObjectIDs();
            $userIDs = $userList->getObjectIDs();

            // set users absent for 14 days
            if (\count($userIDs)) {
                $language = LanguageFactory::getInstance()->getLanguage(LanguageFactory::getInstance()->getDefaultLanguageID());
                $userAction = new UserAction($userIDs, 'update', [
                    'data' => [
                        'absentFrom' => TIME_NOW,
                        'absentTo' => \strtotime("midnight", TIME_NOW + 15 * 86400),
                        'absentReason' => $language->get(ABSENCE_AUTO_REASON),
                        'absentAuto' => 1,
                        'absentRepID' => null,
                    ],
                ]);
                $userAction->executeAction();
            }
        }
    }
}
