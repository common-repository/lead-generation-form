<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; /* Exit if accessed directly */
}

wp_enqueue_style( 'we-lgf-bootstrap-scoped-admin-css', plugin_dir_url(__FILE__) . 'assets/bootstrap-5.3.1/dist/css/bootstrap-scoped-admin.css', array(), '5.3.1', 'all' );
wp_enqueue_style( 'we-lgf-fontawesome-css', plugins_url( 'assets/fontawesome-free-6.4.2-web/css/all.min.css', __FILE__ ), array(), '6.4.2', 'all' );
wp_enqueue_style( 'we-lgf-datatables-bootstrap5-min-css', plugin_dir_url(__FILE__). 'assets/datatables/datatables.bootstrap5.min.css', array(), '5.0.0', 'all' );

wp_enqueue_script( 'we-lgf-datatables-min-js', plugin_dir_url(__FILE__). 'assets/datatables/datatables.min.js', array('jquery'), '1.13.6' );
wp_enqueue_script( 'we-lgf-datatables-bootstrap5-min-js', plugin_dir_url(__FILE__). 'assets/datatables/datatables.bootstrap5.min.js', array('jquery'), '5.0.0' );

// get plugin version
$we_lgf_current_version = get_option( 'we_lgf_current_version' );
$we_lgf_last_version    = get_option( 'we_lgf_last_version' );

// all forms list
global $wpdb;
$lgf_options_table_name = "{$wpdb->prefix}options";
$lgf_form_key        = 'lgf_form_';
// reference : https://wordpress.stackexchange.com/questions/8825/how-do-you-properly-prepare-a-like-sql-statement
$lgf_all_forms = $wpdb->get_results(
	$wpdb->prepare( "SELECT option_name FROM `$wpdb->options` WHERE `option_name` LIKE %s ORDER BY option_id ASC", '%' . $lgf_form_key . '%' )
);
//print_r($lgf_all_forms);

// load all saved form data
$lgf_saved_form_data_key        = 'lgf_saved_form_data';
$lgf_all_saved_forms = $wpdb->get_results(
	$wpdb->prepare( "SELECT * FROM `$wpdb->options` WHERE `option_name` LIKE %s ORDER BY option_id ASC", '%' . $lgf_saved_form_data_key . '%' )
);
/* echo "<pre>";
print_r($lgf_all_saved_forms);
echo "</pre>"; */
?>
<div class="mt-3">
	<h3><?php esc_html_e( 'Documentation', 'lead-generation-form' ); ?><?php esc_html_e( 'Select Form', 'lead-generation-form' ); ?></h3>
	<select class="form-select form-select-lg mb-3" name="lgf-form-list">
		<option value="0"><?php esc_html_e( 'Select Form', 'lead-generation-form' ); ?></option>
	<?php
	// print form list
	foreach ( $lgf_all_forms as $lgf_form ) {
		$lgf_form_key    = $lgf_form->option_name;
		//print_r($lgf_form);
		$lgf_form_data = get_option($lgf_form_key);
		?>
		<option value="<?php echo esc_html($lgf_form_data['form_id']); ?>"><?php echo esc_html($lgf_form_data['form_name']); ?></option>
		<?php
		}
	?>
	</select>
</div>

<?php
// print table columns accordingly to form structure start
$lgf_form_id = 1;
$lgf_form_body = get_option('lgf_form_'.$lgf_form_id);
$lgf_form = $lgf_form_body['form'];
$lgf_form_data = get_option('lgf_saved_form_data_'.$lgf_form_id);
//echo "<pre>";
//print_r($lgf_form_data);
//echo "</pre>";
$data = json_decode($lgf_form);
?>

<table class="table table-responsive" border="1">
    <thead>
        <tr>
            <?php foreach ($data as $item): ?>
            <?php if (property_exists($item, 'label') && !in_array($item->type, ['header', 'button', 'paragraph'])): ?>
                    <th><?php echo esc_html($item->label); ?></th>
                <?php endif; ?>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
		<?php
			// Iterate through the data and display it in the table
			if(is_array($lgf_form_data)){
				foreach ($lgf_form_data as $row) {
					echo '<tr>';
					foreach ($row as $key => $value) {
						if($key != "lgf_form_id") {
							echo '<td>' . esc_html($value) . '</td>';
						}
					}
					echo '</tr>';
				}
			}
		?>
    </tbody>
</table>