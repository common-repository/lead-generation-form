function WLGF(wlgf_form_id, do_action) {
	//console.log(wlgf_form_id + do_action);
     
     //export form start
     var wlgf_form_id = jQuery('#wlgf-form-list').val();
	if(do_action == 'export') {
          
          var wlgf_form_name = jQuery("#wlgf-form-name").val();
          if(wlgf_form_id <= 0) {
            jQuery('#wlgf-form-list').focus();
            jQuery('#wlgf-form-list').effect('shake', {distance: 5, times: 3, });
            return false;
          }
          
          jQuery("div#wlgf-form-export").empty();
          jQuery("div#wlgf-form-export").addClass('d-none');
          jQuery("#wlgf-copycode").addClass('d-none');
          jQuery("div#wlgf-export-loader").removeClass('d-none');
          
          console.log(wlgf_form_id + do_action);
		jQuery.ajax({
			type: 'POST',
			url: ImportExport.ajaxUrl,
			data: {
                  'action': 'wlgf_import_export',
                  'do': do_action,
                  'nonce': ImportExport.nonce,
                  'wlgf_form_id': wlgf_form_id,
			}, 
			success: function (result) {
                    jQuery("div#wlgf-export-loader").addClass('d-none');
				jQuery("div#wlgf-form-export").removeClass('d-none');
                    jQuery("#wlgf-copycode").removeClass('d-none');
				jQuery("div#wlgf-form-export").append(result);
                    jQuery('#wlgf-form-list').val('0');
			},
			error: function () {
			}
		});
	}
	//export form end
     
     //import form start
      if(do_action == 'import') {
          
          var wlgf_form_name = jQuery("#wlgf-form-name").val();
          if(wlgf_form_name == "") {
            jQuery("#wlgf-form-name").focus();
            jQuery('#wlgf-form-name').effect('shake', {distance: 5, times: 3, });
            return false;
          }
          
          var wlgf_form_data = jQuery("#wlgf-form-data").val();
          if(wlgf_form_data == "") {
            jQuery("#wlgf-form-data").focus();
            jQuery('#wlgf-form-data').effect('shake', {distance: 5, times: 3, });
            return false;
          }
          
          jQuery("div#wlgf-import-loader").removeClass('d-none');
          jQuery("#wlgf-import-btn").addClass('d-none');
          console.log(wlgf_form_id + do_action);
		jQuery.ajax({
			type: 'POST',
			url: ImportExport.ajaxUrl,
			data: {
                  'action': 'wlgf_import_export',
                  'do': do_action,
                  'nonce': ImportExport.nonce,
                  'wlgf_form_name': wlgf_form_name,
                  'wlgf_form_data': wlgf_form_data,
			}, 
			success: function (result) {
                  jQuery("div#wlgf-import-loader").addClass('d-none');
                  jQuery("#wlgf-import-btn").addClass('d-none');
                  jQuery("#wlgf-import-success").removeClass('d-none');
                  jQuery("#wlgf-import-success").fadeIn(500);

                  // Automatically hide the alert message after 3 seconds (3000 milliseconds)
                  setTimeout(function() {
                        jQuery("#wlgf-import-success").fadeOut(500);
                        jQuery("#wlgf-form-name").val("");
                        jQuery("#wlgf-form-data").val("");
                        jQuery("#wlgf-import-btn").removeClass('d-none');
                  }, 2000);
			},
			error: function () {
			}
		});
	}
	//import form end
     
      //combine form start
      if(do_action == 'combine') {
          var wlgf_form_one_id = jQuery("#wlgf-form-one").val();
          if(wlgf_form_one_id <= 0) {
            jQuery("#wlgf-form-one").focus();
            jQuery('#wlgf-form-one').effect('shake', {distance: 5, times: 3, });
            return false;
          }
          
          var wlgf_form_two_id = jQuery("#wlgf-form-two").val();
          if(wlgf_form_two_id <= 0) {
            jQuery("#wlgf-form-two").focus();
            jQuery('#wlgf-form-two').effect('shake', {distance: 5, times: 3, });
            return false;
          }
          
          var wlgf_form_one_name = jQuery('#wlgf-form-one').find('option:selected').text();
          var wlgf_form_two_name = jQuery('#wlgf-form-two').find('option:selected').text();
          
          console.log(wlgf_form_one_name);
          console.log(wlgf_form_two_name);
          
          jQuery("#wlgf-combine-btn").addClass('d-none');
          jQuery("#wlgf-combine-loader").removeClass('d-none');
          jQuery.ajax({
			type: 'POST',
			url: ImportExport.ajaxUrl,
			data: {
                  'action': 'wlgf_import_export',
                  'do': do_action,
                  'nonce': ImportExport.nonce,
                  'wlgf_form_one_id': wlgf_form_one_id,
                  'wlgf_form_two_id': wlgf_form_two_id,
                  'wlgf_form_one_name': wlgf_form_one_name,
                  'wlgf_form_two_name': wlgf_form_two_name,
			}, 
			success: function (result) {
                  jQuery("#wlgf-combine-loader").addClass('d-none');
                  jQuery("#wlgf-combine-btn").addClass('d-none');
                  jQuery("#wlgf-combine-success").removeClass('d-none');
                  jQuery("#wlgf-combine-success").fadeIn(500);

                  // Automatically hide the alert message after 3 seconds (3000 milliseconds)
                  setTimeout(function() {
                        jQuery("#wlgf-combine-success").fadeOut(500);
                        jQuery('#wlgf-form-one').val('0');
                        jQuery('#wlgf-form-two').val('0');
                        jQuery("#wlgf-combine-btn").removeClass('d-none');
                  }, 5000);
			},
			error: function () {
			}
		});
      }
      //combine form end
     
     //copy code start
     if (do_action === 'copy') {
        // Get the textarea element by its ID
        var textarea = document.getElementById("wlgf-form-code");
        
        // Select the text inside the textarea
        textarea.select();
        
        // Copy the selected text to the clipboard
        document.execCommand("copy");
        
        // Deselect the text (optional)
        textarea.setSelectionRange(0, 0);
        
        // Optionally, display a success message to the user
        //alert("Code copied to clipboard!");
        
        // Show the alert message
        jQuery("#wlgf-copy-success").removeClass('d-none');
        jQuery("#wlgf-copy-success").fadeIn(500);

        // Automatically hide the alert message after 3 seconds (3000 milliseconds)
        setTimeout(function() {
            jQuery("#wlgf-copy-success").fadeOut(500);
        }, 2000);
    }
    //copy code end
}