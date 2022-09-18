<ul class="inlineList commaSeparated small">
    {foreach from=$userProfiles item=user}
        <li>{user object=$user}</li>
    {/foreach}
</ul>

<p><small>{lang}wcf.user.absence.detail{/lang}{if ABSENCE_DISPLAY_RECORD && ABSENCE_DISPLAY_RECORD_COUNT} <span class="separatorLeft">{lang}wcf.user.absence.detail.record{/lang}</span>{/if}</small></p>
