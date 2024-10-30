<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; /* Exit if accessed directly */
}

wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui-core');
wp_enqueue_script('jquery-ui-accordion');
wp_enqueue_script('jquery-ui-autocomplete');
wp_enqueue_script('jquery-ui-button');
wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_script('jquery-ui-dialog');
wp_enqueue_script('jquery-ui-draggable');
wp_enqueue_script('jquery-ui-droppable');
wp_enqueue_script('jquery-ui-menu');
wp_enqueue_script('jquery-ui-mouse');
wp_enqueue_script('jquery-ui-progressbar');
wp_enqueue_script('jquery-ui-selectable');
wp_enqueue_script('jquery-ui-resizable');
wp_enqueue_script('jquery-ui-sortable');
wp_enqueue_script('jquery-ui-slider');
wp_enqueue_script('jquery-ui-spinner');
wp_enqueue_script('jquery-ui-tooltip');
wp_enqueue_script('jquery-ui-tabs');
wp_enqueue_script('jquery-effects-core');
wp_enqueue_script('jquery-effects-blind');
wp_enqueue_script('jquery-effects-shake');

wp_enqueue_script( 'wlgf-formbuilder-js', plugin_dir_url(__FILE__). 'assets/formbuilder-3.19.12/js/form-builder.min.js', array('jquery'), '3.8.2' );
wp_enqueue_style( 'wlgf-bootstrap-scoped-admin-css', plugin_dir_url(__FILE__) . 'assets/bootstrap-5.3.3/dist/css/bootstrap-scoped-admin.css', array(), '5.3.3', 'all' );
wp_enqueue_style( 'wlgf-fontawesome-css', plugins_url( 'assets/fontawesome-free-6.4.2-web/css/all.min.css', __FILE__ ), array(), '6.4.2', 'all' );

