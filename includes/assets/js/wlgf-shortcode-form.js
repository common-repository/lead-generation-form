jQuery(document).ready(function($) {
     
     //console.log(WLGFShortcode.FormId);
     
     // Disable all buttons on load
     jQuery('button').prop('disabled', true);
     // Enable buttons when the page finishes loading
     jQuery(window).on('load', function() {
          jQuery(function() {
               // it will wait for 1.5 sec.
               setTimeout(function() {
                    jQuery('button').prop('disabled', false);
               }, 1000);
          });
     });
     
    /* $('#'+ WLGFShortcode.FormId).submit(function(event) {
        event.preventDefault(); // Prevent the form from submitting immediately
        var form = this;

        $('button[type="submit"]', form).prop('disabled', true); // Disable the submit button specifically

        // Assuming you want to do something (like an AJAX call) before the actual form submission
        setTimeout(function() {
            $('button[type="submit"]', form).focus();
            form.submit();
        }, 1000); // Adjust the delay as needed
    });*/
    
     // prevent resubmit the form
     var isSubmitting = false; // Flag to prevent resubmission

     $('#'+ WLGFShortcode.FormId).on('submit', function(event) {
          if (isSubmitting) {
               event.preventDefault(); // Prevent resubmission
               return false; // Stop execution
          }

          // Set the flag to true and disable the submit button
          isSubmitting = true;
          $('#submit-1').attr('disabled', true);
     });
     
     // Focus on message after form submit
     setTimeout(function() {
          // Change the display to 'none' to make the div disappear
          jQuery('div#wlgf-msg').focus();
          //jQuery('#wlgf-msg').css('display', 'none');
          jQuery('#wlgf-msg').fadeOut('slow');
     }, 3000);
     
     // bootstrap tooltip
     //const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
     //const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    
});

// JavaScript
document.addEventListener('DOMContentLoaded', function() {
     const rangeInputs = document.querySelectorAll('input[type="range"]');

     rangeInputs.forEach(function(rangeInput) {
          const display = document.getElementById('range-value-display-' + rangeInput.id);

          // Set initial display value
          if (display) {
               display.textContent = rangeInput.value; // Initial value display
          }

          // Update display value on input change
          rangeInput.addEventListener('input', function() {
               display.textContent = this.value; // Update the displayed value
          });
     });
});


// back button does not take the user back to the form submission.
if ( window.history.replaceState ) {
     window.history.replaceState( null, null, window.location.href );
}
