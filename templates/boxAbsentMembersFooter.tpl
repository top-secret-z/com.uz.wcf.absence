<ul class="inlineList dotSeparated">
    <li>{lang}wcf.user.absence.detail{/lang}</li>
    {if ABSENCE_DISPLAY_RECORD && ABSENCE_DISPLAY_RECORD_COUNT}<li>{lang}wcf.user.absence.detail.record{/lang}</li>{/if}
</ul>

{if $userProfiles|count}
    <ul class="inlineList commaSeparated">
        {foreach from=$userProfiles item=user}
            <li>{user object=$user}</li>
        {/foreach}
    </ul>
{/if}
