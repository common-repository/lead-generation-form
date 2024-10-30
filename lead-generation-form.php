<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; /* Exit if accessed directly */
}

/**
 * Plugin Name:       Lead Generation Form
 * Plugin URI:        https://webenvo.com/
 * Description:       Ultimate solution for harnessing the power of dynamic forms to capture valuable leads from your prospective customers.
 * Version:           1.0.7
 * Requires at least: 4.0
 * Requires PHP:      4.0
 * Author:            FARAZFRANK
 * Author URI:        https://profiles.wordpress.org/farazfrank/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       lead-generation-form
 * Domain Path:       /languages
 
Lead Generation Form is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Lead Generation Form is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Lead Generation Form. If not, see https://webenvo.com/.
*/

/* activation */
function wlgf_activation() {
	/* update current plugin version */
	if ( is_admin() ) {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$wlgf_plugin_data = get_plugin_data( __FILE__ );
		if ( isset( $wlgf_plugin_data['Version'] ) ) {
			$wlgf_plugin_version = $wlgf_plugin_data['Version'];
			update_option( 'wlgf_current_version', $wlgf_plugin_version );
		}
		
		//install-script
		require_once 'includes/install-script.php';
	}
}
register_activation_hook( __FILE__, 'wlgf_activation' );

/* de-activation */
function wlgf_deactivation(){
	// update last active plugin version
	$wlgf_last_version = get_option('wlgf_current_version');
	if($wlgf_last_version !== ""){
		update_option('wlgf_last_version', $wlgf_last_version);
	}
}
register_deactivation_hook( __FILE__, 'wlgf_deactivation' );

/* uninstall */
function wlgf_uninstall(){
}
register_uninstall_hook(__FILE__, 'wlgf_uninstall');

