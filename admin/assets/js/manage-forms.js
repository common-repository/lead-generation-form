jQuery(document).ready(function() {
	jQuery('#forms-tables').DataTable();
});

function WLGF(wlgf_form_id, do_action) {
	console.log(wlgf_form_id + do_action);
	
	//copy short code form start
	if(do_action == 'copy') {
		var copyShortcode = document.getElementById("wlgf-shortcode-" + wlgf_form_id);
		copyShortcode.select();
		document.execCommand('copy');
		//fade in and out copied message
		jQuery('.wlgf-copied-' + wlgf_form_id).removeClass('d-none');
		jQuery('.wlgf-copied-' + wlgf_form_id).fadeIn('2000', 'linear');
		jQuery('.wlgf-copied-' + wlgf_form_id).fadeOut(3000,'swing');
	}
	//copy short code form end
	
	//clone form start
	if(do_action == 'clone') {
		jQuery.ajax({
			type: 'POST',
			url: ManageForms.ajaxUrl,
			data: {
				'action': 'wlgf_manage_form_action',
				'do': do_action,
				'nonce': ManageForms.nonce,
				'wlgf_form_id': wlgf_form_id,
			}, 
			success: function (result) {
				jQuery("tbody#wlgf-tbody").append(result);
			},
			error: function () {
			}
		});
	}
	//clone form end
	
	//delete form start
	if(do_action == 'delete') {
		var secondColumnText = jQuery('tr#'+ wlgf_form_id +' td:nth-child(2)').text();
		if(confirm('Are you sure to delete "'+ secondColumnText +'" form? \nIMPORTANT NOTE:\nAll lead captured through form "'+ secondColumnText +'" will be deleted.')){
			jQuery.ajax({
				type: 'POST',
				url: ManageForms.ajaxUrl,
				data: {
					'action': 'wlgf_manage_form_action',
					'do': do_action,
					'nonce': ManageForms.nonce,
					'wlgf_form_id': wlgf_form_id,
				}, 
				success: function (result) {
					jQuery("tr#"+wlgf_form_id).fadeOut('1500');
					jQuery("tr#"+wlgf_form_id).remove('1600');
				},
				error: function () {
				}
			});
		}
	}
	//delete form end
}