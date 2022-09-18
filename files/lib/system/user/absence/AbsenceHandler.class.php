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
namespace wcf\system\user\absence;

use wcf\data\user\User;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\cache\runtime\UserRuntimeCache;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Handles absences.
 */
class AbsenceHandler extends SingletonFactory
{
    /**
     * Returns true if the current user is absent.
     */
    public function isAbsent($user = null)
    {
        if ($user === null) {
            $user = WCF::getUser();
        }
        if (!$user->userID) {
            return false;
        }

        if ($user->absentFrom < TIME_NOW && $user->absentTo > TIME_NOW) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if the current user is absent in the future.
     */
    public function isAbsentFuture($user = null)
    {
        if ($user === null) {
            $user = WCF::getUser();
        }
        if (!$user->userID) {
            return false;
        }

        if ($user->absentFrom > TIME_NOW && $user->absentTo > TIME_NOW) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if the current user can view absences.
     */
    public function canViewAbsence()
    {
        if (!MODULE_ABSENCE) {
            return false;
        }
        if (!WCF::getSession()->getPermission('user.profile.canViewAbsence')) {
            return false;
        }

        return true;
    }

    /**
     * Return user object of representative
     */
    public function getRep($userID = null)
    {
        // not enabled
        if (!MODULE_ABSENCE) {
            return null;
        }
        if (!ABSENCE_REP_ENABLE) {
            return null;
        }

        // user and permission
        if ($userID === null) {
            $user = WCF::getUser();
            if (!WCF::getSession()->getPermission('user.profile.canUseAbsenceRep')) {
                return null;
            }
        } else {
            $user = UserProfileRuntimeCache::getInstance()->getObject($userID);
            if ($user === null) {
                return null;
            }
            if (!$user->getPermission('user.profile.canUseAbsenceRep')) {
                return null;
            }
        }

        if (!$user->absentRepID) {
            return null;
        }

        return UserRuntimeCache::getInstance()->getObject($user->absentRepID);
    }
}
