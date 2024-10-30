<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; /* Exit if accessed directly */
}
//page assets
wp_enqueue_style( 'wlgf-manage-forms-css', plugin_dir_url(__FILE__) . 'admin/assets/css/manage-forms.css', array(), '1.0', 'all' );
wp_enqueue_script( 'wlgf-manage-forms-js', plugin_dir_url(__FILE__) . 'admin/assets/js/manage-forms.js', array('jquery'), '1.0', 'all' );
wp_add_inline_script( 'wlgf-manage-forms-js', 'const ManageForms = ' . wp_json_encode( array(
    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
    'nonce' => wp_create_nonce( 'wlgf-manage-forms' ),
)), 'before' );

wp_enqueue_style( 'wlgf-bootstrap-scoped-admin-css', plugin_dir_url(__FILE__) . 'assets/bootstrap-5.3.3/dist/css/bootstrap-scoped-admin.css', array(), '5.3.3', 'all' );
wp_enqueue_style( 'wlgf-fontawesome-css', plugins_url( 'assets/fontawesome-free-6.4.2-web/css/all.min.css', __FILE__ ), array(), '6.4.2', 'all' );
wp_enqueue_style( 'wlgf-datatables-min-css', plugin_dir_url(__FILE__). 'assets/datatables-2.0.8/datatables.min.css', array(), '2.0.8', 'all' );
wp_enqueue_script( 'wlgf-datatables-min-js', plugin_dir_url(__FILE__). 'assets/datatables-2.0.8/datatables.min.js', array('jquery'), '2.0.8' );


// get plugin version
$wlgf_current_version = get_option( 'wlgf_current_version' );
$wlgf_last_version    = get_option( 'wlgf_last_version' );

// get all forms list
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
	<div class="my-3">
		<h1><?php esc_html_e( 'Manage Form', 'lead-generation-form' ); ?>
		<a class="btn btn-primary py-2 float-end mx-3" href="admin.php?page=wlgf-form-generator"><i class="fa-sharp fa-regular fa-file-lines"></i> <?php esc_html_e( 'Create New Form', 'lead-generation-form' ); ?></a>
		</h1>
	</div>
	<table id="forms-tables" class="table table-hover" style="width:100%">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Form ID', 'lead-generation-form' ); ?></th>
				<th><?php esc_html_e( 'Form Name', 'lead-generation-form' ); ?></th>
				<th><?php esc_html_e( 'Form Shortcode', 'lead-generation-form' ); ?></th>
				<th><?php esc_html_e( 'Action', 'lead-generation-form' ); ?></th>
			</tr>
		</thead>
		<tbody id="wlgf-tbody">
			<?php
			// print form list
			foreach ( $wlgf_all_forms as $wlgf_form ) {
				$wlgf_form_key    = $wlgf_form->option_name;
				//print_r($wlgf_form);
				$wlgf_form_data = get_option($wlgf_form_key);
				$wlgf_form_id = sanitize_text_field($wlgf_form_data['form_id']);
				$wlgf_form_shortcode = sanitize_text_field("[WLFG id='$wlgf_form_id']");
				/* echo "<hr>";
				print_r($wlgf_form_data);
				echo $wlgf_form_data['form_id'];
				echo "<hr>"; */
				?>
			<tr id="<?php echo esc_attr($wlgf_form_id); ?>">
				<td><?php echo esc_html($wlgf_form_id); ?></td>
				<td><?php echo esc_html($wlgf_form_data['form_name']); ?></td>
				<td>
					<input type="text" id="wlgf-shortcode-<?php echo esc_attr( $wlgf_form_id ); ?>" class="btn btn-info btn-sm" value="<?php echo esc_attr( $wlgf_form_shortcode ); ?>">
					<button title="copy shortcode" id="wlgf-shortcode-<?php echo esc_attr( $wlgf_form_id ); ?>" onclick="return WLGF('<?php echo esc_attr($wlgf_form_id); ?>', 'copy');" type="button" class="btn btn-sm btn-outline-dark"><i class="fa-sharp fa-solid fa-copy"></i></button>
					<button class="btn btn-sm btn-light d-none wlgf-copied-<?php echo esc_attr( $wlgf_form_id ); ?>"><?php esc_html_e( 'Copied', 'lead-generation-form' ); ?></button>
				</td>
				<td>
					<button onclick="return WLGF('<?php echo esc_attr($wlgf_form_id); ?>', 'clone');" type="button" class="btn btn-sm btn-outline-primary" title="clone shortcode"><i class="fa-sharp fa-regular fa-clone"></i></button>
					<a href="?page=wlgf-form-generator&form-id=<?php echo esc_attr( $wlgf_form_id ); ?>&modify-nonce=<?php echo esc_attr( wp_create_nonce( 'wlgf-modify-form' )); ?>" type="button" class="btn btn-sm btn-outline-primary" title="modify"><i class="fa-sharp fa-solid fa-pen-to-square"></i></a>
					<?php if($wlgf_form_id != 1) { ?>
					<button onclick="return WLGF('<?php echo esc_attr($wlgf_form_id); ?>', 'delete');" type="button" class="btn btn-sm btn-outline-primary" title="delete"><i class="fa-sharp fa-regular fa-trash-can"></i></button>
					<?php } ?>
				</td>
			</tr>
			<?php
				}
			?>
		</tbody>
		<tfoot>
			<tr>
				<th><?php esc_html_e( 'Form ID', 'lead-generation-form' ); ?></th>
				<th><?php esc_html_e( 'Form Name', 'lead-generation-form' ); ?></th>
				<th><?php esc_html_e( 'Form Shortcode', 'lead-generation-form' ); ?></th>
				<th><?php esc_html_e( 'Action', 'lead-generation-form' ); ?></th>
			</tr>
		</tfoot>
	</table>
	
	<!-- Faq Start-->
	<div class="col-md-12 py-3">
		<div id="faq" class="mt-5">
			 <div class="bg-white p-2">
				<h6 class="card-header">
				<strong><?php esc_html_e( 'Please Read FAQ While Manage Forms', 'lead-generation-form' ); ?></strong>
				</h6>
				<hr>

				<p>
				<strong><?php esc_html_e( 'Q.1. What is the use of Clone Form features?', 'lead-generation-form' ); ?></strong><br>
				<?php esc_html_e( 'Using clone form features you can make a copy of an existing form.', 'lead-generation-form' ); ?>
				</p>
				
				<p>
				<strong><?php esc_html_e( 'Q.2. What are the primary guidelines for deleting a form?', 'lead-generation-form' ); ?></strong><br>
				<?php esc_html_e( 'When you want to delete a form, it is essential to be aware that all the leads captured through that specific form will be permanently removed along with the form itself.', 'lead-generation-form' ); ?>
				</p>
			 </div>
		</div>
	</div> <!-- Faq End-->
</div>