{if ABSENCE_DISPLAY_SIDEBAR}
	{assign var='hasRep' value=$__wcf->getAbsenceHandler()->getRep($userProfile->userID)}
	
	{if $__wcf->getAbsenceHandler()->canViewAbsence() && $__wcf->getAbsenceHandler()->isAbsent($userProfile)}
		<div class="messageAuthor">
			{if $__wcf->getUser()->userID == $userProfile->userID}
				<a href="{link controller='AbsenceSettings'}{/link}" class="jsTooltip badge {ABSENCE_DISPLAY_SIDEBAR_COLOR} pointer" title="{lang rep=$hasRep}wcf.user.absence.info.tooltip.userProfile{if !ABSENCE_DISPLAY_TIME}.noTime{/if}{/lang}">{lang}wcf.user.absence.absent{/lang}</a>
			{else}
				<span class="jsTooltip badge {ABSENCE_DISPLAY_SIDEBAR_COLOR}" title="{lang rep=$hasRep}wcf.user.absence.info.tooltip.userProfile{if !ABSENCE_DISPLAY_TIME}.noTime{/if}{/lang}">{lang}wcf.user.absence.absent{/lang}</span>
			{/if}
		</div>
	{/if}
{/if}