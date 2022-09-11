{capture assign='pageTitle'}{lang}wcf.user.menu.settings{/lang}: {lang}wcf.user.absence.setting{/lang} - {lang}wcf.user.menu.settings{/lang}{/capture}

{capture assign='contentTitle'}{lang}wcf.user.menu.settings{/lang}: {lang}wcf.user.absence.setting{/lang}{/capture}

{capture assign='contentDescription'}{lang}wcf.user.absence.setting.description{/lang}{/capture}

{include file='userMenuSidebar'}

{include file='header' __disableAds=true}

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.edit{/lang}</p>
{/if}

<form method="post" action="{link controller='AbsenceSettings'}{/link}">
	<div id="absenceSettings">
		<section class="section">
			<h2 class="sectionTitle">{lang}wcf.user.absence.setting.period{/lang}</h2>
			
			<dl{if $errorField == 'absentFrom'} class="formError"{/if}>
				<dt><label for="absentFrom">{lang}wcf.user.absence.setting.from{/lang}</label></dt>
				<dd>
					<input type="datetime" id="absentFrom" name="absentFrom" value="{$absentFrom}" class="medium">
					{if $errorField == 'absentFrom'}
						<small class="innerError">
						
								{lang}wcf.user.absence.setting.from.error.{@$errorType}{/lang}
				
						</small>
					{/if}
				</dd>
			</dl>
			<dl{if $errorField == 'absentTo'} class="formError"{/if}>
				<dt><label for="absentTo">{lang}wcf.user.absence.setting.to{/lang}</label></dt>
				<dd>
					<input type="datetime" id="absentTo" name="absentTo" value="{$absentTo}" class="medium">
					{if $errorField == 'absentTo'}
						<small class="innerError">
						
								{lang}wcf.user.absence.setting.to.error.{@$errorType}{/lang}
						
						</small>
					{/if}
				</dd>
			</dl>
		</section>
		<section class="section">
			<h2 class="sectionTitle">{lang}wcf.user.absence.setting.reason{/lang}</h2>
			
			<dl{if $errorField == 'absentReason'} class="formError"{/if}>
				<dt><label for="absentReason">{lang}wcf.user.absence.setting.reason.detail{/lang}</label></dt>
				<dd>
					<textarea name="absentReason" id="absentReason" rows="2">{if !$absentReason|empty}{$absentReason}{/if}</textarea>
					
					{if $errorField == 'absentReason'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.user.absence.setting.reason.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
		</section>
		
		{if ABSENCE_REP_ENABLE && $__wcf->session->getPermission('user.profile.canUseAbsenceRep')}
			<section class="section">
				<h2 class="sectionTitle">{lang}wcf.user.absence.setting.rep{/lang}</h2>
				
				<dl{if $errorField == 'absentRepName'} class="formError"{/if}>
					<dt><label for="absentRepName">{lang}wcf.user.absence.setting.repName{/lang}</label></dt>
					<dd>
						<input type="text" id="absentRepName" name="absentRepName" value="{$absentRepName}" class="medium" maxlength="255">
						<small>{lang}wcf.user.absence.setting.repName.description{/lang}</small>
						
						{if $errorField == 'absentRepName'}
							<small class="innerError">
								{lang}wcf.user.absence.setting.repName.error.{@$errorType}{/lang}
							</small>
						{/if}
					</dd>
				</dl>
			</section>
		{/if}
		
		{event name='sections'}
	</div>
	
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
		{csrfToken}
	</div>
</form>

{if ABSENCE_REP_ENABLE && $__wcf->session->getPermission('user.profile.canUseAbsenceRep')}
	<script data-relocate="true">
		require(['WoltLabSuite/Core/Ui/User/Search/Input'], function(UiUserSearchInput) {
			new UiUserSearchInput(elBySel('input[name="absentRepName"]'));
		});
	</script>
{/if}

{include file='footer' __disableAds=true}
