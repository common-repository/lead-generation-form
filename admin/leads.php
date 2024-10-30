<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; /* Exit if accessed directly */
}

wp_enqueue_style( 'wlgf-bootstrap-scoped-admin-css', plugin_dir_url(__FILE__) . 'assets/bootstrap-5.3.3/dist/css/bootstrap-scoped-admin.css', array(), '5.3.3', 'all' );
wp_enqueue_style( 'wlgf-fontawesome-css', plugins_url( 'assets/fontawesome-free-6.4.2-web/css/all.min.css', __FILE__ ), array(), '6.4.2', 'all' );
wp_enqueue_style( 'wlgf-datatables-min-css', plugin_dir_url(__FILE__). 'assets/datatables-2.0.8/datatables.min.css', array(), '2.0.8', 'all' );
wp_enqueue_script( 'wlgf-datatables-min-js', plugin_dir_url(__FILE__). 'assets/datatables-2.0.8/datatables.min.js', array('jquery'), '2.0.8' );

//page assets
wp_enqueue_style( 'wlgf-leads-css', plugin_dir_url(__FILE__) . 'admin/assets/css/leads.css', array(), '1.0', 'all' );
wp_enqueue_script( 'wlgf-leads-js', plugin_dir_url(__FILE__) . 'admin/assets/js/leads.js', array('jquery'), '1.0', true );
wp_add_inline_script( 'wlgf-leads-js', 'const Leads = ' . wp_json_encode( array(
    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
    'nonce' => wp_create_nonce( 'wlgf-leads' ),
)), 'before' );

// get plugin version
$wlgf_current_version = get_option( 'wlgf_current_version' );
$wlgf_last_version    = get_option( 'wlgf_last_version' );

// all forms list
global $wpdb;
$wlgf_options_table_name = "{$wpdb->prefix}options";
$wlgf_form_key        = 'wlgf_form_';
// reference : https://wordpress.stackexchange.com/questions/8825/how-do-you-properly-prepare-a-like-sql-statement
$wlgf_all_forms = $wpdb->get_results(
	$wpdb->prepare( "SELECT option_name FROM `$wpdb->options` WHERE `option_name` LIKE %s ORDER BY option_id ASC", '%' . $wlgf_form_key . '%' )
);
//print_r($wlgf_all_forms);
?>
<div class="wpfrank-lgf">
	<div class="mt-3">
		<h3><?php esc_html_e( "Select a Form to Access Its Captured Leads", 'lead-generation-form' ); ?></h3>
		<select class="form-select form-select-lg mb-3" id="wlgf-form-list" name="wlgf-form-list" onchange="return WLGF(this.value, 'load', '');">
			<option value="0"><?php esc_html_e( 'Select Form', 'lead-generation-form' ); ?></option>
			<?php
			// print form list
			foreach ( $wlgf_all_forms as $wlgf_form ) {
				$wlgf_form_key    = $wlgf_form->option_name;
				//print_r($wlgf_form);
				$wlgf_form_data = get_option($wlgf_form_key);
				?>
				<option value="<?php echo esc_attr($wlgf_form_data['form_id']); ?>"><?php echo esc_html($wlgf_form_data['form_name']); ?></option>
				<?php
				}
			?>
		</select>
	</div>
	<div id="wlgf-loader-process" class="wpfrank-lgf spinner-grow m-3 text-dark d-none" role="status">
		<span class="visually-hidden"></span>
	</div>

	<div id="wlgf-table-container" class="mt-3">
	</div>

	<div id="wlgf-notice" class="mt-5">
		<div class="alert alert-warning" role="alert">
			<strong>NOTE:</strong> In Free plugin you are allowed to access 100 records per from. Upgrade to Pro for get unlimited records.
			<a href="https://webenvo.com/premium-wordpress-plugins/" target="_new"><span class="badge text-bg-warning p-2">Get Lead Generation Form Pro</span></a>
		</div>
	</div>
	
	<div id="faq" class="my-2">
		<div class="bg-white p-2">
			<h6 class="card-header">
			<strong><?php esc_html_e( 'Please Read FAQ While Managing All Leads', 'lead-generation-form' ); ?></strong>
			</h6>
			<hr>
			
			<p>
			<strong><?php esc_html_e( 'Q.1. How do you filter data tables?', 'lead-generation-form' ); ?></strong><br>
			<?php esc_html_e( 'Use the search button located at the top corner of the table.', 'lead-generation-form' ); ?><br>
			<?php esc_html_e( 'Enter your criteria in the search box to filter data and rows.', 'lead-generation-form' ); ?><br>
			<?php esc_html_e( 'To sort the table, click on the desired column header or label.', 'lead-generation-form' ); ?>
			</p>
			
			<p>
			<strong><?php esc_html_e( 'Q.2. How can you download the complete list of leads data?', 'lead-generation-form' ); ?></strong><br>
			<?php esc_html_e( 'Choose the desired Form from the list.', 'lead-generation-form' ); ?><br>
			<?php esc_html_e( 'If you have a saved record, a download button will appear below the table.', 'lead-generation-form' ); ?><br>
			<?php esc_html_e( 'Click on this button to download the list.', 'lead-generation-form' ); ?>
			</p>
			
			<p>
			<strong><?php esc_html_e( 'Q.3. Can I download the filtered data list?', 'lead-generation-form' ); ?></strong><br>
			<?php esc_html_e( 'No, downloading the filtered data list is not possible.', 'lead-generation-form' ); ?><br>
			<?php esc_html_e( 'The download provides a complete list of leads in CSV format.', 'lead-generation-form' ); ?><br>
			<?php esc_html_e( 'However, once you open the CSV file in Microsoft Excel, you can apply filters to the data.', 'lead-generation-form' ); ?>
			</p>
		</div>
	</div>
</div>