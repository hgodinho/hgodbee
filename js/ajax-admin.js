jQuery(document).ready(function ($) {
	$('#get_token').click(function () {
		var ajaxURL = hgodbee_object.ajax_url;
		var data = {
			action: 'hgodbee_token',
			nonce: hgodbee_object.nonce_admin,
        };
		$.post(ajaxURL, data, function (response) {
			var notificationArea = document.getElementById('notification-area');
			notificationArea.innerHTML = response;
		});
	});
});
