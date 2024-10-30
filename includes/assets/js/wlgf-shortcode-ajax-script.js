jQuery(document).ready(function($) {
    // Bind the submit event to the form
    $('#wlgf-form-' + WLGFAjax.FormId).on('submit', function(e) {

        $('#wlgf-response-output').empty();
        $('#wlgf-response-output').hide();
        jQuery('button').prop('disabled', true);
        $('.wlgf-spinner').show();
        e.preventDefault(); // Prevent the default form submission
        
        // Date fields validation start
        var isValid = true; // Flag to track overall validity
        var errorFieldName = ""; // Store error field name
        var errorMessage = ""; // Store error messages
        var dateFields = $(this).find('input[type="date"]');
        dateFields.each(function() {
            var dateValue = $(this).val(); // Get the value of the current date field
            var dobPattern = /^\d{4}-\d{2}-\d{2}$/; // Regex for YYYY-MM-DD format

            // Validate the date format
            if (!dobPattern.test(dateValue)) {
                isValid = false; // Set validity to false
                errorFieldName = $(this).attr('name'); // Store the name of the invalid field
                errorMessage += "Please enter a valid date in the format YYYY-MM-DD for field: " + errorFieldName + "\n";
            }
            // Additional validation can go here (e.g., checking if the date is not in the future)
        });
        if (!isValid) {
            // Focus on the first invalid date field
            $(this).find('input[type="date"][name="' + errorFieldName + '"]').focus();
            jQuery('button').prop('disabled', false);
            $('.wlgf-spinner').hide();
            return; // Stop the form submission
        }
        // Date fields validation end

        var formData = new FormData(this); // Create a FormData object from the form

        $.ajax({
            type: 'POST',
            //url: ajax_object.ajax_url, // Use localized AJAX URL
            url: window.location.href, // Use the current URL
            data: formData, // Send the FormData object
            processData: false, // Important: prevent jQuery from automatically transforming the data into a query string
            contentType: false, // Important: set the content type to false to let the browser set it
            success: function(response) {
                jQuery(function() {
                    // it will wait for 1.5 sec and then will fire
                    setTimeout(function() {
                    // hide processing icon and show button
                        $('.wlgf-spinner').hide();
                        $('#wlgf-form-' + WLGFAjax.FormId).trigger('reset'); // Reset the form
                        jQuery('button').prop('disabled', false);
                        $('#wlgf-response-output').show();
                        $('#wlgf-response-output').append(WLGFAjax.UserMsg); // Append new text
                    }, 1500);
                });
            },
            error: function() {
                
            }
        });
    });
});