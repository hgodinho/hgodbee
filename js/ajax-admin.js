jQuery(document).ready(function ($) {
	$('#get_token').click(function () {
		var ajaxURL = hgodbee_object.ajax_url;
		var data = {
			action: 'token_bee',
			nonce: hgodbee_object.nonce_admin,
        };
        console.log(data);
		$.post(ajaxURL, data, function (response) {
			var notificationArea = document.getElementById('notification_area');
			notificationArea.innerHTML = response;
		});
	});
});
