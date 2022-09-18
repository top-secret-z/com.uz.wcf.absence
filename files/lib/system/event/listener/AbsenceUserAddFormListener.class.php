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
namespace wcf\system\event\listener;

use DateTime;
use wcf\data\user\User;
use wcf\system\cache\builder\AbsentMembersBoxCacheBuilder;
use wcf\system\cache\runtime\UserRuntimeCache;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\DateUtil;
use wcf\util\StringUtil;

/**
 * Handles absence data during edit.
 */
class AbsenceUserAddFormListener implements IParameterizedEventListener
{
    /**
     * instance of UserAdd/EditForm
     */
    protected $eventObj;

    /**
     * absence data
     */
    protected $absentFrom = '';

    protected $absentFromObj = false;

    protected $absentReason = '';

    protected $absentTo = '';

    protected $absentToObj = false;

    protected $absentAuto = 0;

    protected $absentRepID;

    protected $absentRepName = '';

    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        $this->eventObj = $eventObj;

        $this->{$eventName}();
    }

    /**
     * Handles the assignVariables event.
     */
    protected function assignVariables()
    {
        WCF::getTPL()->assign([
            'absentFrom' => $this->absentFrom,
            'absentReason' => $this->absentReason,
            'absentTo' => $this->absentTo,
            'absentAuto' => $this->absentAuto,
            'absentRepName' => $this->absentRepName,
        ]);
    }

    /**
     * Handles the readData event.
     */
    protected function readData()
    {
        if (empty($_POST)) {
            $this->absentFrom = '';
            if ($this->eventObj->user->absentFrom) {
                $dateTime = DateUtil::getDateTimeByTimestamp($this->eventObj->user->absentFrom);
                $dateTime->setTimezone($this->eventObj->user->getTimeZone());
                $this->absentFrom = $dateTime->format('c');
            }

            $this->absentTo = '';
            if ($this->eventObj->user->absentTo) {
                $dateTime = DateUtil::getDateTimeByTimestamp($this->eventObj->user->absentTo);
                $dateTime->setTimezone($this->eventObj->user->getTimeZone());
                $this->absentTo = $dateTime->format('c');
            }

            $this->absentReason = $this->eventObj->user->absentReason;
            $this->absentAuto = $this->eventObj->user->absentAuto;

            $this->absentRepID = $this->eventObj->user->absentRepID;
            $this->absentRepName = '';
            if (ABSENCE_REP_ENABLE && $this->absentRepID) {
                $user = UserRuntimeCache::getInstance()->getObject($this->absentRepID);
                if ($user !== null) {
                    $this->absentRepName = $user->username;
                }
            }
        }
    }

    /**
     * Handles the readFormParameters event.
     */
    protected function readFormParameters()
    {
        if (isset($_POST['absentFrom'])) {
            $this->absentFrom = $_POST['absentFrom'];
        }
        if (isset($_POST['absentReason'])) {
            $this->absentReason = StringUtil::trim($_POST['absentReason']);
        }
        if (isset($_POST['absentTo'])) {
            $this->absentTo = $_POST['absentTo'];
        }
        if (isset($_POST['absentRepName'])) {
            $this->absentRepName = StringUtil::trim($_POST['absentRepName']);
        }

        $this->absentFromObj = DateTime::createFromFormat('Y-m-d\TH:i:sP', $this->absentFrom);
        $this->absentToObj = DateTime::createFromFormat('Y-m-d\TH:i:sP', $this->absentTo);
    }

    /**
     * Handles the validate event.
     */
    protected function validate()
    {
        // either both or none
        if (!empty($this->absentFrom) && empty($this->absentTo)) {
            throw new UserInputException('absentTo', 'empty');
        }
        if (empty($this->absentFrom) && !empty($this->absentTo)) {
            throw new UserInputException('absentFrom', 'empty');
        }

        // only if times are set
        if (!empty($this->absentFrom)) {
            // must be valid
            if ($this->absentFromObj === false) {
                throw new UserInputException('absentFrom', 'invalid');
            }
            if ($this->absentToObj === false) {
                throw new UserInputException('absentTo', 'invalid');
            }

            // from before to
            if ($this->absentToObj->getTimestamp() < $this->absentFromObj->getTimestamp()) {
                throw new UserInputException('absentTo', 'toBeforeFrom');
            }

            // accept to in past in ACP
        }

        // reason max. 255 chars
        if (!empty($this->absentReason) && \mb_strlen($this->absentReason) > 255) {
            throw new UserInputException('absentReason', 'tooLong');
        }

        // representative
        $this->absentRepID = null;
        if (ABSENCE_REP_ENABLE && !empty($this->absentRepName)) {
            $user = User::getUserByUsername($this->absentRepName);
            if (!$user->userID) {
                throw new UserInputException('absentRepName', 'notFound');
            }
            if ($user->userID == $this->eventObj->user->userID) {
                throw new UserInputException('absentRepName', 'notSelf');
            }

            $this->absentRepID = $user->userID;
        }
    }

    /**
     * Handles the save event.
     */
    protected function save()
    {
        $from = $this->absentFromObj ? $this->absentFromObj->getTimestamp() : 0;
        $to = $this->absentToObj ? $this->absentToObj->getTimestamp() : 0;

        $this->eventObj->additionalFields = \array_merge($this->eventObj->additionalFields, [
            'absentFrom' => $from,
            'absentTo' => $to,
            'absentReason' => $this->absentReason,
            'absentAuto' => 0,    // edit -> not automatic
            'absentRepID' => $this->absentRepID,
        ]);
    }

    /**
     * Handles the saved event.
     */
    protected function saved()
    {
        AbsentMembersBoxCacheBuilder::getInstance()->reset();
    }
}
