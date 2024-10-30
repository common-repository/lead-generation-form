function WLGF(wlgf_form_id, do_action, wlgf_row_id) {
	console.log(wlgf_form_id);
	console.log(do_action);
	console.log(wlgf_row_id);
	if(wlgf_form_id > 0){
		if(do_action == 'load') {
			jQuery("div#wlgf-table-container").empty();
			jQuery('div#wlgf-loader-process').removeClass('d-none');
			
			jQuery.ajax({
				type: 'POST',
				url: Leads.ajaxUrl,
				data: {
					'action': 'wlgf_lead_loader',
					'do': do_action,
					'wlgf_form_id': wlgf_form_id,
					'nonce': Leads.nonce,
				}, 
				success: function (result) {
					// hide loading start
					jQuery(function() {
						// it will wait for 5 sec.
						setTimeout(function() {
							jQuery('div#wlgf-loader-process').addClass('d-none');
							jQuery("div#wlgf-table-container").append(result);
							jQuery('#forms-tables').DataTable({
								'aoColumnDefs': [{
									'bSortable': false,
									'aTargets': [-1, -2] // 1st one, start by the right
								}]
							});
						}, 1500);
					});
					// hide loading end
				},
				error: function () {
					//alert("error");
				}
			});
		}
		// end load
		
		// delete single lead
		if(do_action == 'delete') {
			if(confirm('Are you sure to delete lead?')){
				jQuery.ajax({
					type: 'POST',
					url: Leads.ajaxUrl,
					data: {
						'action': 'wlgf_lead_loader',
						'do': do_action,
						'wlgf_form_id': wlgf_form_id,
						'wlgf_row_id': wlgf_row_id,
						'nonce': Leads.nonce,
					}, 
					success: function (result) {
						jQuery("tr#"+wlgf_row_id).fadeOut('1500');
						jQuery("tr#"+wlgf_row_id).remove('1600');
					},
					error: function () {
						//alert("error");
					}
				});
			}
		}
		
		//delete multiple selected leads
		if(do_action == 'multiple') {
			var wlgf_lead_ids = [];
			if(confirm('Are you sure to delete selected leads?')){
				
				jQuery("input:checkbox[name=wlgf-lead-id]:checked").each(function() {
					wlgf_lead_ids.push(jQuery(this).val());
					
					//hide selected table row on multiple gallery delete
					jQuery("tr#" + jQuery(this).val()).fadeOut('1500');
					jQuery("tr#" + jQuery(this).val()).remove('1600');
				});
				console.log(wlgf_lead_ids);
				
				jQuery.ajax({
					type: 'POST',
					url: Leads.ajaxUrl,
					data: {
						'action': 'wlgf_lead_loader',
						'do': do_action,
						'wlgf_form_id': wlgf_form_id,
						'wlgf_row_id': wlgf_lead_ids,
						'nonce': Leads.nonce,
					}, 
					success: function (result) {
					},
					error: function () {
					}
				});
			}
		}
		
		
	} else {
		jQuery("div#wlgf-table-container").empty();
		jQuery('#wlgf-form-list').focus();
		return false;
	}
}

//select all leads
jQuery(document).on('click', '#wlgf-select-all', function() {
	console.log('checked');
	jQuery('input:checkbox').not(this).prop('checked', this.checked);
});

/* function wlgf_select_all_leads(){
	jQuery('input:checkbox').not(this).prop('checked', this.checked);
} */
jQuery('#forms-tables').DataTable({
   'aoColumnDefs': [{
	  'bSortable': false,
	  'aTargets': [-1, -2] // 1st one, start by the right
   }]
});