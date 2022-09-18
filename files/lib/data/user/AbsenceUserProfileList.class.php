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
namespace wcf\data\user;

use wcf\system\cache\builder\AbsentMembersBoxCacheBuilder;
use wcf\system\WCF;

/**
 * Represents a list of absent user profiles.
 */
class AbsenceUserProfileList extends UserProfileList
{
    /**
     * Creates a new AbsenceUserProfileList object.
     */
    public function __construct()
    {
        parent::__construct();

        // get users from cache
        $userIDs = AbsentMembersBoxCacheBuilder::getInstance()->getData();
        if (!empty($userIDs)) {
            $this->getConditionBuilder()->add("user_table.userID IN (?)", [$userIDs]);
        } else {
            $this->getConditionBuilder()->add("1=0");
        }

        // must have permission
        if (!WCF::getSession()->getPermission('user.profile.canViewAbsence')) {
            $this->getConditionBuilder()->add("1=0");
        }
    }
}