function wlgf_check_version() {
    // Check if our transient exists. If it does, it means the check has been done in the last 24 hours.
	if ( get_transient( 'wlgf_version_check' ) ) {
		return;
	}

	if ( ! function_exists( 'get_plugin_data' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$wlgf_plugin_data = get_plugin_data( __FILE__ );
	if ( isset( $wlgf_plugin_data['Version'] ) ) {
		// Sanitize the version value
		$wlgf_plugin_version = sanitize_text_field( $wlgf_plugin_data['Version'] );

		// Get the option and sanitize it as well
		$wlgf_current_version = sanitize_text_field( get_option( 'wlgf_current_version', '1.0' ) ); // Assuming 1.0 is your starting version

		if ( version_compare( $wlgf_current_version, $wlgf_plugin_version, '<' ) ) {
			// Run your update routine here

			// Finally, update the version in the database
			update_option( 'wlgf_current_version', $wlgf_plugin_version );
		}

		// Set our transient to expire in 24 hours
		set_transient( 'wlgf_version_check', true, DAY_IN_SECONDS );
	}
}
add_action( 'admin_init', 'wlgf_check_version' );

// load translation
function wlgf_load_translation() {
	load_plugin_textdomain( 'lead-generation-form', false, dirname( plugin_basename(__FILE__) ) .'/languages' );
}
add_action( 'plugins_loaded', 'wlgf_load_translation');

// register styles and scripts for frontend
function wlgf_user_register_scripts(){
	wp_enqueue_script('jquery');
	wp_register_script( 'wlgf-formbuilder-js', plugin_dir_url(__FILE__). 'admin/assets/formbuilder-master/js/form-builder.min.js', array('jquery'), '3.19.12' );
	wp_register_script( 'wlgf-datatables-js', plugin_dir_url(__FILE__). 'admin/assets/datatables/datatables.min.js', array('jquery'), '1.13.6' );
	wp_register_script( 'wlgf-datatables-bootstrap5-min-js', plugin_dir_url(__FILE__). 'admin/assets/datatables/datatables.bootstrap5.min.js', array('jquery'), '5.0.0' );
	wp_register_script( 'wlgf-bootstrap-popper-min-js', plugin_dir_url(__FILE__). 'admin/assets/bootstrap-5.3.3/dist/js/popper.min.js', array('jquery'), '2.11.8');
	wp_register_script( 'wlgf-bootstrap-min-js', plugin_dir_url(__FILE__). 'admin/assets/bootstrap-5.3.3/dist/js/bootstrap.min.js', array('jquery'), '5.3.3');
	wp_register_script( 'wlgf-bootstrap-bundle-min-js', plugin_dir_url(__FILE__). 'admin/assets/bootstrap-5.3.3/dist/js/bootstrap.bundle.min.js', array('jquery'), '5.3.3' );
	
	wp_register_style( 'wlgf-fontawesome-css', plugin_dir_url(__FILE__) . 'admin/assets/fontawesome-free-6.4.2-web/css/all.min.css', array(), '6.4.2', 'all' );
	wp_register_style( 'wlgf-datatables-min-css', plugin_dir_url(__FILE__) . 'admin/assets/datatables/datatables.min.css', array(), '1.0.0', 'all' );
	
	// shortcode assets
	wp_register_style( 'wlgf-shortcode-form', plugin_dir_url(__FILE__) . 'includes/assets/css/wlgf-shortcode-form.css', array(), '1.0.0', 'all' );
	
	wp_register_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), null, true); // load in footer
    	wp_register_script('wlgf-shortcode-form-js', plugin_dir_url(__FILE__). 'includes/assets/js/wlgf-shortcode-form.js', array('jquery'), null, true); // load in footer
	wp_register_script('wlgf-shortcode-ajax-script', plugin_dir_url(__FILE__) . 'includes/assets/js/wlgf-shortcode-ajax-script.js', array('jquery'), null, true);
	wp_localize_script('wlgf-shortcode-ajax-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action( 'wp_enqueue_scripts', 'wlgf_user_register_scripts' );

// register styles and scripts for backend
function wlgf_admin_register_scripts(){
	wp_enqueue_script('jquery');
	
	//manage forms assets
	wp_register_style( 'wlgf-bootstrap-scoped-admin-css', plugin_dir_url(__FILE__) . 'admin/assets/bootstrap-5.3.3/dist/css/bootstrap.css', array(), '5.3.3', 'all' );
	
	wp_register_style( 'wlgf-manage-forms-css', plugin_dir_url(__FILE__) . 'admin/assets/css/manage-forms.css', array(), '1.0', 'all' );
	wp_register_script( 'wlgf-manage-forms-js', plugin_dir_url(__FILE__) . 'admin/assets/js/manage-forms.js', array('jquery'), '1.0', true );
	
	wp_register_style( 'wlgf-form-generator-css', plugin_dir_url(__FILE__) . 'admin/assets/css/form-generator.css', array(), '1.0', 'all' );
	wp_register_script( 'wlgf-form-generator-js', plugin_dir_url(__FILE__) . 'admin/assets/js/form-generator.js', array('jquery'), '1.0', 'all' );
	
	wp_register_style( 'wlgf-leads-css', plugin_dir_url(__FILE__) . 'admin/assets/css/leads.css', array(), '1.0', 'all' );
	wp_register_script( 'wlgf-leads-js', plugin_dir_url(__FILE__) . 'admin/assets/js/leads.js', array('jquery'), '1.0', true );
	
	wp_register_script( 'wlgf-import-export-js', plugin_dir_url(__FILE__) . 'admin/assets/js/import-export.js', array('jquery'), '1.0', 'all' );
	wp_register_script( 'wlgf-settings-js', plugin_dir_url(__FILE__) . 'admin/assets/js/settings.js', array('jquery'), '1.0', 'all' );
}
add_action( 'admin_enqueue_scripts', 'wlgf_admin_register_scripts' );

// menu
function wlgf_menus() {
	// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
	add_menu_page(
		'Lead Generation Form',
		'Lead Generation Form',
		'manage_options',
		'lead-generation-form',
		'wlgf_main',
		'dashicons-media-spreadsheet',
		65
	);
	
	//add_submenu_page( string $parent_slug, string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', int $position )
	add_submenu_page( 'lead-generation-form', 'Manage Forms', __('Manage Forms', 'lead-generation-form'), 'manage_options', 'wlgf-manage-forms', 'wlgf_manage_forms' );
	add_submenu_page( 'lead-generation-form', 'Form Generator', __('Form Generator', 'lead-generation-form'), 'manage_options', 'wlgf-form-generator', 'wlgf_form_generator' );
	add_submenu_page( 'lead-generation-form', 'All Captured Leads', __('All Captured Leads', 'lead-generation-form'), 'manage_options', 'wlgf-leads', 'wlgf_leads' );
	add_submenu_page( 'lead-generation-form', 'Import & Export Forms', __('Import & Export', 'lead-generation-form'), 'manage_options', 'wlgf-import-export', 'wlgf_import_export' );
	add_submenu_page( 'lead-generation-form', 'Settings', __('Settings', 'lead-generation-form'), 'manage_options', 'wlgf-settings', 'wlgf_settings' );
}
add_action( 'admin_menu', 'wlgf_menus' );

//include('includes/shortcode.php');
include('includes/shortcode-ajax.php');

// main page
function wlgf_main(){
	require 'admin/dashboard.php';
}

// manage forms page
function wlgf_manage_forms(){
	require 'admin/manage-forms.php';
}

// form generator page
function wlgf_form_generator(){
	require 'admin/form-generator.php';
}

// all leads page
function wlgf_leads(){
	require 'admin/leads.php';
}

// import export page
function wlgf_import_export(){
	require 'admin/import-export.php';
}

// settings page
function wlgf_settings(){
	require 'admin/settings.php';
}

function wlgf_get_next_id(){
	global $wpdb;
	$wlgf_form_key = "wlgf_form_";
	// reference : https://wordpress.stackexchange.com/questions/8825/how-do-you-properly-prepare-a-like-sql-statement
	$wlgf_form_entries = $wpdb->get_row(
		$wpdb->prepare("SELECT option_name FROM `$wpdb->options` WHERE `option_name` LIKE %s ORDER BY option_id DESC LIMIT 1", '%'.$wlgf_form_key.'%'), ARRAY_N
	);
	
	if($wpdb->num_rows) {
		$wlgf_form_last_key = $wlgf_form_entries[0];
		$wlgf_underscore_pos = strrpos($wlgf_form_last_key, '_');
		$wlgf_last_form_id = (int) substr($wlgf_form_last_key, ($wlgf_underscore_pos + 1));
		return ($wlgf_last_form_id + 1);
	} else {
		return 1;
	}
}

// 1. manage form actions start - clone, delete
function wlgf_manage_form_action_callback(){
	//print_r($_POST);
	if ( current_user_can( 'manage_options' ) ) {
		
		if(isset( $_POST['do'])) $wlgf_do_action = sanitize_text_field( wp_unslash( $_POST['do'] ) ); else $wlgf_do_action = sanitize_text_field("none");
		
		// clone form start
		if($wlgf_do_action == 'clone'){
			if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ), 'wlgf-manage-forms' ) ) {
				// verified action
				if(isset( $_POST['wlgf_form_id'] )){
					$wlgf_form_id = sanitize_text_field( wp_unslash( $_POST['wlgf_form_id'] ) );

					//get cloning gallery data
					$wlgf_form_clone_data = get_option("wlgf_form_".$wlgf_form_id);
					
					// new form data
					$wlgf_form_id_new = wlgf_get_next_id();
					$wlgf_form_name_new = sanitize_text_field($wlgf_form_clone_data['form_name'].' - Clone');
					$wlgf_form_new = sanitize_text_field($wlgf_form_clone_data['form']);
					$wlgf_form_edit_nonce = wp_create_nonce( 'wlgf-edit-form' );
					
					// add cloned form details
					$wlgf_form_new_details = array('form_id' => $wlgf_form_id_new, 'form_name' => $wlgf_form_name_new, 'form' => $wlgf_form_new);
					update_option('wlgf_form_'.$wlgf_form_id_new, $wlgf_form_new_details);
					if($wlgf_form_id_new > $wlgf_form_id){
						$wlgf_form_shortcode = sanitize_text_field("[WLFG id='$wlgf_form_id_new']");
					?>
					<tr id="<?php echo esc_attr( $wlgf_form_id_new ); ?>">
						<td><?php echo esc_html($wlgf_form_id_new); ?></td>
						<td><?php echo esc_html($wlgf_form_name_new); ?></td>
						<td>
							<input type="text" id="wlgf-shortcode-<?php echo esc_attr( $wlgf_form_id_new ); ?>" class="btn btn-info btn-sm" value="<?php echo esc_attr( $wlgf_form_shortcode ); ?>">
							<button title="copy shortcode" id="wlgf-shortcode-<?php echo esc_attr( $wlgf_form_id_new ); ?>" onclick="return WLGF('<?php echo esc_attr($wlgf_form_id_new); ?>', 'copy');" type="button" class="btn btn-sm btn-outline-dark"><i class="fa-sharp fa-solid fa-copy"></i></button>
							<button class="btn btn-sm btn-light d-none wlgf-copied-<?php echo esc_attr( $wlgf_form_id_new ); ?>"><?php esc_html_e( 'Copied', 'lead-generation-form' ); ?></button>
						</td>
						<td>
							<button onclick="return WLGF('<?php echo esc_attr($wlgf_form_id_new); ?>', 'clone');" type="button" class="btn btn-sm btn-outline-primary" title="clone shortcode"><i class="fa-sharp fa-regular fa-clone"></i></button>
							<a href="?page=wlgf-form-generator&form-id=<?php echo esc_attr( $wlgf_form_id_new ); ?>&modify-nonce=<?php echo esc_attr( wp_create_nonce( 'wlgf-modify-form' )); ?>" type="button" class="btn btn-sm btn-outline-primary" title="modify"><i class="fa-sharp fa-solid fa-pen-to-square"></i></a>
							<button onclick="return WLGF('<?php echo esc_attr($wlgf_form_id_new); ?>', 'delete');" type="button" class="btn btn-sm btn-outline-primary" title="delete"><i class="fa-sharp fa-regular fa-trash-can"></i></button>
						</td>
					</tr>
					<?php
					}
				}
				wp_die();
			} else {
				echo esc_html("Nonce not verified action.");
				wp_die();
			}
		} // clone form end
		
		
		// delete form start
		if($wlgf_do_action == 'delete'){
			if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ), 'wlgf-manage-forms' ) ) {
				// verified action
				if(isset( $_POST['wlgf_form_id'] )){
					$wlgf_form_id = sanitize_text_field( wp_unslash( $_POST['wlgf_form_id'] ) );
					//delete form row
					delete_option("wlgf_form_".$wlgf_form_id);
					delete_option("wlgf_saved_form_data_".$wlgf_form_id);
				}
				wp_die();
			} else {
				echo esc_html("Nonce not verified action.");
				wp_die();
			}
		} // delete form end
		
	}
}
add_action( 'wp_ajax_wlgf_manage_form_action', 'wlgf_manage_form_action_callback' );
// 1. manage form actions end


