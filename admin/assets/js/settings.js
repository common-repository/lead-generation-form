jQuery(document).ready(function() {
	// Disable all buttons on page load
	jQuery('button').prop('disabled', true);

	// Enable buttons when the page finishes loading
	jQuery(window).on('load', function() {
		jQuery(function() {
			// it will wait for 1.5 sec.
			setTimeout(function() {
				jQuery('button').prop('disabled', false);
			}, 1000);
		});
		
		// onload recaptcha and email engine start
		var recaptcha_status = jQuery('#recaptcha').val();
		if(recaptcha_status == 1){
			jQuery('div#site-key').removeClass('d-none');
			jQuery('div#secret-key').removeClass('d-none');
		} else {
			jQuery('div#site-key').addClass('d-none');
			jQuery('div#secret-key').addClass('d-none');
		}
		
		var notify_admin = jQuery('#notify_admin').val();
		if(notify_admin == 2){
			jQuery('div.email').addClass('d-none');
		} else {
			jQuery('div.email').removeClass('d-none');
		}
		
		var email_engine = jQuery('#email_engine').val();
		if(email_engine == 2){
			jQuery('div.smtp').removeClass('d-none');
		} else {
			jQuery('div.smtp').addClass('d-none');
		}
		// onload recaptcha and email engine end
	});
});


// onchange recaptcha and email engine start
jQuery('#recaptcha').change(function() {
	// Get the selected value
	var recaptcha_status = jQuery('#recaptcha').val();
	if(recaptcha_status == 1){
		jQuery('div#site-key').removeClass('d-none');
		jQuery('div#secret-key').removeClass('d-none');
	} else {
		jQuery('div#site-key').addClass('d-none');
		jQuery('div#secret-key').addClass('d-none');
	}
});

jQuery('#notify_admin').change(function() {
	// Get the selected value
	var notify_admin = jQuery('#notify_admin').val();
	if(notify_admin == 2){
		jQuery('div.email').addClass('d-none');
		jQuery('div.smtp').addClass('d-none');
	} else {
		jQuery('div.email').removeClass('d-none');
		var email_engine = jQuery('#email_engine').val();
		if(email_engine == 2){
			jQuery('div.smtp').removeClass('d-none');
		} else {
			jQuery('div.smtp').addClass('d-none');
		}
	}
});

jQuery('#email_engine').change(function() {
	// Get the selected value
	var email_engine = jQuery('#email_engine').val();
	if(email_engine == 2){
		jQuery('div.smtp').removeClass('d-none');
	} else {
		jQuery('div.smtp').addClass('d-none');
	}
});
// onchange recaptcha and email engine end

function wlgf_save_setting(){
	
	var wlgf_recaptcha = jQuery('#recaptcha').val();
	var wlgf_sitekey = jQuery('#sitekey').val();
	var wlgf_secretkey = jQuery('#secretkey').val();
	if(wlgf_recaptcha == 1){
		if(wlgf_sitekey == ""){
			jQuery('#sitekey').focus();
			return false;
		}
		if(wlgf_secretkey == ""){
			jQuery('#secretkey').focus();
			return false;
		}
	}
	
	var user_message = jQuery('#user_message').val();
	if(user_message == ""){
		jQuery('#user_message').focus();
		return false;
	}
	
	var notify_admin = jQuery('#notify_admin').val();
	var email_engine = jQuery('#email_engine').val();
	var smtp_host = jQuery('#smtp_host').val();
	var smtp_username = jQuery('#smtp_username').val();
	var smtp_password = jQuery('#smtp_password').val();
	var smtp_encryption = jQuery('#smtp_encryption').val();
	var smtp_port = jQuery('#smtp_port').val();
	if(notify_admin == 1){
		if(email_engine == 2){ // smtp selected
			if(smtp_host == ""){
				jQuery('#smtp_host').focus();
				return false;
			}
			if(smtp_username == ""){
				jQuery('#smtp_username').focus();
				return false;
			}
			if(smtp_password == ""){
				jQuery('#smtp_password').focus();
				return false;
			}
			if(smtp_port == ""){
				jQuery('#smtp_port').focus();
				return false;
			}
		}
	}

	jQuery('button#save-settings').addClass('d-none');
	jQuery('div#wlgf-save-process').removeClass('d-none');
	
	jQuery.ajax({
		type: 'POST',
		url: Settings.ajaxUrl,
		data: {
			'action': 'wlgf_save_settings',
			'recaptcha': wlgf_recaptcha,
			'sitekey': wlgf_sitekey,
			'secretkey': wlgf_secretkey,
			
			'notify_admin': notify_admin,
			'email_engine': email_engine,
			'smtp_host': smtp_host,
			'smtp_username': smtp_username,
			'smtp_password': smtp_password,
			'smtp_encryption': smtp_encryption,
			'smtp_port': smtp_port,
			
			'user_message': user_message,
			
			'nonce': Settings.nonce,
		}, 
		success: function (result) {
			// hide loading start
			jQuery(function() {
				// it will wait for 5 sec.
				setTimeout(function() {
					jQuery('div#wlgf-save-process').addClass('d-none');
					jQuery('button#save-settings').removeClass('d-none');
				}, 1500);
			});
			// hide loading end
		},
		error: function () {
			//alert("error");
		}
	});
}