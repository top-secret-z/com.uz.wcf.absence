<li><a href="{@$__wcf->getAnchor('absenceForm')}">{lang}wcf.user.menu.settings.absence{/lang}</a></li>

{if ABSENCE_REP_ENABLE}
    <script data-relocate="true">
        require(['WoltLabSuite/Core/Ui/User/Search/Input'], function(UiUserSearchInput) {
            new UiUserSearchInput(elBySel('input[name="absentRepName"]'));
        });
    </script>
{/if}
