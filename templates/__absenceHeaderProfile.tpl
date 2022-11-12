{if MODULE_ABSENCE && ABSENCE_DISPLAY_WARNING && $templateName|isset}
    {if $__wcf->getAbsenceHandler()->isAbsent() && $__wcf->session->getPermission('user.profile.canUseAbsence') && $templateName != 'absenceSettings'}
        <div id="jsAbsenceWarning" class="error">{lang}wcf.user.absence.warning.online{/lang}</div>
    {/if}
{/if}

{if MODULE_ABSENCE && $__wcf->getAbsenceHandler()->canViewAbsence() && $templateName|isset && $templateName == 'user'}
    {assign var='hasRep' value=$__wcf->getAbsenceHandler()->getRep($user->userID)}

    {if $__wcf->getAbsenceHandler()->isAbsent($user)}
        <div class="userNotice">
            <p class="error notice">
                {if $__wcf->session->getPermission('mod.absence.canDeleteAbsence')}
                    <span class="icon icon16 fa-times pointer jsTooltip jsAbsenceDelete" data-object-id="{$user->userID}" title="{lang}wcf.user.absence.delete{/lang}"></span>
                {/if}
                {lang rep=$hasRep}wcf.user.absence.info.profile{if !ABSENCE_DISPLAY_TIME}.noTime{/if}{/lang}
            </p>
        </div>
    {elseif $__wcf->getAbsenceHandler()->isAbsentFuture($user)}
        <div class="info">{lang rep=$hasRep}wcf.user.absence.info.profile{if !ABSENCE_DISPLAY_TIME}.noTime{/if}{/lang}</div>
    {/if}

    <script data-relocate="true">
        require(['UZ/Absence/Delete'], function(AbsenceDelete) {
            AbsenceDelete.setup();
        });

        $(function() {
            WCF.Language.addObject({
                'wcf.user.absence.delete.confirm': '{jslang}wcf.user.absence.delete.confirm{/jslang}'
            });
        });
    </script>
{/if}
