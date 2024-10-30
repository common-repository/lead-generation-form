<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; /* Exit if accessed directly */
}

wp_enqueue_style( 'wlgf-bootstrap-scoped-admin-css', plugin_dir_url(__FILE__) . 'assets/bootstrap-5.3.3/dist/css/bootstrap-scoped-admin.css', array(), '5.3.3', 'all' );
wp_enqueue_style( 'wlgf-fontawesome-css', plugins_url( 'assets/fontawesome-free-6.4.2-web/css/all.min.css', __FILE__ ), array(), '6.4.2', 'all' );

wp_enqueue_script( 'wlgf-settings-js', plugin_dir_url(__FILE__) . 'admin/assets/js/settings.js', array('jquery'), '1.0', 'all' );
wp_add_inline_script( 'wlgf-settings-js', 'const Settings = ' . wp_json_encode( array(
    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
    'nonce' => wp_create_nonce( 'wlgf-save-settings' ),
)), 'before' );

// get plugin version
$wlgf_current_version = get_option( 'wlgf_current_version' );
$wlgf_last_version    = get_option( 'wlgf_last_version' );

//load saved settings
$wlgf_settings = get_option('wlgf_settings');
//print_r($wlgf_settings);
$wlgf_recaptcha = (isset($wlgf_settings['recaptcha'])) ? sanitize_text_field($wlgf_settings['recaptcha']) : 2;
$wlgf_sitekey = (isset($wlgf_settings['sitekey'])) ? sanitize_text_field($wlgf_settings['sitekey']) : '';
$wlgf_secretkey = (isset($wlgf_settings['secretkey'])) ? sanitize_text_field($wlgf_settings['secretkey']) : '';

$wlgf_notify_admin = (isset($wlgf_settings['notify_admin'])) ? sanitize_text_field($wlgf_settings['notify_admin']) : '2';
$wlgf_email_engine = (isset($wlgf_settings['email_engine'])) ? sanitize_text_field($wlgf_settings['email_engine']) : '';
$wlgf_smtp_host = (isset($wlgf_settings['smtp_host'])) ? sanitize_text_field($wlgf_settings['smtp_host']) : '';
$wlgf_smtp_username = (isset($wlgf_settings['smtp_username'])) ? sanitize_text_field($wlgf_settings['smtp_username']) : '';
$wlgf_smtp_password = (isset($wlgf_settings['smtp_password'])) ? sanitize_text_field($wlgf_settings['smtp_password']) : '';
$wlgf_smtp_encryption = (isset($wlgf_settings['smtp_encryption'])) ? sanitize_text_field($wlgf_settings['smtp_encryption']) : '';
$wlgf_smtp_port = (isset($wlgf_settings['smtp_port'])) ? sanitize_text_field($wlgf_settings['smtp_port']) : '';