// 2. form generator page action start
function wlgf_form_save_callback(){
	if ( current_user_can( 'manage_options' ) ) {
		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ), 'wlgf-save-form' ) ) {
			// save filters
			
			/* echo "<pre>";
			print_r($_POST);
			echo "</pre>"; */
			
			$wlgf_form = array();
			$wlgf_form_id = (isset($_POST['id'])) ? sanitize_text_field( wp_unslash ($_POST['id'] ) ) : '';
			$wlgf_form_name = (isset($_POST['name'])) ? sanitize_text_field( wp_unslash ($_POST['name'] ) ) : '';
			//$wlgf_sanitized_form_data = wp_kses_post( stripslashes( $_POST['form'] ) );
			//$wlgf_sanitized_form_data = isset($_POST['form']) ? wp_kses_post(stripslashes($_POST['form'])) : '';
			$wlgf_sanitized_form_data = isset($_POST['form']) ? wp_kses_post(wp_unslash($_POST['form'])) : '';
			$wlgf_form = json_decode($wlgf_sanitized_form_data);

			/* echo "<hr>";
			echo "<pre>";
			print_r($wlgf_form);
			echo "</pre>"; */
			
			if($wlgf_form_id == 0) $wlgf_form_id = wlgf_get_next_id(); // it's new form
			//if($wlgf_form_id == 1) $wlgf_form_id = $wlgf_form_id + 1;
			$wlgf_form_details = array('form_id' => $wlgf_form_id, 'form_name' => $wlgf_form_name, 'form' => $wlgf_form);
			update_option("wlgf_form_".$wlgf_form_id, $wlgf_form_details );
			echo '<div class="row my-3"><h5><strong>Form Shortcode</strong> <input type="text" id="wlgf-shortcode-' . esc_attr($wlgf_form_id) . '" class="btn btn-info btn-sm" value="' . esc_html("[WLFG id=$wlgf_form_id]") . '"> <button title="copy shortcode" id="wlgf-shortcode-' . esc_attr($wlgf_form_id) . '" onclick="return WLGF(' . esc_attr($wlgf_form_id) . ', \'copy\');" type="button" class="btn btn-sm btn-outline-dark">Copy Shortcode <i class="fa-sharp fa-solid fa-copy"></i></button> <button class="btn btn-sm btn-light d-none wlgf-copied-' . esc_attr($wlgf_form_id) . '">Copied</button></h5></div>';
			echo '<input type="hidden" id="wlgf-modify-form-id" value="' . esc_attr($wlgf_form_id) . '">';
			echo '<button type="button" id="wlgf-modify-form" value="wlgf_modify_form" class="wlgf-action-button btn btn-primary btn-lg"><i class="fa-sharp fa-solid fa-file-pen"></i> ' . esc_html__( 'Modify Form', 'lead-generation-form' ) . '</button>';
		}
		
		// new form modify immediately
		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ), 'wlgf-modify-form' ) ) {
			// save filters
			/* echo "<pre>";
			print_r($_POST);
			echo "</pre>"; */
			
			$wlgf_form = array();
			$wlgf_form_id = (isset($_POST['id'])) ? sanitize_text_field( wp_unslash ($_POST['id'] ) ) : '';
			$wlgf_form_name = (isset($_POST['name'])) ? sanitize_text_field( wp_unslash ($_POST['name'] ) ) : '';
			//$wlgf_sanitized_form_data = wp_kses_post( stripslashes( $_POST['form'] ) );
			//$wlgf_sanitized_form_data = isset($_POST['form']) ? wp_kses_post(stripslashes($_POST['form'])) : '';
			$wlgf_sanitized_form_data = isset($_POST['form']) ? wp_kses_post(wp_unslash($_POST['form'])) : '';
			$wlgf_form = json_decode($wlgf_sanitized_form_data);

			/* echo "<hr>";
			echo "<pre>";
			print_r($wlgf_form);
			echo "</pre>"; */
			
			$wlgf_form_details = array('form_id' => $wlgf_form_id, 'form_name' => $wlgf_form_name, 'form' => $wlgf_form);
			update_option("wlgf_form_".$wlgf_form_id, $wlgf_form_details );
			echo '<div class="row my-3"><h5><strong>Form Shortcode</strong> <input type="text" id="wlgf-shortcode-' . esc_attr($wlgf_form_id) . '" class="btn btn-info btn-sm" value="' . esc_html("[WLFG id=$wlgf_form_id]") . '"> <button title="copy shortcode" id="wlgf-shortcode-' . esc_attr($wlgf_form_id) . '" onclick="return WLGF(' . esc_attr($wlgf_form_id) . ', \'copy\');" type="button" class="btn btn-sm btn-outline-dark">Copy Shortcode <i class="fa-sharp fa-solid fa-copy"></i></button> <button class="btn btn-sm btn-light d-none wlgf-copied-' . esc_attr($wlgf_form_id) . '">Copied</button></h5></div>';
			echo '<input type="hidden" id="wlgf-modify-form-id" value="' . esc_attr($wlgf_form_id) . '">';
			echo '<button type="button" id="wlgf-modify-form" value="wlgf_modify_form" class="wlgf-action-button btn btn-primary btn-lg"><i class="fa-sharp fa-solid fa-file-pen"></i> ' . esc_html__( 'Modify Form', 'lead-generation-form' ) . '</button>';
		}
		
		wp_die();
	}
}
add_action( 'wp_ajax_wlgf_save_form', 'wlgf_form_save_callback' );
// 2. form generator page action end