$wlgf_FormIdSet = sanitize_text_field("notset");
$wlgf_form_id = "";
$wlgf_form_name = "";
$wlgf_form_data = "";
if(isset($_GET['form-id'])){
	if ( current_user_can( 'manage_options' ) ) {
		if ( isset( $_GET['modify-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_GET['modify-nonce'] ) ), 'wlgf-modify-form' ) ) {
			$wlgf_form_id = sanitize_text_field( wp_unslash( $_GET['form-id'] ) );
			$wlgf_form_data = get_option('wlgf_form_'.$wlgf_form_id);
			$wlgf_form_name = sanitize_text_field( $wlgf_form_data['form_name'] );
			$wlgf_sanitized_form_data = wp_kses_post( stripslashes($wlgf_form_data['form']) );
			$wlgf_form_data = json_decode($wlgf_sanitized_form_data);
			$wlgf_FormIdSet = sanitize_text_field("set");
		}
	}
}
wp_enqueue_style( 'wlgf-form-generator-css', plugin_dir_url(__FILE__) . 'admin/assets/css/form-generator.css', array(), '1.0', 'all' );
wp_enqueue_script( 'wlgf-form-generator-js', plugin_dir_url(__FILE__) . 'admin/assets/js/form-generator.js', array('jquery'), '1.0', 'all' );
wp_add_inline_script( 'wlgf-form-generator-js', 'const FormGenerator = ' . wp_json_encode( array(
    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
    'FormIdSet' => $wlgf_FormIdSet,
    'FormId' => $wlgf_form_id,
    'FormName' => $wlgf_form_name,
    'FormData' => $wlgf_form_data,
    'SaveNonce' => wp_create_nonce( 'wlgf-save-form' ),
    'ModifyNonce' => wp_create_nonce( 'wlgf-modify-form' ),
)), 'before' );

//get all form list
global $wpdb;
$wlgf_options_table_name = "{$wpdb->prefix}options";
$wlgf_form_key = 'wlgf_form_';
// reference : https://wordpress.stackexchange.com/questions/8825/how-do-you-properly-prepare-a-like-sql-statement
$wlgf_all_forms = $wpdb->get_results(
	$wpdb->prepare( "SELECT option_name FROM `$wpdb->options` WHERE `option_name` LIKE %s ORDER BY option_id ASC", '%' . $wlgf_form_key . '%' )
);
//print_r($wlgf_all_forms);
?>
<div class="wpfrank-lgf">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-6 col-sm-6">
				<div class="my-3">
					<h1><?php esc_html_e( 'Create or Modify Form', 'lead-generation-form' ); ?></h1>
				</div>
			</div>
			<div class="col-md-6 col-sm-6">
				<div class="my-3">
					<!--<select name="wlgf_saved_forms" id="wlgf_saved_forms" class="form-control form-select form-select-lg">
						<option value="0"><?php //esc_html_e( 'Create New Form', 'lead-generation-form' ); ?></option>
						<option value="1">Lead Generation Form Sample</option>
						<?php
						// print form list
						/* foreach ( $wlgf_all_forms as $wlgf_form ) {
							$wlgf_form_key    = $wlgf_form->option_name;
							//$wlgf_underscore_pos = strrpos( $wlgf_form_key, '_' );
							//$wlgf_form_id     = substr( $wlgf_form_key, ( $wlgf_underscore_pos + 1 ) );
							$wlgf_form_data = get_option($wlgf_form_key);
							?><option value="<?php echo esc_attr($wlgf_form_data['form_id']); ?>"><?php echo esc_html($wlgf_form_data['form_name']); ?></option><?php
						} */
						?>
					</select>-->
				</div>
			</div>
			<div class="col-12">
				<hr/>
				<div class="mb-3">
					<label for="wlgf-form-name" class="form-label"><strong><?php esc_html_e( 'Form Name', 'lead-generation-form' ); ?></strong> *</label>
					<input class="form-control" id="wlgf-form-name" name="wlgf-form-name" type="text" value="" placeholder="<?php esc_html_e( 'Type a name for the form', 'lead-generation-form' ); ?>" aria-label="default input example">
				</div>
				<div id="build-wrap"></div>
				<div id="wlgf-load-process" class="spinner-grow m-3 text-dark d-none" role="status">
					<span class="visually-hidden"></span>
				</div>
				<hr/>
				<div id="wlgf-button-div">
					<?php
					if(isset($_GET['form-id'])){
						$wlgf_form_id = sanitize_text_field( wp_unslash( $_GET['form-id'] ) );
						//echo '<div class="row my-3"><h5><strong>Form Shortcode</strong> ' . esc_html("[WLFG id=" . esc_attr($wlgf_form_id) . "]") . '</h5></div>';
						echo '<div class="row my-3"><h5><strong>Form Shortcode</strong> <input type="text" id="wlgf-shortcode-' . esc_attr($wlgf_form_id) . '" class="btn btn-info btn-sm" value="' . esc_html("[WLFG id=$wlgf_form_id]") . '"> <button title="copy shortcode" id="wlgf-shortcode-' . esc_attr($wlgf_form_id) . '" onclick="return WLGF(' . esc_attr($wlgf_form_id) . ', \'copy\');" type="button" class="btn btn-sm btn-outline-dark">Copy Shortcode <i class="fa-sharp fa-solid fa-copy"></i></button> <button class="btn btn-sm btn-light d-none wlgf-copied-' . esc_attr($wlgf_form_id) . '">Copied</button></h5></div>';
						echo '<input type="hidden" id="wlgf-modify-form-id" value="' . esc_attr($wlgf_form_id) . '">';
						echo '<button type="button" id="wlgf-modify-form" value="wlgf_modify_form" class="wlgf-action-button btn btn-primary btn-lg"><i class="fa-sharp fa-solid fa-file-pen"></i> ' . esc_html__( 'Modify Form', 'lead-generation-form' ) . '</button>';
					} else {
					?>
					<button type="button" id="wlgf-save-form" value="wlgf_save_form" class="wlgf-action-button btn btn-primary btn-lg"><i class="fa-sharp fa-regular fa-floppy-disk"></i> <?php esc_html_e( 'Save Form', 'lead-generation-form' ); ?></button>
					<?php } ?>
				</div>
				<div id="wlgf-save-process" class="spinner-grow m-3 text-dark d-none" role="status">
					<span class="visually-hidden"></span>
				</div>
				<div id="faq" class="mt-5">
					<div class="bg-white p-4">
						<h6 class="card-header">
						<strong><?php esc_html_e( 'Please Read FAQ While Create OR Modify Form', 'lead-generation-form' ); ?></strong>
						</h6>
						<hr>

						<p>
						<strong><?php esc_html_e( 'Q.1. Is it permissible to use copied content for the Form structure?', 'lead-generation-form' ); ?></strong><br>
						<?php esc_html_e( 'You may insert copied information as plain text.', 'lead-generation-form' ); ?><br>
						<?php esc_html_e( 'However, directly pasting data into Form label and attribute fields is discouraged.', 'lead-generation-form' ); ?>
						</p>

						<p>
						<strong><?php esc_html_e( 'Q.2. Can I include several fields in the Form structure?', 'lead-generation-form' ); ?></strong><br>
						<?php esc_html_e( 'Absolutely, you can incorporate numerous fields in the Form.', 'lead-generation-form' ); ?><br>
						<?php esc_html_e( 'However, only one submit button is allowed per Form.', 'lead-generation-form' ); ?>
						</p>

						<p>
						<strong><?php esc_html_e( "Q.3. Can I alter the Form structure after it's been published on the website?", 'lead-generation-form' ); ?></strong><br>
						<?php esc_html_e( "It's advised against changing the form structure after publishing.", 'lead-generation-form' ); ?><br>
						<?php esc_html_e( 'Doing so may lead to discrepancies in the records table on the All Leads page.', 'lead-generation-form' ); ?><br>
						<?php esc_html_e( "However, if you haven't received any submissions or data from users, you may make adjustments to the Form.", 'lead-generation-form' ); ?><br>
						<?php esc_html_e( 'You can clone the current Form and modify or replace it with an older one.', 'lead-generation-form' ); ?>
						</p>

						<p>
						<strong><?php esc_html_e( 'Server Info for debugging', 'lead-generation-form' ); ?></strong><br>
						<?php
						// Get maximum upload file size
						$max_upload_size = ini_get('upload_max_filesize');
						// Get maximum post size
						$max_post_size = ini_get('post_max_size');

						// Define translation strings for labels
						$max_upload_size_label = __("Maximum upload file size", "lead-generation-form");
						$max_post_size_label = __("Maximum post size", "lead-generation-form");

						// Output the translated and sanitized text
						echo esc_html($max_upload_size_label) . ": <strong>" . esc_html($max_upload_size) . "</strong><br>";
						echo esc_html($max_post_size_label) . ": <strong>" . esc_html($max_post_size) . "</strong><br>";
						?>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>