{if ABSENCE_DISPLAY_USERINFO}
	{if $__wcf->getAbsenceHandler()->canViewAbsence() && $__wcf->getAbsenceHandler()->isAbsent($user)}
		<li class="jsTooltip" title="{lang}wcf.user.absence.info.tooltip{if !ABSENCE_DISPLAY_TIME}.noTime{/if}{/lang}">{lang}wcf.user.absence.info.userInformation{if !ABSENCE_DISPLAY_TIME}.noTime{/if}{/lang}</li>
	{/if}
{/if}