$wlgf_user_message = (isset($wlgf_settings['user_message'])) ? sanitize_text_field($wlgf_settings['user_message']) : __('Thank you! Your query has been successfully submitted.', 'lead-generation-form');
?>
<div class="wpfrank-lgf">
	<div class="mt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="mb-3">
						<h3><?php esc_html_e( 'Settings Panel', 'lead-generation-form' ); ?></h3>
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="p-3 bg-white">
						
						<div class="mb-3 pt-3">
							<h4 for="message" class="form-label"><?php esc_html_e( 'Message', 'lead-generation-form' ); ?></h4>
							<hr>
						</div>
						<div class="mb-3">
							<label for="user_message" class="form-label"><?php esc_html_e( 'Message To User', 'lead-generation-form' ); ?></label>
							<textarea class="form-control" rows="5" id="user_message" name="user_message"><?php echo esc_html($wlgf_user_message); ?></textarea>
							<p><?php esc_html_e( 'After successful form submission shows a message to the user, allowed HTML tags are p, strong, i, br, a, and hr.', 'lead-generation-form' ); ?></p>
						</div>
						
						<div class="mb-3">
							<h4 for="recaptcha" class="form-label">reCAPTCHA Version 3</h4>
							<hr>
						</div>
						<div class="mb-3">
							<label for="recaptcha" class="form-label"><strong><?php esc_html_e( 'Recaptcha Status', 'lead-generation-form' ); ?></strong></label><br>
							<select class="form-select" id="recaptcha" name="recaptcha" disabled>
								<option value="1" <?php if($wlgf_recaptcha == 1) echo esc_attr('selected'); ?>><?php esc_html_e( 'ON', 'lead-generation-form' ); ?></option>
								<option value="2" selected><?php esc_html_e( 'OFF', 'lead-generation-form' ); ?></option>
							</select>
							<p><strong>IMPORTANT NOTE</strong>:<br> We are preforming Regression Testing on reCaptcha feature. This will be available in upcoming versions.</p>
						</div>
						<div id="site-key" class="mb-3 d-none">
							<label for="sitekey" class="form-label"><strong>Site Key</strong></label>
							<input type="text" class="form-control" id="sitekey" name="sitekey" value="" disabled>
						</div>
						<div id="secret-key" class="mb-3 d-none">
							<label for="secretkey" class="form-label"><strong>Secret Key</strong></label>
							<input type="text" class="form-control" id="secretkey" name="secretkey" value="" disabled>
							<p><?php esc_html_e( 'How do we get the Google reCAPTCHA Site Key and Secret Key?' , 'lead-generation-form' ); ?> <a href="https://www.youtube.com/watch?v=wCliSGd-rfQ" target="_new">click here</a></p>
						</div>
						
					</div>
				</div>
				
				<div class="col-md-6 ">
					<div class="p-3 bg-white">
						<div class="mb-3">
							<h4 for="email" class="form-label"><?php esc_html_e( 'Email', 'lead-generation-form' ); ?></h4>
							<hr>
						</div>
						<div class="mb-3">
							<label for="notify_admin" class="form-label"><strong><?php esc_html_e( 'Notify To Admin', 'lead-generation-form' ); ?></strong></label><br>
							<select class="form-select" id="notify_admin" name="notify_admin">
								<option value="1" <?php if($wlgf_notify_admin == 1) echo esc_attr('selected'); ?>><?php esc_html_e( 'ON', 'lead-generation-form' ); ?></option>
								<option value="2" <?php if($wlgf_notify_admin == 2) echo esc_attr('selected'); ?>><?php esc_html_e( 'OFF', 'lead-generation-form' ); ?></option>
							</select>
							<p><?php esc_html_e( 'After successful lead captures an email notification is sent to admin with lead data.', 'lead-generation-form' ); ?></p>
						</div>
						<div class="mb-3 d-none email">
							<label for="email_engine" class="form-label"><strong><?php esc_html_e( 'Email Via', 'lead-generation-form' ); ?></strong></label><br>
							<select class="form-select" id="email_engine" name="email_engine">
								<option value="1" <?php if($wlgf_email_engine == 1) echo esc_attr('selected'); ?>>WP MAIL</option>
								<option value="2" <?php if($wlgf_email_engine == 2) echo esc_attr('selected'); ?>>SMTP</option>
							</select>
						</div>
						<div class="mb-3 d-none smtp">
							<label for="smtp_host" class="form-label"><strong>SMTP Host</strong></label>
							<input type="text" class="form-control" id="smtp_host" name="smtp_host" value="<?php echo esc_attr($wlgf_smtp_host); ?>">
							<p><?php esc_html_e( 'For Gmail use smtp.gmail.com as SMTP Host.', 'lead-generation-form' ); ?></p>
						</div>
						
						<div class="mb-3 d-none smtp">
							<label for="smtp_username" class="form-label"><strong>SMTP <?php esc_html_e( 'Username', 'lead-generation-form' ); ?></strong></label>
							<input type="text" class="form-control" id="smtp_username" name="smtp_username" value="<?php echo esc_attr($wlgf_smtp_username); ?>">
							<p><?php esc_html_e( 'For Gmail, your email address example@gmail.com is used as an SMTP Username.', 'lead-generation-form' ); ?></p>
						</div>
						
						<div class="mb-3 d-none smtp">
							<label for="smtp_password" class="form-label"><strong>SMTP <?php esc_html_e( 'Password', 'lead-generation-form' ); ?></strong></label>
							<input type="password" class="form-control" id="smtp_password" name="smtp_password" value="<?php echo esc_attr($wlgf_smtp_password); ?>">
							<p><?php esc_html_e( 'How to get a Gmail SMTP Password?', 'lead-generation-form' ); ?> <a href="https://www.youtube.com/watch?v=jjDNndrl7_U" target="_new">click here</a></p>
						</div>
						
						<div class="mb-3 d-none smtp">
							<label for="smtp_encryption" class="form-label"><strong>Encryption <?php esc_html_e( 'Encryption', 'lead-generation-form' ); ?></strong></label><br>
							<select class="form-select" id="smtp_encryption" name="smtp_encryption">
								<option value="1" <?php if($wlgf_smtp_encryption == 1) echo esc_attr('selected'); ?>>TLS</option>
								<option value="2" <?php if($wlgf_smtp_encryption == 2) echo esc_attr('selected'); ?>>SSL</option>
							</select>
							<p><?php esc_html_e( 'Encryption will be used when sending an email.', 'lead-generation-form' ); ?></p>
						</div>
						
						<div class="mb-3 d-none smtp">
							<label for="smtp_port" class="form-label"><strong>SMTP Port</strong></label>
							<input type="number" class="form-control" id="smtp_port" name="smtp_port" value="<?php echo esc_attr($wlgf_smtp_port); ?>">
							<p><?php esc_html_e( 'The port which will be used when sending an email. If you choose TLS it should be set to 587. For SSL use port 465 instead.', 'lead-generation-form' ); ?></p>
						</div>
					</div>
				</div>
				
				<div class="col-md-12">
					<div class="my-3">
						<button onclick="return wlgf_save_setting();" type="button" id="save-settings" class="btn btn-primary btn-lg"><i class="fa-sharp fa-regular fa-floppy-disk"></i> <?php esc_html_e( 'Save Settings', 'lead-generation-form' ); ?></button>
						<div id="wlgf-save-process" class="spinner-grow m-3 text-dark d-none" role="status">
							<span class="visually-hidden"></span>
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</div>
</div>