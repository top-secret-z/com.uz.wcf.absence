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
 * Actions for absences.
 */
class AbsenceAction extends UserAction
{
    /**
     * Absent user.
     */
    protected $absentUser;

    /**
     * Validates the absenceDelete action
     */
    public function validateAbsenceDelete()
    {
        if (WCF::getUser()->userID != $this->objectIDs[0]) {
            WCF::getSession()->checkPermissions(['mod.absence.canDeleteAbsence']);
        }
    }

    /**
     * Executes the absenceDelete action
     */
    public function absenceDelete()
    {
        $data = [
            'absentFrom' => 0,
            'absentTo' => 0,
            'absentReason' => '',
            'absentAuto' => 0,
            'absentRepID' => null,
        ];

        $userAction = new UserAction([$this->objectIDs[0]], 'update', ['data' => $data]);
        $userAction->executeAction();

        AbsentMembersBoxCacheBuilder::getInstance()->reset();
    }
}
