jQuery(document).ready(function($) {
     console.log(FormGenerator.FormIdSet);
     
    // Disable all buttons on page load
    $('button').prop('disabled', true);

    // Enable buttons when the page finishes loading
    $(window).on('load', function() {
        setTimeout(function() {
            $('button').prop('disabled', false);
        }, 1000);
    });

    // dynamic load selected forms template to modify
    if(FormGenerator.FormIdSet == "set"){
        var wlgf_form_id = FormGenerator.FormId;
        $("#wlgf_saved_forms").val(wlgf_form_id);
        $("#wlgf-form-name").val(FormGenerator.FormName);
    }

    $('#wlgf-load-process').removeClass('d-none'); // show form loader
    
    // Disable the button once it dragged
    const typeUserEventsConfig = {
        button: {
            onadd: () => {
                $('.input-control[data-type=button]').css('pointer-events', 'none');
            },
            onremove: () => {
                $('.input-control[data-type=button]').css('pointer-events', '');
            },
        }
    };
    
    // Initialize the form builder with the defined configuration
    const fbEditor = document.getElementById("build-wrap");
    const templateSelect = document.getElementById("wlgf_saved_forms");
    let formBuilder;

    if(FormGenerator.FormIdSet == "set"){
        formBuilder = $(fbEditor).formBuilder({
            formData: FormGenerator.FormData,
            disabledActionButtons: ['data', 'save'],
            disableFields: ['autocomplete', 'hidden'],
            controlOrder: [ 'header', 'paragraph', 'text', 'textarea', 'select', 'radio-group', 'checkbox-group', 'date', 'number', 'file', 'button'],
            disabledAttrs: [ 'access', 'multiple', 'other', 'toggle', 'inline' ],
            disabledSubtypes: {
                paragraph: ['address', 'blockquote', 'canvas', 'output'],
                button: ['button', 'reset'],
                textarea: ['tinymce', 'quill'],
            },
            typeUserEvents: typeUserEventsConfig,
            typeUserDisabledAttrs: {
                'date': [
                    'step',
                ]
            },
        });
    } else {
        formBuilder = $(fbEditor).formBuilder({
            disabledActionButtons: ['data', 'save'],
            disableFields: ['autocomplete', 'hidden'],
            controlOrder: [ 'header', 'paragraph', 'text', 'textarea', 'select', 'radio-group', 'checkbox-group', 'date', 'number', 'file', 'button'],
            disabledAttrs: [ 'access', 'multiple', 'other', 'toggle', 'inline' ],
            disabledSubtypes: {
                paragraph: ['address', 'blockquote', 'canvas', 'output'],
                button: ['button', 'reset'],
                textarea: ['tinymce', 'quill'],
            },
            typeUserEvents: typeUserEventsConfig,
            typeUserDisabledAttrs: {
                'date': [
                    'step',
                ]
            },
        });
    }

    // hide pre-loader
    setTimeout(function() {
        $('#wlgf-load-process').addClass('d-none');
    }, 1500);
    
    // save form on click on save/modify button
    $('body').on('click', '.wlgf-action-button', function() {
        
        var wlgf_form_do_action = $(this).val();
        
        if(wlgf_form_do_action == 'wlgf_save_form'){
            var wlgf_form_id = 0;
            var wlgf_form_nonce = FormGenerator.SaveNonce;
        } else {
            var wlgf_form_id = $("input#wlgf-modify-form-id").val();
            var wlgf_form_nonce = FormGenerator.ModifyNonce;
        }
        console.log(wlgf_form_id);
        console.log(wlgf_form_do_action);
        var wlgf_form_name = $('#wlgf-form-name').val();
        if(wlgf_form_name == "") {
            $("#wlgf-form-name").focus();
            return false;
        }
        
        var wlgf_formdata_array = formBuilder.actions.getData('json', true);
        var wlgf_jsondata = JSON.stringify(wlgf_formdata_array);
        console.log(wlgf_jsondata);
        
        var wlgf_parsed_array = JSON.parse(wlgf_formdata_array);
        if (wlgf_parsed_array.length === 0) {
            $("button#wlgf-save-form").effect( "shake", {times:1}, 300 );
            return false;
        }
        
        $('button#wlgf-save-form').addClass('d-none');
        $('button#wlgf-modify-form').addClass('d-none');
        $('div#wlgf-save-process').removeClass('d-none');					
        
        $.ajax({
            type: 'POST',
            url: FormGenerator.ajaxUrl,
            data: {
                'action': 'wlgf_save_form',
                'id': wlgf_form_id,
                'name': wlgf_form_name,
                'form': wlgf_jsondata,
                'nonce': wlgf_form_nonce,
            }, 
            success: function (result) {
                // hide loading start
                setTimeout(function() {
                    $('div#wlgf-button-div').empty();
                    $('div#wlgf-save-process').addClass('d-none');
                    $('button#wlgf-save-form').removeClass('d-none');
                    $('div#wlgf-button-div').append(result);
                }, 1500);
            },
            error: function () {
                //alert("error");
            }
        });
    }); // end save
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
}