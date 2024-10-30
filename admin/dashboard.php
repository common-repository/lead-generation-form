<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; /* Exit if accessed directly */
}
// get plugin version
$wlgf_current_version = get_option( 'wlgf_current_version' );
$wlgf_last_version    = get_option( 'wlgf_last_version' );

wp_enqueue_style( 'wlgf-bootstrap-scoped-admin-css', plugin_dir_url(__FILE__) . 'assets/bootstrap-5.3.3/dist/css/bootstrap-scoped-admin.css', array(), '5.3.3', 'all' );
wp_enqueue_style( 'wlgf-fontawesome-css', plugins_url( 'assets/fontawesome-free-6.4.2-web/css/all.min.css', __FILE__ ), array(), '6.4.2', 'all' );

// all forms list
$wlgf_form_count = 0;
$wlgf_lead_count = 0;

global $wpdb;
$wlgf_options_table_name = "{$wpdb->prefix}options";
$wlgf_form_key        = 'wlgf_form_';
// reference : https://wordpress.stackexchange.com/questions/8825/how-do-you-properly-prepare-a-like-sql-statement
$wlgf_all_forms = $wpdb->get_results(
	$wpdb->prepare( "SELECT option_name FROM `$wlgf_options_table_name` WHERE `option_name` LIKE %s ORDER BY option_id ASC", '%' . $wlgf_form_key . '%' )
);
if(is_array($wlgf_all_forms)) {
	$wlgf_form_count = count($wlgf_all_forms);
	foreach ( $wlgf_all_forms as $wlgf_form ) {
		$wlgf_form_key = $wlgf_form->option_name;
		$wlgf_form_data = get_option($wlgf_form_key);
		$wlgf_form_id = sanitize_text_field($wlgf_form_data['form_id']);
		$wlgf_saved_data = get_option('wlgf_saved_form_data_'.$wlgf_form_id);
		if(is_array($wlgf_saved_data)) {
			$wlgf_lead_count = $wlgf_lead_count + count($wlgf_saved_data);
		}
	}
}
?>
<div class="wpfrank-lgf">
	<div class="my-3">
		<div class="container-fluid">
			<div class="row text-center">
				<div class="col-md-12">
					<div class="py-3">
						<h1>Lead Generation Form</h1>
						<h3>v<?php echo esc_html($wlgf_last_version); ?></h3>
						<p class="h6">Ultimate solution for harnessing the power of dynamic forms to capture valuable leads from your prospective customers.</p>
					</div>
				</div>
				<!--
				<div class="col-md-2"><p class="h5">Reponsive Forms Design</p></div>
				<div class="col-md-2"><p class="h5">Create Unlimited Forms</p></div>
				<div class="col-md-2"><p class="h5">Accept Unlimited Query</p></div>
				<div class="col-md-2"><p class="h5">Export Lead CSV List</p></div>
				<div class="col-md-2"><p class="h5">Bootstrap v5 Design</p></div>
				<div class="col-md-2"><p class="h5">Fontawesome v6 Icons</p></div>
				-->
			</div>
		
			<div class="row">
				<!--
				<div class="col-md-12">
					<h1 class="h2 pt-3">Dashboard</h1>
				</div>
				-->
				<div class="col-md-6 py-3">
					<div class="p-5 bg-light text-center rounded-3">
						<p class="h5 py-2"><?php esc_html_e( 'Total From Created', 'lead-generation-form' ); ?></p>
						<p class="h2 py-2"><?php echo esc_html($wlgf_form_count); ?></p>
						<a class="btn btn-primary py-2" href="admin.php?page=wlgf-form-generator"><i class="fa-sharp fa-regular fa-file-lines"></i> <?php esc_html_e( 'Create New Form', 'lead-generation-form' ); ?></a>&nbsp;&nbsp;
						<a class="btn btn-secondary py-2" href="admin.php?page=wlgf-manage-forms"><i class="fa-sharp fa-solid fa-file-pen"></i> <?php esc_html_e( 'Manage Forms', 'lead-generation-form' ); ?></a>
					</div>
				</div>
				<div class="col-md-6 py-3">
					<div class="p-5 bg-light text-center rounded-3">
						<p class="h5 py-2"><?php esc_html_e( 'Total Lead Captured', 'lead-generation-form' ); ?></p>
						<p class="h2 py-2"><?php echo esc_html($wlgf_lead_count); ?></p>
						<a class="btn btn-success py-2" href="admin.php?page=wlgf-leads"><i class="fa-sharp fa-solid fa-list-check"></i> <?php esc_html_e( 'Manage Leads', 'lead-generation-form' ); ?></a>
					</div>
				</div>
				<div class="col-md-6 py-3">
					<div class="p-3 bg-light rounded-3">
						<p class="h5 py-2"><?php esc_html_e( 'Getting Started Tips', 'lead-generation-form' ); ?></p>
						<p class="h6 py-2">1. <?php esc_html_e( 'Create a new form using the FORM GENERATOR menu', 'lead-generation-form' ); ?></p>
						<p class="h6 py-2">2. <?php esc_html_e( 'Copy a form shortcode from the MANAGE FORMS menu', 'lead-generation-form' ); ?></p>
						<p class="h6 py-2">3. <?php esc_html_e( 'Publish form shortcode on a page or post', 'lead-generation-form' ); ?></p>
						<p class="h6 py-2">4. <?php esc_html_e( 'Manage captured leads and saved data on the ALL CAPTURED LEADS menu', 'lead-generation-form' ); ?></p>
						<p class="h6 py-2">5. <?php esc_html_e( 'Using the SETTINGS menu page you can configure the settings', 'lead-generation-form' ); ?></p>
					</div>
				</div>
				<div class="col-md-6 py-3">
					<div class="p-3 bg-light rounded-3">
						<p class="h5 py-2"><?php esc_html_e( 'Documentation', 'lead-generation-form' ); ?></p>
						<p class="h6 py-2"><?php esc_html_e( 'Please refer to our video docs for more help and tutorial videos about plugins.', 'lead-generation-form' ); ?></p>
						<a class="btn btn-danger py-2" href="https://www.youtube.com/@WPFrankOfficial/videos" target="_blank"><i class="fa-brands fa-youtube fa-beat"></i> <?php esc_html_e( 'Visit Our YouTube Channel', 'lead-generation-form' ); ?></a>
						<p class="h6 py-2"><?php esc_html_e( 'Read plugin change logs.', 'lead-generation-form' ); ?></p>
						<?php $wlgf_changelog_file = plugin_dir_url(__FILE__) .'../change-log.txt'; ?>
						<a class="btn btn-success py-2" href="<?php echo esc_url($wlgf_changelog_file); ?>" target="_blank"><i class="fa-sharp fa-solid fa-book"></i> v<?php echo esc_html($wlgf_last_version); ?> <?php esc_html_e( 'Plugin Change Logs', 'lead-generation-form' ); ?></a>
					</div>
				</div>
				
			</div>
		</div>
	</div>
</div>