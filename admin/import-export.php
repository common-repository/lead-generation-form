<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; /* Exit if accessed directly */
}
wp_enqueue_style( 'wlgf-bootstrap-scoped-admin-css', plugin_dir_url(__FILE__) . 'assets/bootstrap-5.3.3/dist/css/bootstrap-scoped-admin.css', array(), '5.3.3', 'all' );
wp_enqueue_style( 'wlgf-fontawesome-css', plugins_url( 'assets/fontawesome-free-6.4.2-web/css/all.min.css', __FILE__ ), array(), '6.4.2', 'all' );

wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui-core');
wp_enqueue_script('jquery-effects-core');
wp_enqueue_script('jquery-effects-shake');

wp_enqueue_script( 'wlgf-import-export-js', plugin_dir_url(__FILE__) . 'admin/assets/js/import-export.js', array('jquery'), '1.0', 'all' );
wp_add_inline_script( 'wlgf-import-export-js', 'const ImportExport = ' . wp_json_encode( array(
    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
    'nonce' => wp_create_nonce( 'wlgf-import-export' ),
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
      <div class="my-3">
            <div class="container-fluid">
                  <div class="row">
                        <!-- Export Form -->
                        <div class="col-md-6 py-3">
                              <div class="p-3 bg-white">
                                    <div class="mb-3">
                                          <h3><?php esc_html_e( "Export A Form", 'lead-generation-form' ); ?></h3>
                                          <hr>
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
                                          <button id="wlgf-export-btn" onclick="return WLGF('', 'export');" type="button" class="btn btn-md btn-primary" title="export"><i class="fa-sharp fa-solid fa-file-export"></i> <?php esc_html_e( 'Export From', 'lead-generation-form' ); ?></button>
                                    </div>
                                    <div id="wlgf-export-loader" class="spinner-grow py-3 m-3 text-dark d-none" role="status">
                                          <span class="visually-hidden"></span>
                                    </div>
                                    <div id="wlgf-form-export" class="d-none py-3"></div>
                                    <button id="wlgf-copycode" onclick="return WLGF('', 'copy');" type="button" class="d-none btn btn-md btn-success" title="copy code"><i class="fa-sharp fa-solid fa-copy"></i> <?php esc_html_e( 'Copy Code', 'lead-generation-form' ); ?></button>
                                    <div id="wlgf-copy-success" class="alert alert-success mt-3 d-none"><?php esc_html_e( 'Code copied to clipboard.', 'lead-generation-form' ); ?></div>
                              </div>
                        </div>
                        
                        <!-- Combine Forms Start-->
                        <div class="col-md-6 py-3">
                              <div class="p-3 bg-white">
                                    <h3><?php esc_html_e( "Combine Two Form", 'lead-generation-form' ); ?></h3>
                                    <hr>
                                    <div class="mb-3">
                                          <label for="wlgf-form-one" class="form-label"><strong><?php esc_html_e( 'Form One', 'lead-generation-form' ); ?></strong> *</label>
                                          <select class="form-select form-select-lg mb-3" id="wlgf-form-one" name="wlgf-form-one" onchange="return WLGF(this.value, 'load', '');">
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
                                    <div class="mb-3">
                                          <label for="wlgf-form-two" class="form-label"><strong><?php esc_html_e( 'Form Two', 'lead-generation-form' ); ?></strong> *</label>
                                          <select class="form-select form-select-lg mb-3" id="wlgf-form-two" name="wlgf-form-two" onchange="return WLGF(this.value, 'load', '');">
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
                                    <div class="mb-3">
                                      <button id="wlgf-combine-btn" onclick="return WLGF('', 'combine');" type="button" class="btn btn-md btn-primary" title="import"><i class="fa-sharp fa-regular fa-file"></i> <i class="fa-sharp fa-solid fa-plus"></i> <i class="fa-sharp fa-regular fa-file"></i> <?php esc_html_e( 'Combine From', 'lead-generation-form' ); ?></button>
                                    </div>
                                    <div id="wlgf-combine-loader" class="spinner-grow py-3 m-3 text-dark d-none" role="status">
                                      <span class="visually-hidden"></span>
                                    </div>
                                    <div id="wlgf-combine-success" class="alert alert-success mt-3 d-none"><?php esc_html_e( 'Form combined successfully. Go to the Manage Forms menu to manage combined forms.', 'lead-generation-form' ); ?></div>
                              </div>
                        </div>
                        <!-- Combine Forms End-->
                        
                                                <!-- Import Form -->
                        <div class="col-md-6 py-3">
                              <div class="p-3 bg-white">
                                    <h3><?php esc_html_e( "Import A Form", 'lead-generation-form' ); ?></h3>
                                    <hr>
                                    <div class="mb-3">
                                          <label for="wlgf-form-name" class="form-label"><strong><?php esc_html_e( 'Form Name', 'lead-generation-form' ); ?></strong> *</label>
                                          <input class="form-control" id="wlgf-form-name" name="wlgf-form-name" type="text" value="" placeholder="<?php esc_html_e( 'Type a name for the form', 'lead-generation-form' ); ?>">
                                    </div>
                                    <div class="mb-3">
                                          <label for="wlgf-form-data" class="form-label"><strong><?php esc_html_e( 'Paste Form Data', 'lead-generation-form' ); ?></strong> *</label>
                                          <textarea class="form-control" id="wlgf-form-data" name="wlgf-form-data" rows="10" type="text" placeholder="<?php esc_html_e( 'Important Note: Please paste the copied Form data in JSON format only.', 'lead-generation-form' ); ?>"></textarea>
                                    </div>
                                    <div class="mb-3">
                                          <button id="wlgf-import-btn" onclick="return WLGF('', 'import');" type="button" class="btn btn-md btn-primary" title="import"><i class="fa-sharp fa-solid fa-file-export"></i> <?php esc_html_e( 'Import From', 'lead-generation-form' ); ?></button>
                                    </div>
                                    <div id="wlgf-import-loader" class="spinner-grow py-3 m-3 text-dark d-none" role="status">
                                          <span class="visually-hidden"></span>
                                    </div>
                                    <div id="wlgf-import-success" class="alert alert-success mt-3 d-none"><?php esc_html_e( 'Form imported successfully', 'lead-generation-form' ); ?></div>
                              </div>
                        </div>
                        
                        <!-- Faq Start-->
                        <div class="col-md-6">
                              <div id="faq" class="mt-3">
                                    <div class="bg-white p-2">
                                          <h6 class="card-header">
                                          <strong><?php esc_html_e( 'Please Read FAQ While Import & Export and Combining Forms', 'lead-generation-form' ); ?></strong>
                                          </h6>
                                          <hr>
                                          
                                          <p>
                                          <strong><?php esc_html_e( 'Q.1. What is the use of import, export, and combine forms features?', 'lead-generation-form' ); ?></strong><br>
                                          <?php esc_html_e( 'You can use Export Form features for exporting forms from one website to another website where the Lead Generation Form plugin is installed.', 'lead-generation-form' ); ?><br>
                                          <?php esc_html_e( 'Using the Import Form feature, you can import via copied code.', 'lead-generation-form' ); ?><br>
                                          <?php esc_html_e( 'The Combined Form feature is utilized to merge two forms into one form.', 'lead-generation-form' ); ?>
                                          </p>
                                          
                                          <p>
                                          <strong><?php esc_html_e( 'Q.2. What are the primary guidelines for exporting forms?', 'lead-generation-form' ); ?></strong><br>
                                          <?php esc_html_e( 'While importing a form, always use the Copy Code button to copy the form code.', 'lead-generation-form' ); ?>
                                          </p>
                                          
                                          <p>
                                          <strong><?php esc_html_e( 'Q.3. What are the primary guidelines for import forms?', 'lead-generation-form' ); ?></strong><br>
                                          <?php esc_html_e( 'Always paste the copied JSON object data for importing.', 'lead-generation-form' ); ?><br>
                                          <?php esc_html_e( 'Adding other than JSON object data for importing Forms will result in an error.', 'lead-generation-form' ); ?>
                                          </p>

                                          <p>
                                          <strong><?php esc_html_e( 'Q.4. Can we import and export Forms from any third-party plugins like Contact Form 7?', 'lead-generation-form' ); ?></strong><br>
                                          <?php esc_html_e( "No, you can't import or export Forms from other or third-party plugins.", 'lead-generation-form' ); ?><br>
                                          <?php esc_html_e( 'You can only import or export Forms into the Lead Generation Form plugin.', 'lead-generation-form' ); ?>
                                          </p>

                                          <p>
                                          <strong><?php esc_html_e( 'Q.5. What are the primary guidelines for combining forms?', 'lead-generation-form' ); ?></strong><br>
                                          <?php esc_html_e( 'The key instruction is to review and refine the resulting form by eliminating any redundant submit buttons if they are present.', 'lead-generation-form' ); ?>
                                          </p>
                                    </div>
                              </div>
                        </div> <!-- Faq End-->
                  </div>
            </div>
      </div>
</div>