// 3. all lead page action - load + file, delete, multiple delete [start]
function wlgf_lead_loader_callback(){
	if ( current_user_can( 'manage_options' ) ) {
		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ), 'wlgf-leads' ) ) {
			
			/* echo "<pre>";
			print_r($_POST);
			echo "</pre>"; */
			
			if(isset( $_POST['wlgf_form_id'] ) && isset( $_POST['do'] )){
				$wlgf_form_id = (isset($_POST['wlgf_form_id'])) ? sanitize_text_field( wp_unslash ($_POST['wlgf_form_id'] ) ) : '';
				$wlgf_do = (isset($_POST['do'])) ? sanitize_text_field( wp_unslash ($_POST['do'] ) ) : '';

				// start load table data				
				if($wlgf_do == 'load') {
					// load form structure to find form label as table coulmn name
					$wlgf_form_body = get_option('wlgf_form_'.$wlgf_form_id);
					$wlgf_form_name = $wlgf_form_body['form_name'];
					$wlgf_form = $wlgf_form_body['form'];
					$structure = json_decode($wlgf_form);
					
					// load form saved data
					$wlgf_form_data = get_option('wlgf_saved_form_data_'.$wlgf_form_id);
					/* echo "<pre>";
					print_r($wlgf_form_data);
					echo "</pre>"; */
					// starting index from 1 (cause zero index delete condition fix)
					//$wlgf_form_data_new = array_combine(range(1, count($wlgf_form_data)), array_values($wlgf_form_data));
					/* echo "<pre>";
					print_r($wlgf_form_data_new);
					echo "</pre>"; */
					
					// write record into csv file
					$wlgf_upload_dir = wp_upload_dir();
					$wlgf_file_path = $wlgf_upload_dir['basedir'].'/';
					$wlgf_file_url = $wlgf_upload_dir['baseurl'].'/';
					$wlgf_file_name = $wlgf_form_name.'- Lead List.csv';
					$wlgf_file_create_path = $wlgf_file_path.$wlgf_file_name;
					$wlgf_download_url = $wlgf_file_url.$wlgf_file_name;
					$wlgf_file_header = "";
					$wlgf_file_record = "";
					
					require_once(ABSPATH . 'wp-admin/includes/file.php');

					// Initialize the WordPress filesystem, no more using 'file-put-contents' function
					if ( ! WP_Filesystem() ) {
					    return false; // Unable to initialize Filesystem
					}

					global $wp_filesystem;

					$wlgf_file_create_path = wp_normalize_path($wlgf_file_create_path); // Normalize the path for file operations

					if ($wp_filesystem->put_contents($wlgf_file_create_path, '', FS_CHMOD_FILE)) {
					    // Success, handle or notify accordingly
					    // echo 'File created successfully.';
					} else {
					    // Error, handle or notify accordingly
					    // echo 'Failed to create file.';
					}

					$wlgf_form_fields_sequence = array();
					?>
					<table id="forms-tables" class="table table-hover table-responsive" style="width:100%">
						<thead>
							<tr>
								<?php foreach ($structure as $item): ?>
								<?php if (property_exists($item, 'label') && !in_array($item->type, ['header', 'button', 'paragraph', 'hidden', 'file'])): ?>
										<?php $wlgf_form_fields_sequence[] = $item->type; ?>			
										<th><?php echo esc_html($item->label); ?></th>
										<?php $wlgf_file_header = $wlgf_file_header . esc_html($item->label.", "); ?>
									<?php endif; ?>
								<?php endforeach; ?>
								<!--- Print file column as last column of the table end -->
								<?php foreach ($structure as $item): ?>
								<?php if (property_exists($item, 'label') && in_array($item->type, ['file' ])): ?>
										<?php $wlgf_form_fields_sequence[] = $item->type; ?>			
										<th><?php echo esc_html($item->label); ?></th>
										<?php $wlgf_file_header = $wlgf_file_header . esc_html($item->label.", "); ?>
									<?php endif; ?>
								<?php endforeach; ?>
								<th><?php  echo esc_html_e( 'Date Time', 'lead-generation-form' ); ?></th>
								<th><?php  echo esc_html_e( 'Actions', 'lead-generation-form' ); ?></th>
								<th><input type="checkbox" id="wlgf-select-all" name="wlgf-select-all" title="<?php esc_attr_e( 'Select All', 'lead-generation-form' ); ?>"></th>
							</tr>
							<?php 
							// write header in file
							$wlgf_file_contents .= $wlgf_file_header . "\n";
							if ($wp_filesystem->put_contents($wlgf_file_create_path, $wlgf_file_contents, FS_CHMOD_FILE)) {
							    //echo "header written";
							}
							?>
						</thead>
						<?php
						//print_r($wlgf_form_fields_sequence);
						
						// Find the key of 'file' value and move it to the end array
						$key = array_search('file', $wlgf_form_fields_sequence);
						// If found
						if ($key !== false) {
							// Remove 'file' from the current position
							unset($wlgf_form_fields_sequence[$key]);
							
							// Add 'file' back to the end of the array
							$wlgf_form_fields_sequence[] = 'file';

							// Re-index the array
							$wlgf_form_fields_sequence = array_values($wlgf_form_fields_sequence);
						}
						//echo "<br>";
						//print_r($wlgf_form_fields_sequence);
						?>
						<tbody>
						<?php
							// Iterate through the data and display it in the table
							if(is_array($wlgf_form_data)){
								$wlgf_data_counter++;$wlgf_trap=100;
								foreach ($wlgf_form_data as $row_key => $row) {
									echo '<tr id="'.esc_attr($row_key).'">';
									//print_r($row);
									$wlgf_tr_counter = 0;
									foreach ($row as $key => $value) {
										if($key != "wlgf_form_id") {
											if (strpos($key, 'hidden') !== false) {
												// skip hidden field key
												$wlgf_tr_counter--;
											} else {
												$wlgf_file_type = $wlgf_form_fields_sequence[$wlgf_tr_counter];
												if(is_array($value)) {
													// check data type
													if($wlgf_file_type == 'checkbox-group'){
														$value = implode(" | ",$value);
														//echo '<td>'.esc_attr($value).'</td>';
														echo '<td>' . esc_html($value ?: 'N/A') . '</td>';  // Output "N/A" if value is empty
													}
												} else {
													if($wlgf_file_type == 'file'){
														$wlgf_img_url_escaped = esc_url($value);
														// Output the image directly
														echo '<td>';
														echo "<img src='" . esc_url($wlgf_img_url_escaped) . "' width='50' class='img-thumbnail' alt='Uploaded Image'>";
														echo '</td>';
													} else {
														echo '<td>' . esc_html($value ?: 'N/A') . '</td>';  // Output "N/A" if value is empty
													}
												}
												$wlgf_file_record = $wlgf_file_record . ($value.", ");
												//echo '<td>'.strpos($value, 'hidden').' - '. $value . '</td>';
											}
											$wlgf_tr_counter++;
										}
									}
									
									$wlgf_file_contents .= $wlgf_file_record . "\n";
									// Write the combined content back to the file
									if ($wp_filesystem->put_contents($wlgf_file_create_path, $wlgf_file_contents, FS_CHMOD_FILE)) {
									    //echo "record written";
									    $wlgf_file_record = "";
									}
									?>
									<td>
										<button onclick="return WLGF('<?php echo esc_attr($wlgf_form_id); ?>', 'delete', '<?php echo esc_attr($row_key); ?>');" type="button" class="btn btn-sm btn-outline-primary" title="delete row"><i class="fa-sharp fa-regular fa-trash-can"></i></button>
									</td>
									<td>
										<input type="checkbox" name="wlgf-lead-id" value="<?php echo esc_attr( $row_key ); ?>" title="<?php esc_html_e( 'Select Lead', 'lead-generation-form' ); ?>">
									</td>
									<?php
									echo '</tr>';
									$wlgf_data_counter++;if($wlgf_data_counter>$wlgf_trap){break;}
								}
								
							}
						?>
						</tbody>
						<thead>
							<tr>
								<?php foreach ($structure as $item): ?>
								<?php if (property_exists($item, 'label') && !in_array($item->type, ['header', 'button', 'paragraph', 'hidden', 'file'])): ?>
										<?php $wlgf_form_fields_sequence[] = $item->type; ?>			
										<th><?php echo esc_html($item->label); ?></th>
										<?php $wlgf_file_header = $wlgf_file_header . esc_html($item->label.", "); ?>
									<?php endif; ?>
								<?php endforeach; ?>
								<!--- Print file column as last column of the table end -->
								<?php foreach ($structure as $item): ?>
								<?php if (property_exists($item, 'label') && in_array($item->type, ['file' ])): ?>
										<?php $wlgf_form_fields_sequence[] = $item->type; ?>			
										<th><?php echo esc_html($item->label); ?></th>
										<?php $wlgf_file_header = $wlgf_file_header . esc_html($item->label.", "); ?>
									<?php endif; ?>
								<?php endforeach; ?>
								<th><?php  echo esc_html_e( 'Date Time', 'lead-generation-form' ); ?></th>
								<th><?php  echo esc_html_e( 'Actions', 'lead-generation-form' ); ?></th>
								<th><button type="button" class="btn btn-danger btn-sm" title="<?php esc_html_e( 'Delete Selected Leads', 'lead-generation-form' ); ?>" onclick="return WLGF('<?php echo esc_attr($wlgf_form_id); ?>', 'multiple', '');"><i class="fas fa-trash-alt"></i></button></th>
							</tr>
						</thead>
					</table>
					<?php if(is_array($wlgf_form_data) && count($wlgf_form_data)){ ?>
					<div class="my-3 text-center">
						<a href="<?php echo esc_url($wlgf_download_url); ?>" download="<?php echo esc_attr($wlgf_file_name); ?>" class="btn btn-lg btn-success"><i class="fa-sharp fa-solid fa-file-export"></i> <?php esc_html_e( 'Download All Data List', 'lead-generation-form' ); ?></a>
					</div>
					<?php
					}
				} // end of load if
				
				// delete record start
				if($wlgf_do == 'delete') {
					$wlgf_row_id = (isset($_POST['wlgf_row_id'])) ? sanitize_text_field( wp_unslash ($_POST['wlgf_row_id'] ) ) : '';
					
					if (isset($wlgf_row_id)) {
						//echo $wlgf_row_id;
						// load saved form data
						$wlgf_saved_form_data = get_option('wlgf_saved_form_data_'.$wlgf_form_id);
						if(is_array($wlgf_saved_form_data)){
							unset($wlgf_saved_form_data[$wlgf_row_id]);
							update_option('wlgf_saved_form_data_'.$wlgf_form_id, $wlgf_saved_form_data);
						}
					}
				}
				// delete record end
				
				// delete multiple lead record start
				if ($wlgf_do == 'multiple') {
					if (isset($_POST['wlgf_row_id']) && is_array($_POST['wlgf_row_id'])) {

						$unslashed_row_ids = wp_unslash($_POST['wlgf_row_id']);

						// Initialize an array to hold sanitized IDs
						$wlgf_row_id = array();
						foreach ($unslashed_row_ids as $row_id) {
							$wlgf_row_id[] = intval($row_id); // Sanitize to integer
						}

						// Load saved form data
						$wlgf_saved_form_data = get_option('wlgf_saved_form_data_' . $wlgf_form_id);
						if (is_array($wlgf_saved_form_data)) {
							foreach ($wlgf_row_id as $wlgf_single_id) {
								if (array_key_exists($wlgf_single_id, $wlgf_saved_form_data)) { // Check existence
									unset($wlgf_saved_form_data[$wlgf_single_id]); // Unset only if exists
								}
							}
							// Update option after modifications
							update_option('wlgf_saved_form_data_' . $wlgf_form_id, $wlgf_saved_form_data);
						}
					}
				}
				// delete multiple lead record end
				
			} // end if isset check wlgf_form_id, do
		} else {
			wp_die();
		}
		wp_die();
	}
}
add_action( 'wp_ajax_wlgf_lead_loader', 'wlgf_lead_loader_callback' );
// 3. all lead page action - load + file, delete, multiple delete [end]

