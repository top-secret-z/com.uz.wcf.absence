/**
 * Handles deletion of an absence.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.absence
 */
define(['Ajax', 'Language', 'Ui/Confirmation'], function(Ajax, Language, UiConfirmation) {
	"use strict";
	
	/**
	 * @exports	UZ/Absence/Delete
	 */
	var AbsenceDelete = {
		/**
		 * Initializes delete buttons.
		 */
		setup: function() {
			var buttons = elByClass('jsAbsenceDelete');
			
			if (buttons.length) {
				var clickCallback = this._click.bind(this);
				for (var i = 0, length = buttons.length; i < length; i++) {
					buttons[i].addEventListener(WCF_CLICK_EVENT, clickCallback);
				}
			}
		},
		
		/**
		 * Sends a request to delete an absence and to remove its display.
		 */
		_click: function(event) {
			var button = event.currentTarget;
			
			UiConfirmation.show({
				confirm: function() {
					Ajax.apiOnce({
						data: {
							actionName: 'absenceDelete',
							className: 'wcf\\data\\user\\AbsenceAction',
							objectIDs: [ elData(button, 'object-id') ]
						},
						success: function() {
							elRemove(button.parentNode);
							
							// remove online warning, if any
							var warning = document.getElementById('jsAbsenceWarning');
							if (warning) {
								warning.style.display = 'none';
							}
						}
					});
				},
				message: Language.get('wcf.user.absence.delete.confirm')
			});	
		}
	};
	
	return AbsenceDelete;
});
