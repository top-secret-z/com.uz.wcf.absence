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
namespace wcf\system\user\activity\event;

use wcf\data\user\UserProfileList;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * User activity event implementation for absence action.
 */
class AbsenceUserActivityEvent extends SingletonFactory implements IUserActivityEvent
{
    /**
     * @inheritDoc
     */
    public function prepare(array $events)
    {
        $objectIDs = [];
        foreach ($events as $event) {
            $objectIDs[] = $event->objectID;
        }

        // fetch user profiles
        $userList = new UserProfileList();
        $userList->getConditionBuilder()->add("user_table.userID IN (?)", [$objectIDs]);
        $userList->readObjects();
        $users = $userList->getObjects();

        // set message
        foreach ($events as $event) {
            if (isset($users[$event->objectID])) {
                if (!WCF::getSession()->getPermission('user.profile.canViewAbsence')) {
                    continue;
                }
                $event->setIsAccessible();

                // title
                $text = WCF::getLanguage()->getDynamicVariable('wcf.user.absence.recentActivity.submit', ['user' => $users[$event->objectID]]);
                $event->setTitle($text);

                // description
                $event->setDescription('');
            } else {
                $event->setIsOrphaned();
            }
        }
    }
}