// 4. import export form start
function wlgf_import_export_callback(){
	//print_r($_POST);
	if ( current_user_can( 'manage_options' ) ) {
		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ), 'wlgf-import-export' ) ) {
			
			$wlgf_form = array();
			$wlgf_form_id = isset($_POST['wlgf_form_id']) ? sanitize_text_field( wp_unslash( $_POST['wlgf_form_id'] ) ) : '';
			$wlgf_do_action = isset($_POST['do']) ? sanitize_text_field( wp_unslash( $_POST['do'] ) ) : '';

			// export form start
			if($wlgf_do_action == "export"){
				// $wlgf_form_details = array('form_id' => $wlgf_form_id, 'form_name' => $wlgf_form_name, 'form' => $wlgf_form);
				$wlgf_form_details = get_option("wlgf_form_".$wlgf_form_id);
				if (!empty($wlgf_form_details)) {
					echo "<textarea id='wlgf-form-code' class='form-control' rows='10' readonly>";
					print_r($wlgf_form_details['form']);
					echo "</textarea>";
				} else {
				    echo "Error: No data found for this option.";
				}
			}
			// export form end
			
			// import form start
			if($wlgf_do_action == "import"){
				$wlgf_form_id = 0;
				$wlgf_do_action = isset($_POST['do']) ? sanitize_text_field( wp_unslash( $_POST['do'] ) ) : '';
				$wlgf_form_name = isset($_POST['wlgf_form_name']) ? sanitize_text_field( wp_unslash( $_POST['wlgf_form_name'] ) ) : '';
				
				// start sanitized form structure data
				//$wlgf_form_data = isset( $_POST['wlgf_form_data'] ) ? json_decode( wp_unslash( $_POST['wlgf_form_data'] ), true ) : array();
				//$wlgf_form_data = isset( $_POST['wlgf_form_data'] ) ? json_decode( wp_unslash( sanitize_text_field( $_POST['wlgf_form_data'] ) ), true ) : array();
				//$wlgf_form_data = isset($_POST['wlgf_form_data']) ? json_decode(wp_unslash($_POST['wlgf_form_data']), true) : array();
				$wlgf_form_data = isset($_POST['wlgf_form_data']) ? json_decode(sanitize_text_field(wp_unslash($_POST['wlgf_form_data'])), true) : array();


				if ( is_array( $wlgf_form_data ) && ! empty( $wlgf_form_data ) ) {
					$wlgf_sanitized_form_data = array();

					foreach ( $wlgf_form_data as $field ) {
						if ( isset( $field['type'] ) ) {
							$sanitized_field = array();

							// Sanitize common fields
							$sanitized_field['type'] = sanitize_text_field( $field['type'] );
							$sanitized_field['label'] = sanitize_text_field( $field['label'] );
							$sanitized_field['className'] = sanitize_text_field( $field['className'] );
							$sanitized_field['required'] = isset( $field['required'] ) ? filter_var( $field['required'], FILTER_VALIDATE_BOOLEAN ) : false;

							// Sanitize based on field subtype/type
							switch ( $sanitized_field['type'] ) {
								case 'text':
								case 'textarea':
									$sanitized_field['value'] = sanitize_text_field( $field['value'] );
									$sanitized_field['placeholder'] = sanitize_text_field( $field['placeholder'] );
									if ( isset( $field['maxlength'] ) ) {
										$sanitized_field['maxlength'] = intval( $field['maxlength'] );
									}
								break;

								case 'email':
									$sanitized_field['value'] = sanitize_email( $field['value'] );
								break;

								case 'number':
									$sanitized_field['value'] = is_numeric( $field['value'] ) ? intval( $field['value'] ) : 0;
									$sanitized_field['min'] = isset( $field['min'] ) ? intval( $field['min'] ) : 0;
									$sanitized_field['max'] = isset( $field['max'] ) ? intval( $field['max'] ) : 100;
									$sanitized_field['step'] = isset( $field['step'] ) ? intval( $field['step'] ) : 1;
								break;

								case 'select':
								case 'radio-group':
								case 'checkbox-group':
									$sanitized_field['values'] = array_map( function( $option ) {
									return array(
											'label' => sanitize_text_field( $option['label'] ),
											'value' => sanitize_text_field( $option['value'] ),
											'selected' => isset( $option['selected'] ) ? filter_var( $option['selected'], FILTER_VALIDATE_BOOLEAN ) : false,
										);
									}, $field['values'] );
									break;

								case 'file':
									// For files, we can store the file name, but actual file upload handling needs more processing.
									$sanitized_field['value'] = sanitize_file_name( $field['value'] );
									break;

								case 'date':
									$sanitized_field['value'] = sanitize_text_field( $field['value'] );
									break;

								default:
									$sanitized_field['value'] = sanitize_text_field( $field['value'] );
									break;
							}

							$wlgf_sanitized_form_data[] = $sanitized_field;
						}
					}
				} // end sanitized form structure data
	
				
				if($wlgf_form_id == 0) $wlgf_form_id = wlgf_get_next_id(); // it's new form
				$wlgf_form = wp_json_encode($wlgf_form_data);
				$wlgf_form_details = array('form_id' => $wlgf_form_id, 'form_name' => $wlgf_form_name, 'form' => $wlgf_form);
				
				/* echo "<hr>";
				echo "<pre>";
				print_r($wlgf_form_details);
				echo "</pre>";
				 */
				 
				update_option("wlgf_form_".$wlgf_form_id, $wlgf_form_details );
			}
			// import form end
			
			// combine form start
			if($wlgf_do_action == "combine"){
				$wlgf_form_one_id = 0;
				$wlgf_form_two_id = 0;
				$wlgf_form_one_name = "";
				$wlgf_form_two_name = "";
				
				$wlgf_form_one_id = isset($_POST['wlgf_form_one_id']) ? sanitize_text_field( wp_unslash( $_POST['wlgf_form_one_id']) ) : '';
				$wlgf_form_two_id = isset($_POST['wlgf_form_two_id']) ? sanitize_text_field( wp_unslash( $_POST['wlgf_form_two_id']) ) : '';
				$wlgf_form_one_name = isset($_POST['wlgf_form_one_name']) ? sanitize_text_field( wp_unslash( $_POST['wlgf_form_one_name']) ) : '';
				$wlgf_form_two_name = isset($_POST['wlgf_form_two_name']) ? sanitize_text_field( wp_unslash( $_POST['wlgf_form_two_name']) ) : '';
				
				// form one data
				$wlgf_form_one = get_option("wlgf_form_".$wlgf_form_one_id);
				$wlgf_form_one_data = json_decode($wlgf_form_one['form']);
				
				// form two data
				$wlgf_form_two = get_option("wlgf_form_".$wlgf_form_two_id);
				$wlgf_form_two_data = json_decode($wlgf_form_two['form']);
				
				// combine form data
				$wlgf_form_id_new = wlgf_get_next_id(); //new form id
				$wlgf_form_name_new = sanitize_text_field($wlgf_form_one_name . " & " . $wlgf_form_two_name ." - [Combined]");
				$wlgf_form_data_new = json_encode(array_merge($wlgf_form_one_data, $wlgf_form_two_data));
				$wlgf_form_details_new = array('form_id' => $wlgf_form_id_new, 'form_name' => $wlgf_form_name_new, 'form' => $wlgf_form_data_new);
				
				/* echo "<hr>";
				echo "<pre>";
				print_r($wlgf_form_details_new);
				echo "</pre>"; */
				
				update_option("wlgf_form_".$wlgf_form_id_new, $wlgf_form_details_new );
			}
			// combine form end
		}
		wp_die();
	}
}
add_action( 'wp_ajax_wlgf_import_export', 'wlgf_import_export_callback' );
// 4. import export form end

