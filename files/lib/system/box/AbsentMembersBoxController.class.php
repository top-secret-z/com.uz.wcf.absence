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
namespace wcf\system\box;

use wcf\data\user\AbsenceUserProfileList;
use wcf\system\event\EventHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Shows absent members.
 */
class AbsentMembersBoxController extends AbstractDatabaseObjectListBoxController
{
    /**
     * @inheritDoc
     */
    protected static $supportedPositions = ['footerBoxes', 'sidebarLeft', 'sidebarRight'];

    /**
     * @inheritDoc
     */
    public $validSortFields = [
        'username',
        'absentFrom',
        'absentTo',
    ];

    /**
     * @inheritDoc
     */
    protected $sortFieldLanguageItemPrefix = 'wcf.user.absence.box.sort';

    /**
     * @inheritDoc
     */
    public function getLink()
    {
        return LinkHandler::getInstance()->getLink('AbsentMembersList');
    }

    /**
     * @inheritDoc
     */
    public function hasLink()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function hasContent()
    {
        if (!MODULE_ABSENCE || !WCF::getSession()->getPermission('user.profile.canViewAbsence')) {
            return false;
        }

        return parent::hasContent();
    }

    /**
     * @inheritDoc
     */
    protected function getObjectList()
    {
        return new AbsenceUserProfileList();
    }

    /**
     * @inheritDoc
     */
    protected function readObjects()
    {
        EventHandler::getInstance()->fireAction($this, 'readObjects');

        if ($this->box->sortOrder && $this->box->sortField && isset($this->realSortFields[$this->box->sortField])) {
            $this->objectList->sqlOrderBy = \implode(' ' . $this->box->sortOrder . ',', $this->realSortFields[$this->box->sortField]) . ' ' . $this->box->sortOrder;
        }

        parent::readObjects();
    }

    /**
     * @inheritDoc
     */
    protected function getTemplate()
    {
        $templateName = 'boxAbsentMembersFooter';
        if ($this->box->position === 'sidebarLeft' || $this->box->position === 'sidebarRight') {
            $templateName = 'boxAbsentMembers';
        }

        // check for more than limit
        $total = \count($this->objectList);

        return WCF::getTPL()->fetch($templateName, 'wcf', [
            'userProfiles' => $this->objectList,
            'total' => $total,
        ]);
    }
}
