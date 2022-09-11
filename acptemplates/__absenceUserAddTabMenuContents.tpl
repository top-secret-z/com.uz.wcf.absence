	<div id="absenceForm" class="tabMenuContent hidden">
		<section class="section">
			<h2 class="sectionTitle">{lang}wcf.user.menu.settings.absence{/lang}</h2>
			{if $absentAuto}
				<p>automatisch</p>
			{/if}
			
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
		
		{if ABSENCE_REP_ENABLE}
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
	</div>