// 5. save settings start
function wlgf_save_settings_callback(){
	if ( current_user_can( 'manage_options' ) ) {
		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ), 'wlgf-save-settings' ) ) {
			
			/* echo "<pre>";
			print_r($_POST);
			echo "</pre>"; */
			
			$wlgf_settings = array();
			$wlgf_recaptcha = isset($_POST['recaptcha']) ? sanitize_text_field( wp_unslash( $_POST['recaptcha'] ) ) : '';
			$wlgf_sitekey = isset($_POST['sitekey']) ? sanitize_text_field( wp_unslash( $_POST['sitekey']) ) : '';
			$wlgf_secretkey = isset($_POST['secretkey']) ? sanitize_text_field( wp_unslash( $_POST['secretkey']) ) : '';

			$wlgf_notify_admin = isset($_POST['notify_admin']) ? sanitize_text_field( wp_unslash( $_POST['notify_admin'] ) ) : '';
			$wlgf_email_engine = isset($_POST['email_engine']) ? sanitize_text_field( wp_unslash( $_POST['email_engine']) ) : '';
			$wlgf_smtp_host = isset($_POST['smtp_host']) ? sanitize_text_field( wp_unslash( $_POST['smtp_host']) ) : '';
			$wlgf_smtp_username = isset($_POST['smtp_username']) ? sanitize_text_field( wp_unslash( $_POST['smtp_username']) ) : '';
			$wlgf_smtp_password = isset($_POST['smtp_password']) ? sanitize_text_field( wp_unslash( $_POST['smtp_password']) ) : '';
			$wlgf_smtp_encryption = isset($_POST['smtp_encryption']) ? sanitize_text_field( wp_unslash( $_POST['smtp_encryption'] ) ) : '';
			$wlgf_smtp_port = isset($_POST['smtp_port']) ? sanitize_text_field( wp_unslash( $_POST['smtp_port'] ) ) : '';

			$wlgf_user_message = isset($_POST['user_message']) ? sanitize_textarea_field( wp_unslash( $_POST['user_message']) ) : '';

			$wlgf_msg_allowed_tags = array(
			    'p' => array(),
			    'strong' => array(),
			    'br' => array(),
			    'a' => array(),
			    'hr' => array(),
			    'i' => array()
			);
			$wlgf_user_message = wp_kses($wlgf_user_message, $wlgf_msg_allowed_tags);

			/* echo "<pre>";
			print_r($wlgf_settings);
			echo "</pre>"; */
			
			$wlgf_settings = array('recaptcha' => $wlgf_recaptcha, 'sitekey' => $wlgf_sitekey, 'secretkey' => $wlgf_secretkey,
			'notify_admin' => $wlgf_notify_admin, 'email_engine' => $wlgf_email_engine, 'smtp_host' => $wlgf_smtp_host,
			'smtp_username' => $wlgf_smtp_username, 'smtp_password' => $wlgf_smtp_password,
			'smtp_encryption' => $wlgf_smtp_encryption, 'smtp_port' => $wlgf_smtp_port, 'user_message' => $wlgf_user_message );
			
			update_option("wlgf_settings", $wlgf_settings );
		} else {
			wp_die();
		}
		wp_die();
	}
}
add_action( 'wp_ajax_wlgf_save_settings', 'wlgf_save_settings_callback' );
// 5. save settings end

