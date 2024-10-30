<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; /* Exit if accessed directly */
}

add_shortcode( 'WLFG', 'wlgf_shortcode_callback' );
function wlgf_shortcode_callback( $atts ) {
	if ( isset( $atts['id'] ) ) {
          ob_start();
          
          // get form id and load form structure
          $wlgf_form_id = sanitize_text_field($atts['id']);
		$wlgf_form_data = get_option('wlgf_form_'.$wlgf_form_id);
		if ($wlgf_form_data !== false) {
			$json_string = $wlgf_form_data['form'];
			$wlgf_form_data = json_decode($json_string, true);
		} else {
			echo 'Sorry! Form does not exist.';
			$wlgf_form_data = array();
		}
          
          // defaults
          $wlgf_blacklist_flag = 0;
          $wlgf_form_nonce = wp_create_nonce( 'wlgf-form-post' );
          $wlgf_checkbox_count = 0;
		$wlgf_checkbox_fields = array();
		$wlgf_cb_names = array();
		$wlgf_tooltip = array(
		    'span' => array(
			   'class' => array(),
			   'data-bs-toggle' => array(),
			   'data-bs-placement' => array(),
			   'title' => array()
		    )
		);
          $wlgf_loader = plugin_dir_url(__FILE__) . 'assets/img/wlgf-loader.gif';
          
          // custom parameters - add to reply, blacklist
		$wlgf_blacklist = (isset($atts['blacklist'])) ? sanitize_text_field($atts['blacklist']) : sanitize_text_field("not-match");
		$wlgf_AddReplyToFieldName = (isset($atts['reply_field_name'])) ? sanitize_text_field($atts['reply_field_name']) : "";
		$wlgf_AddReplyToFieldEmail = (isset($atts['reply_field_email'])) ? sanitize_text_field($atts['reply_field_email']) : "";
		$wlgf_settings = get_option('wlgf_settings'); //load settings
           $wlgf_recaptcha = (isset($wlgf_settings['recaptcha'])) ? sanitize_text_field($wlgf_settings['recaptcha']) : 2;
		 $wlgf_sitekey = (isset($wlgf_settings['sitekey'])) ? sanitize_text_field($wlgf_settings['sitekey']) : '';
		 $wlgf_secretkey = (isset($wlgf_settings['secretkey'])) ? sanitize_text_field($wlgf_settings['secretkey']) : '';
           $wlgf_user_message = (isset($wlgf_settings['user_message'])) ? sanitize_text_field($wlgf_settings['user_message']) : '';
          
          // load plugin styles and scripts
          wp_enqueue_style('wlgf-shortcode-form');
		wp_enqueue_script('jquery');
		wp_enqueue_script('wlgf-shortcode-form-js', plugin_dir_url(__FILE__). 'assets/js/wlgf-shortcode-form.js', array('jquery'), null, true); // load in footer
		wp_add_inline_script( 'wlgf-shortcode-form-js', 'const WLGFShortcode = ' . wp_json_encode( array(
		    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		    'FormId' => esc_js($wlgf_form_id),
		)), 'before' ); // ajax script dynamic parameters
          
          wp_enqueue_script('wlgf-shortcode-ajax-script');
          //wp_enqueue_script('wlgf-shortcode-ajax-script', plugin_dir_url(__FILE__) . 'assets/js/wlgf-shortcode-ajax-script.js', array('jquery'), null, true); // ajax script
          wp_add_inline_script( 'wlgf-shortcode-form-js', 'const WLGFAjax = ' . wp_json_encode( array(
		    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		    'FormId' => esc_js($wlgf_form_id),
		    'UserMsg' => esc_js($wlgf_user_message),
		)), 'before' ); // ajax script dynamic parameters
          
		if($wlgf_recaptcha == 1 && $wlgf_sitekey && $wlgf_secretkey) {
	    		wp_enqueue_script('wlgf-google-recaptcha', 'https://www.google.com/recaptcha/api.js?render=' . esc_attr($wlgf_sitekey), array(), null, true);
		}
          
          // if reCAPTCHA is enabled - add reCAPTCHA field
		if($wlgf_recaptcha == 1 ){
			$wlgf_form_data[] = array("type" => "recaptcha", 'sitekey' => $wlgf_sitekey);
		}
          
          /* echo "<hr>";
		echo "<pre>";
		print_r($wlgf_form_data);
		echo "</pre>";
		echo "<hr>"; */
		
          // form structure output start
          echo "<div class='wlgf-container'>";
               echo "<form id='" . esc_attr("wlgf-form-$wlgf_form_id") . "' method='post' enctype='multipart/form-data'>";
               
               foreach ($wlgf_form_data as $field) {
                    switch ($field['type']) {
                         case "text":
                              $placeholder = isset($field['placeholder']) ? esc_attr($field['placeholder']) : '';
                              $maxLength = isset($field['maxlength']) ? esc_attr($field['maxlength']) : '';
                              $wlgf_field_value = isset($field['value']) ? esc_attr($field['value']) : '';
                              $required = (isset($field['required']) && $field['required'] == 1) ? "required" : '';
                              $required_astrick = (isset($field['required']) && $field['required'] == 1) ? "*" : '';
                              $wlgfDescription = isset($field['description']) ? "<span class='bg-light text-dark px-1 my-1' data-bs-toggle='tooltip' data-bs-placement='right' title='" . esc_attr($field['description']) . "'>?</span>" : '';
                              
                              // Determine the subtype
                              $inputType = 'text';
                              if (isset($field['subtype'])) {
                                   $subType = esc_attr($field['subtype']);
                                   if (in_array($subType, array('password', 'email', 'color', 'tel'))) {
                                        $inputType = $subType;
                                   }
                              }

                              // Sanitize and escape output
                              $fieldName = esc_attr($field['name']);
                              $fieldLabel = isset($field['label']) ? esc_html($field['label']) : '';
                              $fieldClassName = isset($field['className']) ? esc_html($field['className']) : '';

                              echo "<div class='mb-3'>";
                              echo "<label for='" . esc_attr( $fieldName ) . "' class='form-label'>" . esc_html( $required_astrick ) . " " . esc_html( $fieldLabel ) . " " . wp_kses( $wlgfDescription, $wlgf_tooltip ) . "</label>";
                              echo "<input type='" . esc_attr( $inputType ) . "' id='" . esc_attr( $fieldName ) . "' name='" . esc_attr( $fieldName ) . "' class='" . esc_attr( $fieldClassName ) . "' value='" . esc_attr( $wlgf_field_value ) . "' placeholder='" . esc_attr( $placeholder ) . "' maxlength='" . esc_attr( $maxLength ) . "' " . esc_attr( $required ) . ">";
                              echo "</div>";
                              break;

                         case "number":
                              $placeholder = isset($field['placeholder']) ? esc_attr($field['placeholder']) : '';
                              $min = isset($field['min']) ? esc_attr($field['min']) : '';
                              $max = isset($field['max']) ? esc_attr($field['max']) : '';
                              $step = isset($field['step']) ? esc_attr($field['step']) : '';
                              $wlgf_field_value = isset($field['value']) ? esc_attr($field['value']) : '';
                              $required = (isset($field['required']) && $field['required'] == 1) ? "required" : '';
                              $required_astrick = (isset($field['required']) && $field['required'] == 1) ? "*" : '';
                              $wlgfDescription = isset($field['description']) ? "<span class='bg-light text-dark px-1 my-1'  data-bs-toggle='tooltip' data-bs-placement='right' title='" . esc_attr($field['description']) . "'>?</span>" : '';

                              // Determine the subtype
                              $inputType = 'number';
                              if (isset($field['subtype'])) {
                                   $subType = esc_attr($field['subtype']);
                                   if (in_array($subType, array('range'))) {
                                        $inputType = $subType;
                                   }
                              }

                              // Sanitize and escape output
                              $fieldName = esc_attr($field['name']);
                              $fieldLabel = isset($field['label']) ? esc_html($field['label']) : '';
                              $fieldClassName = isset($field['className']) ? esc_html($field['className']) : '';

                              echo "<div class='mb-3'>";
                              echo "<label for='" . esc_attr($fieldName) . "' class='form-label'>" . esc_html($required_astrick) . " " . esc_html($fieldLabel) . " " . wp_kses($wlgfDescription, $wlgf_tooltip) . "</label>";
                              // If the input type is range, display the range input and its value
                              if ($inputType === 'range') {
                                   // Display the selected range value
                                   echo "<div class='range-value-display' id='range-value-display-" . esc_attr($fieldName) . "' '>" . esc_html($wlgf_field_value) . "</div>";
                                   echo "<input type='" . esc_attr($inputType) . "' id='" . esc_attr($fieldName) . "' name='" . esc_attr($fieldName) . "' class='" . esc_attr($fieldClassName) . "' value='" . esc_attr($wlgf_field_value) . "' placeholder='" . esc_attr($placeholder) . "' min='" . esc_attr($min) . "' max='" . esc_attr($max) . "' step='" . esc_attr($step) . "' " . esc_attr($required) . ">";
                              } else {
                                   echo "<input type='" . esc_attr($inputType) . "' id='" . esc_attr($fieldName) . "' name='" . esc_attr($fieldName) . "' class='" . esc_attr($fieldClassName) . "' value='" . esc_attr($wlgf_field_value) . "' placeholder='" . esc_attr($placeholder) . "' min='" . esc_attr($min) . "' max='" . esc_attr($max) . "' step='" . esc_attr($step) . "' " . esc_attr($required) . ">";
                              }
                              echo "</div>";
                         break;

                         
                         case "date":
                              $placeholder = isset($field['placeholder']) ? esc_attr($field['placeholder']) : '';
                              $wlgf_field_value = isset($field['value']) ? esc_attr($field['value']) : '';
                              $required = (isset($field['required']) && $field['required'] == 1) ? "required" : '';
                              $required_astrick = (isset($field['required']) && $field['required'] == 1) ? "*" : '';
                              $wlgfDescription = isset($field['description']) ? "<span class='bg-light text-dark px-1 my-1'  data-bs-toggle='tooltip' data-bs-placement='right' title='" . esc_attr($field['description']) . "'>?</span>" : '';
                              
                              // Sanitize and escape output
                              $fieldName = esc_attr($field['name']);
                              $fieldLabel = isset($field['label']) ? esc_html($field['label']) : '';
                              $fieldClassName = isset($field['className']) ? esc_html($field['className']) : '';
                              
                              echo "<div class='mb-3'>";
                              echo "<label for='" . esc_attr($fieldName) . "' class='form-label'>" . esc_html($required_astrick) . " " . esc_html($fieldLabel) . " " . wp_kses( $wlgfDescription, $wlgf_tooltip ) . "</label>";
                              echo "<input type='date' id='" . esc_attr($fieldName) . "' name='" . esc_attr($fieldName) . "' class='" . esc_attr($fieldClassName) . "' value='" . esc_attr($wlgf_field_value) . "' placeholder='" . esc_attr($placeholder) . "' " . esc_attr($required) . ">";
                              echo "</div>";
                              break;

                         case "file":
                              $multiple = isset($field['multiple']) && $field['multiple'] ? 'multiple' : '';
                              $wlgf_field_value = isset($field['value']) ? esc_attr($field['value']) : '';
                              $required = (isset($field['required']) && $field['required'] == 1) ? "required" : '';
                              $required_astrick = (isset($field['required']) && $field['required'] == 1) ? "*" : '';
                              $wlgfDescription = isset($field['description']) ? "<span class='bg-light text-dark px-1 my-1'  data-bs-toggle='tooltip' data-bs-placement='right' title='" . esc_attr($field['description']) . "'>?</span>" : '';

                              // Sanitize and escape output
                              $fieldName = esc_attr($field['name']);
                              $fieldLabel = isset($field['label']) ? esc_html($field['label']) : '';
                              $fieldClassName = isset($field['className']) ? esc_html($field['className']) : '';

                              echo "<div class='mb-3'>";
                              echo "<label for='" . esc_attr($fieldName) . "' class='form-label'>" . esc_html($required_astrick) . " " . esc_html($fieldLabel) . " " . wp_kses( $wlgfDescription, $wlgf_tooltip ) . "</label>";
                              echo "<input type='file' id='" . esc_attr($fieldName) . "' name='" . esc_attr($fieldName) . "' class='" . esc_attr($fieldClassName) . "' value='" . esc_attr($wlgf_field_value) . "' " . esc_attr($multiple) . " " . esc_attr($required) . ">";
                              echo "</div>";
                              break;

                         case "textarea":
                              $placeholder = isset($field['placeholder']) ? esc_attr($field['placeholder']) : '';
                              $maxlength = isset($field['maxlength']) ? esc_attr($field['maxlength']) : '';
                              $rows = isset($field['rows']) ? esc_attr($field['rows']) : '';
                              $wlgf_field_value = isset($field['value']) ? esc_attr($field['value']) : '';
                              $required = (isset($field['required']) && $field['required'] == 1) ? "required" : '';
                              $required_astrick = (isset($field['required']) && $field['required'] == 1) ? "*" : '';
                              $wlgfDescription = isset($field['description']) ? "<span class='bg-light text-dark px-1 my-1'  data-bs-toggle='tooltip' data-bs-placement='right' title='" . esc_attr($field['description']) . "'>?</span>" : '';

                              // Sanitize and escape output
                              $fieldName = esc_attr($field['name']);
                              $fieldLabel = isset($field['label']) ? esc_html($field['label']) : '';
                              $fieldClassName = isset($field['className']) ? esc_html($field['className']) : '';

                              echo "<div class='mb-3'>";
                              echo "<label for='" . esc_attr($fieldName) . "' class='form-label'>" . esc_html($required_astrick) . " " . esc_html($fieldLabel) . " " . wp_kses( $wlgfDescription, $wlgf_tooltip ) . "</label>";
                              echo "<textarea id='" . esc_attr($fieldName) . "' name='" . esc_attr($fieldName) . "' class='" . esc_attr($fieldClassName) . "' placeholder='" . esc_attr($placeholder) . "' rows='" . esc_attr($rows) . "' maxlength='" . esc_attr($maxlength) . "' " . esc_attr($required) . ">" . esc_textarea($wlgf_field_value) . "</textarea>";
                              echo "</div>";
                              break;

                         case "select":
                              $required = (isset($field['required']) && $field['required'] == 1) ? "required" : '';
                              $required_astrick = (isset($field['required']) && $field['required'] == 1) ? "*" : '';

                              // Sanitize and escape output
                              $fieldName = esc_attr($field['name']);
                              $fieldLabel = isset($field['label']) ? esc_html($field['label']) : '';
                              $fieldClassName = isset($field['className']) ? esc_html($field['className']) : '';
                              $wlgfDescription = isset($field['description']) ? "<span class='bg-light text-dark px-1 my-1'  data-bs-toggle='tooltip' data-bs-placement='right' title='" . esc_attr($field['description']) . "'>?</span>" : '';

                              $placeholder = isset($field['placeholder']) ? esc_html($field['placeholder']) : "Select An Option";

                              echo "<div class='mb-3'>";
                              echo "<label for='" . esc_attr($fieldName) . "' class='form-label'>" . esc_html($required_astrick) . " " . esc_html($fieldLabel) . " " . wp_kses( $wlgfDescription, $wlgf_tooltip ) . "</label>";
                              echo "<select id='" . esc_attr($fieldName) . "' name='" . esc_attr($fieldName) . "' class='" . esc_attr($fieldClassName) . "' " . esc_attr($required) . ">";
                              // Add the placeholder as the first option
                              echo "<option value='' disabled selected>" . esc_html($placeholder) . "</option>";
                              foreach ($field['values'] as $option) {
                                   $optionValue = esc_attr($option['value']);
                                   $optionLabel = esc_html($option['label']);
                                   $selected = isset($option['selected']) && $option['selected'] ? "selected" : "";
                                   echo "<option value='" . esc_attr($optionValue) . "' " . esc_attr($selected) . ">" . esc_html($optionLabel) . "</option>";
                              }
                              echo "</select>";
                              echo "</div>";
                              break;
                         
                         case "checkbox-group":
                              $required = (isset($field['required']) && $field['required'] == 1) ? "required" : '';
                              $required_astrick = (isset($field['required']) && $field['required'] == 1) ? "*" : '';
                              // Sanitize and escape output
                              $fieldName = esc_attr($field['name']);
                              $fieldLabel = isset($field['label']) ? esc_html($field['label']) : '';
                              $fieldClassName = isset($field['className']) ? esc_html($field['className']) : '';
                              $wlgfDescription = isset($field['description']) ? "<span class='bg-light text-dark px-1 my-1'  data-bs-toggle='tooltip' data-bs-placement='right' title='" . esc_attr($field['description']) . "'>?</span>" : '';

                              echo "<div class='mb-3 wlgf-cb-group'>";
                              echo "<label>" . esc_html($required_astrick) . " " . esc_html($fieldLabel) . " " . wp_kses( $wlgfDescription, $wlgf_tooltip ) . "</label><br>";
                              $wlgf_cbc = 1;
                              foreach ($field['values'] as $checkbox) {
                                   $checkboxValue = esc_attr($checkbox['value']);
                                   $checkboxLabel = esc_html($checkbox['label']);
                                   $checked = isset($checkbox['selected']) && $checkbox['selected'] ? "checked" : "";
                                   echo "<input type='checkbox' class='form-check-input' id='" . esc_attr($fieldName . '[' . $wlgf_cbc . ']') . "' name='" . esc_attr($fieldName . '[' . $wlgf_cbc . ']') . "' value='" . esc_attr($checkboxValue) . "' " . esc_attr($checked) . "> ";
                                   echo "<label class='form-check-label' for='" . esc_attr($fieldName . '[' . $wlgf_cbc . ']') . "'>" . esc_html($checkboxLabel) . "</label><br>";
                                   $wlgf_cb_names[] = $fieldName.'['.$wlgf_cbc.']';
                                   $wlgf_cbc++;
                              }
                              $wlgf_checkbox_fields[] = array('field_name' => $fieldName, 'required' => $required, 'names' => $wlgf_cb_names);
                              echo "</div>";
                              unset($wlgf_cb_names);
                              $wlgf_checkbox_count++;
                              break;


                         case "radio-group":
                              $required = (isset($field['required']) && $field['required'] == 1) ? "required" : '';
                              $required_astrick = (isset($field['required']) && $field['required'] == 1) ? "*" : '';
                              $wlgfDescription = isset($field['description']) ? "<span class='bg-light text-dark px-1 my-1'  data-bs-toggle='tooltip' data-bs-placement='right' title='" . esc_attr($field['description']) . "'>?</span>" : '';

                              // Sanitize and escape output
                              $fieldName = esc_attr($field['name']);
                              $fieldLabel = isset($field['label']) ? esc_html($field['label']) : '';
                              $fieldClassName = isset($field['className']) ? esc_html($field['className']) : '';
                              echo "<div class='mb-3 wlgf-rd-group'>";
                              echo "<label class='form-label'>" . esc_html($required_astrick) . " " . esc_html($fieldLabel) . " " . wp_kses( $wlgfDescription, $wlgf_tooltip ) . "</label><br>";
                              foreach ($field['values'] as $radio) {
                                   $radioValue = esc_attr($radio['value']);
                                   $radioLabel = esc_html($radio['label']);
                                   $checked = isset($radio['selected']) && $radio['selected'] ? "checked" : "";
                                   echo "<input type='radio' name='" . esc_attr($fieldName) . "' value='" . esc_attr($radioValue) . "' " . esc_attr($checked) . " " . esc_attr($required) . "> " . esc_html($radioLabel) . "<br>";
                              }
                              echo "</div>";
                              break;


                         case "header":
                              echo "<div class='mb-3'>";
                              // Ensure the subtype is a valid header HTML tag
                              $allowed_header_tags = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6');
                              $subtype = in_array($field['subtype'], $allowed_header_tags) ? esc_html($field['subtype']) : 'h1';
                              $fieldLabel = isset($field['label']) ? esc_html($field['label']) : '';
                              $fieldClassName = isset($field['className']) ? esc_html($field['className']) : '';
                              echo "<" . esc_attr($subtype) . " class='" . esc_attr($fieldClassName) . "'>" . esc_html($fieldLabel) . "</" . esc_attr($subtype) . ">";
                              echo "</div>";
                              break;


                         case "paragraph":
                              echo "<div class='mb-3'>";
                              // Ensure the subtype is a valid HTML tag (for safety reasons)
                              $allowed_tags = array('p', 'div', 'span', 'section');
                              $subtype = in_array($field['subtype'], $allowed_tags) ? esc_html($field['subtype']) : 'p';
                              $fieldLabel = isset($field['label']) ? esc_html($field['label']) : '';
                              $fieldClassName = isset($field['className']) ? esc_html($field['className']) : '';
                              echo "<" . esc_attr($subtype) . " class='" . esc_attr($fieldClassName) . "'>" . esc_html($fieldLabel) . "</" . esc_attr($subtype) . ">";
                              echo "</div>";
                              break;


                         case "hidden":
                              // Since hidden fields don't require a wrapper, the <div> is optional. If needed, keep it.
                              echo "<div class='mb-3'>";
                              // Sanitize and escape the field values
                              $wlgf_field_value = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
                              $fieldName = isset( $field['name'] ) ? esc_attr( $field['name'] ) : '';
                              // Output the hidden input field
                              echo "<input type='hidden' name='" . esc_attr($fieldName) . "' value='" . esc_attr($wlgf_field_value) . "'>";
                              echo "</div>";
                              break;
                              
                              
                         case "button":
                              echo "<div class='mb-3'>";
                              // Sanitize and escape fields
                              $wlgf_field_class_name = isset( $field['className'] ) ? esc_attr( $field['className'] ) : '';
                              $wlgf_field_value = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
                              $fieldName = isset( $field['name'] ) ? esc_attr( $field['name'] ) : '';
                              $fieldSubtype = isset( $field['subtype'] ) ? esc_attr( $field['subtype'] ) : 'button';
                              $fieldLabel = isset( $field['label'] ) ? esc_html( $field['label'] ) : '';
                              // Output the button
                              echo "<button type='" . esc_attr( $fieldSubtype ) . "' id='" . esc_attr( $fieldName ) . "-".esc_attr($wlgf_form_id)."' class='" . esc_attr( $wlgf_field_class_name ) . "' value='" . esc_attr( $wlgf_field_value ) . "'>" . esc_html( $fieldLabel ) . "</button>";
                              echo "</div>";
                              break;
                              
                              
                         case "recaptcha":
                              if($wlgf_recaptcha == 1 && $wlgf_sitekey && $wlgf_secretkey) {
                                   $wlgf_fieldRecaptcha = "";

                                   // Add the reCAPTCHA v3. This will be invisible.
                                   $wlgf_fieldRecaptcha .= "<input type='hidden' name='recaptcha_response' id='recaptchaResponse'>";

                                   // Sanitize sitekey
                                   $safe_sitekey = esc_js($field['sitekey']);

                                   // JavaScript to execute reCAPTCHA and set the value to our hidden input
                                   $wlgf_fieldRecaptcha = '<script>';
                                   $wlgf_fieldRecaptcha .= 'function runRecaptcha() {';
                                   $wlgf_fieldRecaptcha .= 'if (typeof grecaptcha === "undefined") {';
                                   $wlgf_fieldRecaptcha .= 'setTimeout(runRecaptcha, 100);'; // Check again in 100ms
                                   $wlgf_fieldRecaptcha .= '} else {';
                                   $wlgf_fieldRecaptcha .= 'grecaptcha.ready(function() {';
                                   $wlgf_fieldRecaptcha .= 'grecaptcha.execute("' . esc_js($safe_sitekey) . '", {action: "submit"}).then(function(token) {';
                                   $wlgf_fieldRecaptcha .= 'document.getElementById("recaptchaResponse").value = token;';
                                   $wlgf_fieldRecaptcha .= '});';
                                   $wlgf_fieldRecaptcha .= '});';
                                   $wlgf_fieldRecaptcha .= '}';
                                   $wlgf_fieldRecaptcha .= '}';
                                   $wlgf_fieldRecaptcha .= 'runRecaptcha();';
                                   $wlgf_fieldRecaptcha .= '</script>';

                                   // Output the reCAPTCHA HTML and JavaScript, escaped safely
                                   echo wp_kses($wlgf_fieldRecaptcha, array(
                                        'input' => array(
                                             'type' => array(),
                                             'name' => array(),
                                             'id' => array(),
                                             'value' => array()
                                        ),
                                        'script' => array(),
                                   ));
                              }
                              break;
                    }
               }
               //echo "<div id='wlgf-loader' class='d-none'><img width='75' height='75' src='" . esc_url($wlgf_loader) . "' alt='" . esc_attr('Loading...') . "'></div>";
               //echo "<img id='wlgf-loader' class='d-none' width='75' height='75' src='" . esc_url($wlgf_loader) . "' alt='" . esc_attr('Loading...') . "'>";
               echo "<input type='hidden' name='wlgf_form_id' value='" . esc_attr($wlgf_form_id) . "'>";
               echo "<input type='hidden' name='wlgf_form_nonce' value='" . esc_attr($wlgf_form_nonce) . "'>";
               echo "<input type='hidden' name='wlgf_honeypot' value=''>";
               echo "<input type='hidden' name='action' value='wlgf_form_submit'>";
               echo "<div class='wlgf-spinner'>";
                    echo "<img aria-hidden='true' class='wlgf-spinner' width='75' height='75' src='" . esc_url($wlgf_loader) . "' alt='" . esc_attr('Loading...') . "'>";
               echo "</div>";
               echo "<div id='wlgf-response-output' class='mb-3 wlgf-response-output' aria-hidden='true'></div>";
               echo "</form>";
               
          echo "</div>";
          // form structure output end
          
          
          // handling form submission start
          echo "<div class='wpfrank-lgf'>";
          if (empty($_POST['wlgf_honeypot']) && isset($_POST['wlgf_form_nonce'])) {
               
               /* echo "<pre>";
               print_r($_POST);
               echo "</pre>"; */
               
               // Fetch the date and time format from WordPress settings
               $wlgf_date_format = get_option('date_format');
               $wlgf_time_format = get_option('time_format');
               // Get the current date and time based on the WordPress timezone setting
               $wlgf_current_datetime = current_time('mysql');
               // Convert the MySQL date/time to PHP date/time
               $wlgf_formatted_datetime = date_i18n("{$wlgf_date_format} {$wlgf_time_format}", strtotime($wlgf_current_datetime));
               // Display or return the formatted date and time
               //echo 'Current date and time: ' . esc_html($wlgf_formatted_datetime);
               
               //load saved settings
               $wlgf_settings = get_option('wlgf_settings');
               //print_r($wlgf_settings);
               $wlgf_recaptcha = (isset($wlgf_settings['recaptcha'])) ? sanitize_text_field($wlgf_settings['recaptcha']) : 2;
               $wlgf_sitekey = (isset($wlgf_settings['sitekey'])) ? sanitize_text_field($wlgf_settings['sitekey']) : '';
               $wlgf_secretkey = (isset($wlgf_settings['secretkey'])) ? sanitize_text_field($wlgf_settings['secretkey']) : '';
               
               $wlgf_notify_admin = (isset($wlgf_settings['notify_admin'])) ? sanitize_text_field($wlgf_settings['notify_admin']) : '';
               $wlgf_email_engine = (isset($wlgf_settings['email_engine'])) ? sanitize_text_field($wlgf_settings['email_engine']) : '';
               $wlgf_smtp_host = (isset($wlgf_settings['smtp_host'])) ? sanitize_text_field($wlgf_settings['smtp_host']) : '';
               $wlgf_smtp_username = (isset($wlgf_settings['smtp_username'])) ? sanitize_text_field($wlgf_settings['smtp_username']) : '';
               $wlgf_smtp_password = (isset($wlgf_settings['smtp_password'])) ? sanitize_text_field($wlgf_settings['smtp_password']) : '';
               $wlgf_smtp_encryption = (isset($wlgf_settings['smtp_encryption'])) ? sanitize_text_field($wlgf_settings['smtp_encryption']) : '';
               $wlgf_smtp_port = (isset($wlgf_settings['smtp_port'])) ? sanitize_text_field($wlgf_settings['smtp_port']) : '';
               $wlgf_user_message = (isset($wlgf_settings['user_message'])) ? sanitize_text_field($wlgf_settings['user_message']) : '';
               
               // reCAPTCHA enable check
               if($wlgf_recaptcha == 1 && $wlgf_sitekey && $wlgf_secretkey) {
                    
                    // reCAPTCHA validate
                    // Define the reCAPTCHA URL
                    $wlgf_recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';

                    // Sanitize incoming POST data
                    $wlgf_recaptcha_response = (isset($_POST['recaptcha_response'])) ? sanitize_text_field( wp_unslash ($_POST['recaptcha_response'] ) ) : '';
                    $wlgf_remote_address = (isset($_SERVER['REMOTE_ADDR'])) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
                    $wlgf_secretkey = sanitize_text_field(get_option('wlgf_secretkey'));

                    // Prepare the data for the POST request
                    $wlgf_recaptcha_data = array(
                        'body' => array(
                            'secret'   => $wlgf_secretkey,
                            'response' => $wlgf_recaptcha_response,
                            'remoteip' => $wlgf_remote_address
                        )
                    );

                    // Make a POST request
                    $wlgf_recaptcha_result = wp_remote_post($wlgf_recaptcha_url, $wlgf_recaptcha_data);

                    // Check for a WP_Error
                    if (is_wp_error($wlgf_recaptcha_result)) {
                         $error_message = esc_html($wlgf_recaptcha_result->get_error_message());
                         echo "Something went wrong: " . esc_html($error_message);
                    } else {
                         // Decode the JSON response
                         $wlgf_recaptcha_result = json_decode(wp_remote_retrieve_body($wlgf_recaptcha_result), true);

                         // Check the reCAPTCHA score and success status
                         if (!empty($wlgf_recaptcha_result['success']) && $wlgf_recaptcha_result['score'] >= 0.5) {
                              
                              //echo 'reCAPTCHA validation passed';
                              if ( isset( $_POST['wlgf_form_id'] ) && isset( $_POST['wlgf_form_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['wlgf_form_nonce'] ) ), 'wlgf-form-post' ) ) {
                                   // saving post data into database start
                                   $wlgf_form_id = sanitize_text_field( wp_unslash($_POST['wlgf_form_id']));
                                   $wlgf_form_info = get_option('wlgf_form_'.$wlgf_form_id);
                                   $wlgf_form_name = sanitize_text_field($wlgf_form_info['form_name']);
                                   $sanitized_data = array();
                                   
                                   foreach ( $_POST as $key => $value ) {
                                        if ( $key !== 'recaptcha_response' && $key !== 'wlgf_form_nonce' ) {
                                             // For array values like checkboxes
                                             if ( is_array( $value ) ) {
                                                  foreach ( $value as $index => $item ) {
                                                       $sanitized_data[$key][$index] = sanitize_text_field( $item );
                                                  }
                                             } else {
                                                  $sanitized_data[$key] = sanitize_text_field( $value );
                                             }
                                        }
                                   }
                                   
                                   /* echo "<pre>";
                                   print_r($sanitized_data);
                                   echo "<hr>";
                                   echo "</pre>";
                                   echo "<pre>"; */
                                   
                                   // Break the string into an array
                                   $wlgf_blacklistArray = array_map('trim', explode(',', $wlgf_blacklist));
                                   
                                   /* echo "<pre>";
                                   print_r($wlgf_blacklistArray);
                                   echo "<hr>";
                                   echo "</pre>";
                                   echo "<pre>"; */
                                   
                                   // Break the string into an array
                                   foreach ($wlgf_blacklistArray as $email) {
                                        if (strpos($sanitized_data['Email'], $email) !== false) {
                                             $wlgf_blacklist_flag = 1;
                                             break;
                                        }
                                   }
                                   
                                   // check for email blacklist
                                   if ($wlgf_blacklist_flag) {
                                       echo esc_html_e('Sorry! you do not have sufficient permission to access this form.', 'lead-generation-form' );
                                   } else {
                                        // Handle file upload start
                                        if (isset($_FILES) && !empty($_FILES)) {
                                             
                                             if ( ! function_exists( 'wp_handle_upload' ) ) {
                                                  require_once( ABSPATH . 'wp-admin/includes/file.php' );
                                             }
                                             
                                             foreach ($_FILES as $key => $file) {
                                                  
                                                  if ( is_array( $file ) && isset( $file['error'] ) ) {
                                                       // Sanitize and validate the error code
                                                       $error_code = intval( $file['error'] );
                                                       
                                                       // Validate that the error code is within the expected range (0 to 8 are valid codes for PHP file upload errors)
                                                       if ( $error_code !== UPLOAD_ERR_OK && $error_code >= 0 && $error_code <= 8 ) {
                                                            // Escape the error code before outputting
                                                            echo 'File upload error. Code: ' . esc_html( $error_code );
                                                            return;
                                                       }
                                                  }

                                                  // Prepare the upload directory
                                                  $wlgf_update_form_name = sanitize_file_name(str_replace(' ', '-', strtolower($wlgf_form_name)));
                                                  $wlgf_uploads = wp_upload_dir();
                                                  $wlgf_upload_path = wp_normalize_path($wlgf_uploads['basedir']) . '/' . $wlgf_update_form_name . '/';
                                                  $wlgf_upload_url = esc_url($wlgf_uploads['baseurl'] . '/' . $wlgf_update_form_name . '/');

                                                  if (!file_exists($wlgf_upload_path)) {
                                                       wp_mkdir_p($wlgf_upload_path);
                                                  }

                                                  // Prepare the file for upload
                                                  $file_array = array(
                                                      'name'     => sanitize_file_name( $file['name'] ), // Sanitize the filename
                                                      'type'     => sanitize_mime_type( $file['type'] ), // Sanitize the MIME type
                                                      'tmp_name' => sanitize_text_field( $file['tmp_name'] ), // Sanitize the temporary file name path
                                                      'error'    => intval( $file['error'] ), // Validate the error code
                                                      'size'     => intval( $file['size'] ) // Validate the file size
                                                  );

                                                  // Turn off any script-based tests (just for this upload)
                                                  add_filter('wp_handle_upload_prefilter', function ($file) {
                                                       $file['test_form'] = false;
                                                       return $file;
                                                  });

                                                  $uploaded_file = wp_handle_upload($file_array, array('test_form' => false, 'test_type' => true));

                                                  if (!isset($uploaded_file['error'])) {
                                                       // Successfully uploaded
                                                       //echo 'Uploaded file moved successfully.';
                                                       $sanitized_data[$key] = $uploaded_file['url'];  // Store URL of the uploaded file in sanitized data
                                                  } else {
                                                       // Handle errors
                                                       //echo 'Failed to move uploaded file. Error: ' . esc_html($uploaded_file['error']);
                                                  }
                                                  remove_filter('wp_handle_upload_prefilter', '__return_false');
                                             }
                                        } // Handle file upload end
                                        
                                        //append Date and Time key
                                        $sanitized_data['Date Time'] =  $wlgf_formatted_datetime;
                                        
                                        /* echo "<pre>";
                                        print_r($sanitized_data);
                                        echo "</pre>"; */
                                        
                                        // get and append old data
                                        $wlgf_current_form_data = get_option("wlgf_saved_form_data_" . $wlgf_form_id, array());
                                        $wlgf_current_form_data[] = $sanitized_data;
                                        if(update_option('wlgf_saved_form_data_'.$wlgf_form_id, $wlgf_current_form_data)){
                                             echo '<div id="' . esc_attr('wlgf-msg') . '">' . esc_html($wlgf_user_message) . '</div>';
                                        }
                                   }
                                   //end of blacklist check
                              }
                         } else {
                              echo esc_html_e( 'Sorry! your request has not been verified.', 'lead-generation-form' );
                         }
                    }
               } else {
                    // reCAPTCHA is disabled check
                    if ( isset( $_POST['wlgf_form_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['wlgf_form_nonce'] ) ), 'wlgf-form-post' ) ) {
                         // saving post data into database start
                         $wlgf_form_id = sanitize_text_field( wp_unslash( $_POST['wlgf_form_id'] ) );
                         $wlgf_form_info = get_option('wlgf_form_'.$wlgf_form_id);
                         $wlgf_form_name = sanitize_text_field($wlgf_form_info['form_name']);
                         $sanitized_data = array();
                         foreach ( $_POST as $key => $value ) {
                              if ( $key !== 'recaptcha_response' && $key !== 'wlgf_form_nonce' ) {
                                   // For array values like checkboxes
                                   if ( is_array( $value ) ) {
                                        foreach ( $value as $index => $item ) {
                                             $sanitized_data[$key][$index] = sanitize_text_field( $item );
                                        }
                                   } else {
                                        $sanitized_data[$key] = sanitize_text_field( $value );
                                   }
                              }
                         }
                         
                         /* echo "<pre>";
                         print_r($sanitized_data);
                         echo "<hr>";
                         echo "</pre>";
                         echo "<pre>"; */
                         
                         // Check email for blacklist
                         // Break the string into an array
                         $wlgf_blacklistArray = array_map('trim', explode(',', $wlgf_blacklist));
                         
                         // Break the email list string into an array
                         if (isset($sanitized_data['Email'])) {
                                   foreach ($wlgf_blacklistArray as $email) {
                                        if (strpos($sanitized_data['Email'], $email) !== false) {
                                        $wlgf_blacklist_flag = 1;
                                        break;
                                   }
                              }
                         }
                         
                         // email blacklist message
                         if ($wlgf_blacklist_flag) {
                             echo esc_html_e('Sorry! you do not have sufficient permission to access this form.', 'lead-generation-form' );
                         } else {
                              
                              // removing honeypot field from saved data
                              if (array_key_exists('wlgf_form_id', $sanitized_data)) {
                                   unset($sanitized_data['wlgf_honeypot']);
                                   unset($sanitized_data['action']);
                              }
                              
                              // Handle file upload start
                              if (isset($_FILES) && !empty($_FILES)) {
                                  if (!function_exists('wp_handle_upload')) {
                                      require_once(ABSPATH . 'wp-admin/includes/file.php');
                                  }

                                  foreach ($_FILES as $key => $file) {
                                      if (is_array($file) && isset($file['error'])) {
                                          // Sanitize and validate the error code
                                          $error_code = intval($file['error']);

                                          // Validate that the error code is within the expected range
                                          if ($error_code !== UPLOAD_ERR_OK) {
                                              // Escape the error code before outputting
                                              echo 'File upload error. Code: ' . esc_html($error_code);
                                              return;
                                          }
                                      }

                                      // Prepare the upload directory
                                      $wlgf_update_form_name = sanitize_file_name(str_replace(' ', '-', strtolower($wlgf_form_name)));
                                      $wlgf_uploads = wp_upload_dir();
                                      $wlgf_upload_path = wp_normalize_path($wlgf_uploads['basedir']) . '/' . $wlgf_update_form_name . '/';
                                      $wlgf_upload_url = esc_url($wlgf_uploads['baseurl'] . '/' . $wlgf_update_form_name . '/');

                                      // Create the upload directory if it doesn't exist
                                      if (!file_exists($wlgf_upload_path)) {
                                          wp_mkdir_p($wlgf_upload_path);
                                      }

                                      // Prepare the file for upload
                                      $file_array = array(
                                          'name'     => sanitize_file_name($file['name']),
                                          'type'     => sanitize_mime_type($file['type']),
                                          'tmp_name' => $file['tmp_name'],  // Use directly
                                          'error'    => intval($file['error']),
                                          'size'     => intval($file['size'])
                                      );

                                      // Turn off any script-based tests (just for this upload)
                                      add_filter('wp_handle_upload_prefilter', function ($file) {
                                          $file['test_form'] = false;
                                          return $file;
                                      });

                                      // Handle the file upload
                                      $uploaded_file = wp_handle_upload($file_array, array('test_form' => false));

                                      if (isset($uploaded_file['error'])) {
                                          // Handle errors
                                          echo 'Failed to move uploaded file. Error: ' . esc_html($uploaded_file['error']);
                                      } else {
                                          // Successfully uploaded
                                          // Store URL of the uploaded file in sanitized data
                                          $sanitized_data[$key] = $uploaded_file['url'];
                                      }

                                      // Remove the filter after upload
                                      remove_filter('wp_handle_upload_prefilter', '__return_false');
                                  }
                              } // Handle file upload end

                              
                              //append Date and Time key
                              $sanitized_data['Date Time'] =  $wlgf_formatted_datetime;
                              
                              // get and append old data
                              $wlgf_current_form_data = get_option("wlgf_saved_form_data_" . $wlgf_form_id, array());
                              $wlgf_current_form_data[] = $sanitized_data;
                              if(update_option('wlgf_saved_form_data_'.$wlgf_form_id, $wlgf_current_form_data)){
                                   //echo esc_html_e( $wlgf_user_message );
                                   echo '<div id="' . esc_attr('wlgf-msg') . '">' . esc_html($wlgf_user_message) . '</div>';
                              }
                              // saving post data into database end
                         }
                         //end of blacklist check
                    }
               }
               // end reCaptcha else

               // send email start
               if($wlgf_notify_admin == 1 && $wlgf_blacklist_flag == 0) {
                    $wlgf_blog_name = get_bloginfo('name');
                    $wlgf_email_to = get_option('admin_email'); // admin email for wp_mail()
                    // Define email recipient, subject and Prepare email body
                    
                    $wlgf_body_allowed_tags = array(
                        'strong' => array(),
                        'br' => array(),
                        'hr' => array(),
                        'h3' => array()
                    );
                    $wlgf_email_txt1 = __("Details are as follows", 'lead-generation-form').": "."<br><br>";
                    $wlgf_email_body = wp_kses("$wlgf_email_txt1", $wlgf_body_allowed_tags);
                    $wlgf_email_txt2 = wp_kses("<strong>", $wlgf_body_allowed_tags) . __("Form ID", 'lead-generation-form').wp_kses("</strong>", $wlgf_body_allowed_tags).": ".$wlgf_form_id.wp_kses("<br>", $wlgf_body_allowed_tags);
                    $wlgf_email_body .= wp_kses($wlgf_email_txt2, $wlgf_body_allowed_tags);
                    //print_r($sanitized_data);
                    
                    // removing Form ID and honeypot from email data
                    if (array_key_exists('wlgf_form_id', $sanitized_data)) {
                         unset($sanitized_data['wlgf_form_id']);
                         unset($sanitized_data['wlgf_honeypot']);
                    }
                    //print_r($sanitized_data);
                    
                    foreach ($sanitized_data as $key => $value) {
                         if (is_array($value)) {
                              $value = implode(', ', $value); // Convert array values to comma-separated string
                         }
                         if (strcasecmp($key, "Name") == 0) {
                              $wlgf_AddReplyToFieldName = $value;
                         }
                         if (strcasecmp($key, "Email") == 0) {
                              $wlgf_AddReplyToFieldEmail = $value;
                         }
                         $wlgf_email_body .=  wp_kses("<strong>", $wlgf_body_allowed_tags) . ucfirst($key) . wp_kses("</strong>", $wlgf_body_allowed_tags).": " .$value . "<br>";
                    }
                    
                    $wlgf_email_subject = $wlgf_blog_name." - ".__('A new query received by', 'lead-generation-form')." ".$wlgf_AddReplyToFieldName;
                    
                    // Set headers
                    $wlgf_headers = array(
                         'Content-Type: text/html; charset=UTF-8',
                         'From: ' . $wlgf_blog_name . ' <' . $wlgf_email_to . '>', // admin site name and email
                         'Reply-To: ' . $wlgf_AddReplyToFieldName . ' <' . $wlgf_AddReplyToFieldEmail . '>', // submitter email
                    );
                    
                    // Send the email via wp_mail() start
                    if($wlgf_email_engine == 1) {
                         if (wp_mail($wlgf_email_to, $wlgf_email_subject, $wlgf_email_body, $wlgf_headers)) {
                             //echo 'Lead information sent successfully.';
                         } else {
                             //echo 'Failed to send lead information via wp_mail().';
                         }
                    } // Send the email via wp_mail() end
                    
                    // Send the email via SMTP start
                    if($wlgf_email_engine == 2) {
                         // Hook into PHPMailer to set SMTP settings
                         add_action('phpmailer_init', 'wlgf_configure_smtp', 999);
                         
                         if (wp_mail($wlgf_email_to, $wlgf_email_subject, $wlgf_email_body, $wlgf_headers)) {
                             //echo 'Lead information sent successfully via SMTP.';
                         } else {
                             //echo 'Failed to send lead information via SMTP.';
                         }
                    } // Send the email via SMTP end
                    
               } // send email end
          }
          echo "</div>";
          // handling form submission end
          
          return ob_get_clean();
     } // end of isset id
}