// 6. smtp setup start
function wlgf_configure_smtp($wlgf_phpmailer){
	//load saved settings
	$wlgf_settings = get_option('wlgf_settings');
	$wlgf_email_engine = (isset($wlgf_settings['email_engine'])) ? sanitize_text_field($wlgf_settings['email_engine']) : '';
	if($wlgf_email_engine == 2) {
		$wlgf_smtp_host = (isset($wlgf_settings['smtp_host'])) ? sanitize_text_field($wlgf_settings['smtp_host']) : '';
		$wlgf_smtp_username = (isset($wlgf_settings['smtp_username'])) ? sanitize_text_field($wlgf_settings['smtp_username']) : '';
		$wlgf_smtp_password = (isset($wlgf_settings['smtp_password'])) ? sanitize_text_field($wlgf_settings['smtp_password']) : '';
		$wlgf_smtp_encryption = (isset($wlgf_settings['smtp_encryption'])) ? sanitize_text_field($wlgf_settings['smtp_encryption']) : '';
		$wlgf_smtp_port = (isset($wlgf_settings['smtp_port'])) ? sanitize_text_field($wlgf_settings['smtp_port']) : '';
		$wlgf_user_message = (isset($wlgf_settings['user_message'])) ? sanitize_text_field($wlgf_settings['user_message']) : '';
		
		$wlgf_phpmailer->isSMTP();     
		$wlgf_phpmailer->Host = $wlgf_smtp_host;
		$wlgf_phpmailer->SMTPAuth = true;
		$wlgf_phpmailer->Username = $wlgf_smtp_username;
		$wlgf_phpmailer->Password = $wlgf_smtp_password;
		$wlgf_phpmailer->SMTPSecure = $wlgf_smtp_encryption;
		$wlgf_phpmailer->Port = $wlgf_smtp_port;
	}
}
// 6. smtp setup end