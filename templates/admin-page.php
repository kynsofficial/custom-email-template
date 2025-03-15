<?php
/**
 * Admin settings page template.
 *
 * @package CustomEmailTemplate
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Verify nonce for tab navigation.
$nonce_verified = false;
if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'custom_email_template_tab_nonce' ) ) {
    $nonce_verified = true;
}

// Get active tab + view from query string (with nonce verification for security).
$default_tab = 'general';
$default_view = 'settings';

// Only process query parameters if nonce is verified or if they are not present (first load)
if ( $nonce_verified || ( ! isset( $_GET['tab_id'] ) && ! isset( $_GET['view'] ) ) ) {
    $active_tab = isset( $_GET['tab_id'] ) ? sanitize_text_field( wp_unslash( $_GET['tab_id'] ) ) : $default_tab;
    $view_mode  = isset( $_GET['view'] ) ? sanitize_text_field( wp_unslash( $_GET['view'] ) ) : $default_view;
} else {
    // Default values if nonce verification fails
    $active_tab = $default_tab;
    $view_mode  = $default_view;
}

// Sample email content for preview.
$sample_content = '
    <h1 style="color: ' . esc_attr( get_option( 'custom_email_primary_text_color', '#212327' ) ) . ';">Welcome to Our Platform!</h1>
    <p>Hi [Dear User],</p>
    <p>Thank you for registering with us. We\'re excited to have you join our community!</p>
    <p>To get started, here are a few things you can do:</p>
    <ul>
        <li>Complete your profile</li>
        <li>Explore our resources</li>
        <li>Connect with other members</li>
    </ul>
    <p style="text-align: center; margin: 25px 0;">
        <a href="#" style="background-color: ' . esc_attr( get_option( 'custom_email_button_bg_color', '#696cff' ) ) . '; color: ' . esc_attr( get_option( 'custom_email_button_text_color', '#ffffff' ) ) . '; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: 500;">Get Started</a>
    </p>
    <p>If you have any questions, please don\'t hesitate to reach out to our support team.</p>
    <p>Best regards,<br>The Team</p>
';

// Create an instance of the Email class to get the template.
$email_template = new Custom_Email_Template_Email();

// Get site/user info for preview.
$site_name  = get_bloginfo( 'name' );
$from_name  = get_option( 'custom_email_from_name', $site_name );
$from_email = get_option( 'custom_email_from_email', get_option( 'admin_email' ) );
?>

<!-- Add CSS to hide sidebar links in preview mode -->
<style>
	.preview-mode .email-template-sidebar {
		visibility: hidden;
	}
	.preview-mode .email-template-container {
		min-height: 700px;
	}
</style>

<div class="email-template-settings-app <?php echo ( 'preview' === $view_mode ) ? 'preview-mode' : ''; ?>">
	<div class="email-template-header">
		<h1><?php echo esc_html__( 'Email Template Settings', 'custom-email-template' ); ?></h1>
		<p><?php echo esc_html__( 'Customize how all emails from your WordPress site look and are delivered.', 'custom-email-template' ); ?></p>
	</div>

	<div class="email-template-container">
		<!-- Left sidebar with navigation tabs -->
		<div class="email-template-sidebar">
			<div class="email-template-tabs">
				<ul>
					<li>
						<a href="#general" data-tab="general">
							<span class="dashicons dashicons-admin-generic"></span>
							<?php echo esc_html__( 'General', 'custom-email-template' ); ?>
						</a>
					</li>
					<li>
						<a href="#sender" data-tab="sender">
							<span class="dashicons dashicons-admin-users"></span>
							<?php echo esc_html__( 'Sender', 'custom-email-template' ); ?>
						</a>
					</li>
					<li>
						<a href="#styling" data-tab="styling">
							<span class="dashicons dashicons-admin-appearance"></span>
							<?php echo esc_html__( 'Styling', 'custom-email-template' ); ?>
						</a>
					</li>
					<li>
						<a href="#smtp" data-tab="smtp">
							<span class="dashicons dashicons-email-alt"></span>
							<?php echo esc_html__( 'SMTP', 'custom-email-template' ); ?>
						</a>
					</li>
				</ul>
			</div>
		</div>

		<!-- Main content area -->
		<div class="email-template-content">
			<!-- View toggle buttons -->
			<div class="view-toggle">
				<?php 
				// Build links to toggle between "Settings" and "Preview" views.
				$base_url     = admin_url( 'options-general.php?page=custom-email-template' );
				
				// Add nonce to view URLs for security
				$tab_nonce = wp_create_nonce( 'custom_email_template_tab_nonce' );
				
				$settings_url = add_query_arg( 
                    array( 
                        'tab_id' => $active_tab,
                        '_wpnonce' => $tab_nonce 
                    ), 
                    $base_url 
                );
                
				$preview_url  = add_query_arg( 
                    array( 
                        'tab_id' => $active_tab, 
                        'view' => 'preview',
                        '_wpnonce' => $tab_nonce 
                    ), 
                    $base_url 
                );
				?>

				<a href="<?php echo esc_url( $settings_url ); ?>"
				   class="view-button <?php echo ( 'preview' !== $view_mode ) ? 'active' : ''; ?>">
					<span class="dashicons dashicons-admin-settings"></span>
					<?php echo esc_html__( 'Settings', 'custom-email-template' ); ?>
				</a>
				<a href="<?php echo esc_url( $preview_url ); ?>"
				   class="view-button <?php echo ( 'preview' === $view_mode ) ? 'active' : ''; ?>">
					<span class="dashicons dashicons-visibility"></span>
					<?php echo esc_html__( 'Preview', 'custom-email-template' ); ?>
				</a>
			</div>

			<?php if ( 'preview' === $view_mode ) : ?>
				<!-- Preview mode -->
				<div class="email-preview-mode">
					<div class="preview-header">
						<h2><?php echo esc_html__( 'Email Preview', 'custom-email-template' ); ?></h2>
						<div class="preview-device-buttons">
							<button type="button" class="preview-device-button active" data-device="desktop">
								<span class="dashicons dashicons-desktop"></span>
							</button>
							<button type="button" class="preview-device-button" data-device="mobile">
								<span class="dashicons dashicons-smartphone"></span>
							</button>
						</div>
					</div>

					<div class="preview-container desktop">
						<div class="preview-device-frame">
							<div class="preview-device-header">
								<div class="preview-device-from">
									<span class="preview-from-name"><?php echo esc_html( $from_name ?: $site_name ); ?></span>
									<span class="preview-from-email">&lt;<?php echo esc_html( $from_email ?: get_option( 'admin_email' ) ); ?>&gt;</span>
								</div>
								<div class="preview-device-subject">Email Subject</div>
							</div>

							<div class="preview-device-content" id="email-preview">
								<?php 
								$button_bg_color   = get_option( 'custom_email_button_bg_color', '#696cff' );
								$button_text_color = get_option( 'custom_email_button_text_color', '#ffffff' );
								$extra_style       = "<style>
                                    a[href='#'], .email-content a.button {
                                        background-color: {$button_bg_color} !important;
                                        color: {$button_text_color} !important;
                                        padding: 10px 20px !important;
                                        border-radius: 4px !important;
                                        text-decoration: none !important;
                                        font-weight: 500 !important;
                                        display: inline-block !important;
                                    }
                                </style>";

								// Escape and output the style block.
								echo wp_kses( $extra_style, array( 'style' => array() ) );

								// Output the email template safely.
								echo wp_kses_post( $email_template->get_template( $sample_content, 'Email Preview' ) );
								?>
							</div>
						</div>
					</div>

					<div class="preview-container mobile" style="display: none;">
						<div class="preview-device-frame mobile-frame">
							<div class="preview-device-header">
								<div class="preview-device-from">
									<span class="preview-from-name"><?php echo esc_html( $from_name ); ?></span>
									<span class="preview-from-email">&lt;<?php echo esc_html( $from_email ); ?>&gt;</span>
								</div>
								<div class="preview-device-subject">Email Subject</div>
							</div>

							<div class="preview-device-content mobile-content" id="email-preview-mobile">
								<?php
								echo wp_kses_post( $email_template->get_template( $sample_content, 'Email Preview' ) );
								?>
							</div>
						</div>
					</div>
				</div>
			<?php else : ?>
				<!-- Settings mode -->
				<form method="post" action="options.php" id="email-template-form">
					<?php
					settings_fields( 'custom-email-template-group' );
					do_settings_sections( 'custom-email-template-group' );
					?>
					<!-- Hidden field to store active tab - this is what powers the redirect after save -->
					<input type="hidden" name="tab_id" id="active-tab-field" value="<?php echo esc_attr( $active_tab ); ?>" />

					<!-- General Settings Tab Content -->
					<div id="general" class="settings-tab-content">
						<h2><?php echo esc_html__( 'General Settings', 'custom-email-template' ); ?></h2>

						<div class="email-template-field">
							<label for="custom_email_logo_url"><?php echo esc_html__( 'Logo', 'custom-email-template' ); ?></label>
							<div class="email-template-field-group">
								<input type="text" name="custom_email_logo_url" id="custom_email_logo_url"
								       value="<?php echo esc_attr( get_option( 'custom_email_logo_url', 'https://img.freepik.com/premium-vector/simple-letter-n-company-logo_197415-6.jpg?w=1380' ) ); ?>"
								       class="regular-text preview-update" />
								<button type="button" class="button" id="upload-logo-button">
									<?php echo esc_html__( 'Choose Logo', 'custom-email-template' ); ?>
								</button>
							</div>
							<div class="email-template-field-note">
								<?php echo esc_html__( 'Upload or enter the URL of your logo. Recommended height: 44px.', 'custom-email-template' ); ?>
							</div>
						</div>

						<div class="email-template-field">
							<label><?php echo esc_html__( 'Logo Alignment', 'custom-email-template' ); ?></label>
							<div class="logo-alignment-options">
								<label class="alignment-option <?php echo ( get_option( 'custom_email_logo_alignment', 'center' ) === 'left' ) ? 'active' : ''; ?>">
									<input type="radio" name="custom_email_logo_alignment" value="left" <?php checked( 'left', get_option( 'custom_email_logo_alignment', 'center' ) ); ?> class="preview-update" />
									<span class="dashicons dashicons-align-left"></span>
								</label>
								<label class="alignment-option <?php echo ( get_option( 'custom_email_logo_alignment', 'center' ) === 'center' ) ? 'active' : ''; ?>">
									<input type="radio" name="custom_email_logo_alignment" value="center" <?php checked( 'center', get_option( 'custom_email_logo_alignment', 'center' ) ); ?> class="preview-update" />
									<span class="dashicons dashicons-align-center"></span>
								</label>
								<label class="alignment-option <?php echo ( get_option( 'custom_email_logo_alignment', 'center' ) === 'right' ) ? 'active' : ''; ?>">
									<input type="radio" name="custom_email_logo_alignment" value="right" <?php checked( 'right', get_option( 'custom_email_logo_alignment', 'center' ) ); ?> class="preview-update" />
									<span class="dashicons dashicons-align-right"></span>
								</label>
							</div>
						</div>

						<div class="email-template-field">
							<label for="custom_email_footer_text"><?php echo esc_html__( 'Footer Text', 'custom-email-template' ); ?></label>
							<textarea name="custom_email_footer_text" id="custom_email_footer_text" rows="2" class="preview-update"><?php 
								echo esc_textarea( get_option( 'custom_email_footer_text', 'This is an automatically generated email. Please do not reply.' ) ); 
							?></textarea>
							<div class="email-template-field-note">
								<?php echo esc_html__( 'Text that appears at the bottom of every email.', 'custom-email-template' ); ?>
							</div>
						</div>
                        
                        <!-- Uninstall Data Control Setting -->
                        <div class="email-template-field switch-field">
                            <label for="custom_email_preserve_data"><?php echo esc_html__( 'Preserve Plugin Data', 'custom-email-template' ); ?></label>
                            <div class="toggle-container">
                                <label class="switch">
                                    <input type="checkbox" name="custom_email_preserve_data" id="custom_email_preserve_data"
                                           value="yes" <?php checked( 'yes', get_option( 'custom_email_preserve_data', 'no' ) ); ?> />
                                    <span class="slider round"></span>
                                </label>
                                <div class="email-template-field-note">
                                    <?php echo esc_html__( 'When enabled, your settings will be preserved when the plugin is uninstalled. This allows you to keep your configurations if you reinstall the plugin later.', 'custom-email-template' ); ?>
                                </div>
                            </div>
                        </div>
					</div>

					<!-- Sender Settings Tab Content -->
					<div id="sender" class="settings-tab-content">
						<h2><?php echo esc_html__( 'Sender Information', 'custom-email-template' ); ?></h2>
						<p class="panel-description">
							<?php echo esc_html__( 'Control who emails appear to be from. By default, WordPress uses "WordPress" as the sender name.', 'custom-email-template' ); ?>
						</p>

						<div class="email-template-field">
							<label for="custom_email_from_name"><?php echo esc_html__( 'From Name', 'custom-email-template' ); ?></label>
							<input type="text" name="custom_email_from_name" id="custom_email_from_name"
							       value="<?php echo esc_attr( get_option( 'custom_email_from_name', '' ) ); ?>"
							       placeholder="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
							       class="preview-update" />
							<div class="email-template-field-note">
								<?php echo esc_html__( 'The name that recipients will see in their inbox.', 'custom-email-template' ); ?>
							</div>
						</div>

						<div class="email-template-field">
							<label for="custom_email_from_email"><?php echo esc_html__( 'From Email', 'custom-email-template' ); ?></label>
							<input type="email" name="custom_email_from_email" id="custom_email_from_email"
							       value="<?php echo esc_attr( get_option( 'custom_email_from_email', '' ) ); ?>"
							       placeholder="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>"
							       class="preview-update" />
							<div class="email-template-field-note">
								<?php echo esc_html__( 'The email address that all WordPress emails will be sent from.', 'custom-email-template' ); ?>
							</div>
						</div>

						<div class="email-template-field">
							<label for="custom_email_reply_to"><?php echo esc_html__( 'Reply-To Email', 'custom-email-template' ); ?></label>
							<input type="email" name="custom_email_reply_to" id="custom_email_reply_to"
							       value="<?php echo esc_attr( get_option( 'custom_email_reply_to', '' ) ); ?>"
							       class="preview-update" />
							<div class="email-template-field-note">
								<?php echo esc_html__( 'The email address that users will reply to. Leave blank to use From Email.', 'custom-email-template' ); ?>
							</div>
						</div>

						<div class="email-template-field">
							<label for="custom_email_avatar_url"><?php echo esc_html__( 'Sender Avatar', 'custom-email-template' ); ?></label>
							<div class="email-template-field-group">
								<input type="text" name="custom_email_avatar_url" id="custom_email_avatar_url"
								       value="<?php echo esc_attr( get_option( 'custom_email_avatar_url', '' ) ); ?>"
								       class="regular-text preview-update" />
								<button type="button" class="button" id="upload-avatar-button">
									<?php echo esc_html__( 'Choose Image', 'custom-email-template' ); ?>
								</button>
							</div>
							<div class="email-template-field-note">
								<?php echo esc_html__( 'Upload a square image to use as your sender avatar. For Gmail, register this email with Gravatar for avatar support.', 'custom-email-template' ); ?>
								<a href="https://gravatar.com/" target="_blank"><?php echo esc_html__( 'Set up Gravatar', 'custom-email-template' ); ?> →</a>
							</div>
						</div>

						<div class="email-template-field">
							<label><?php echo esc_html__( 'Test Email', 'custom-email-template' ); ?></label>
							<div class="email-template-field-group">
								<input type="email" id="test-email-address" placeholder="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" />
								<button type="button" id="test-email-button" class="button button-primary">
									<?php echo esc_html__( 'Send Test', 'custom-email-template' ); ?>
								</button>
							</div>
							<div id="test-email-result" class="email-template-notice" style="display: none;"></div>
						</div>
					</div>

					<!-- Styling Settings Tab Content -->
					<div id="styling" class="settings-tab-content">
						<h2><?php echo esc_html__( 'Email Styling', 'custom-email-template' ); ?></h2>
						
						<div class="email-template-colors">
							<div class="email-template-color-group">
								<h3><?php echo esc_html__( 'Background Colors', 'custom-email-template' ); ?></h3>
								
								<div class="email-template-field color-field">
									<label for="custom_email_bg_color"><?php echo esc_html__( 'Page Background', 'custom-email-template' ); ?></label>
									<div class="color-picker-container">
										<input type="text" name="custom_email_bg_color" id="custom_email_bg_color"
										       value="<?php echo esc_attr( get_option( 'custom_email_bg_color', '#f7f7f7' ) ); ?>"
										       class="color-input preview-update" data-default-color="#f7f7f7" />
										<div class="color-preview" style="background-color: <?php echo esc_attr( get_option( 'custom_email_bg_color', '#f7f7f7' ) ); ?>;">
										</div>
									</div>
								</div>
								
								<div class="email-template-field color-field">
									<label for="custom_email_container_bg_color"><?php echo esc_html__( 'Content Background', 'custom-email-template' ); ?></label>
									<div class="color-picker-container">
										<input type="text" name="custom_email_container_bg_color" id="custom_email_container_bg_color"
										       value="<?php echo esc_attr( get_option( 'custom_email_container_bg_color', '#ffffff' ) ); ?>"
										       class="color-input preview-update" data-default-color="#ffffff" />
										<div class="color-preview" style="background-color: <?php echo esc_attr( get_option( 'custom_email_container_bg_color', '#ffffff' ) ); ?>;">
										</div>
									</div>
								</div>
							</div>
							
							<div class="email-template-color-group">
								<h3><?php echo esc_html__( 'Text Colors', 'custom-email-template' ); ?></h3>
								
								<div class="email-template-field color-field">
									<label for="custom_email_primary_text_color"><?php echo esc_html__( 'Heading Text', 'custom-email-template' ); ?></label>
									<div class="color-picker-container">
										<input type="text" name="custom_email_primary_text_color" id="custom_email_primary_text_color"
										       value="<?php echo esc_attr( get_option( 'custom_email_primary_text_color', '#212327' ) ); ?>"
										       class="color-input preview-update" data-default-color="#212327" />
										<div class="color-preview" style="background-color: <?php echo esc_attr( get_option( 'custom_email_primary_text_color', '#212327' ) ); ?>;">
										</div>
									</div>
								</div>
								
								<div class="email-template-field color-field">
									<label for="custom_email_secondary_text_color"><?php echo esc_html__( 'Body Text', 'custom-email-template' ); ?></label>
									<div class="color-picker-container">
										<input type="text" name="custom_email_secondary_text_color" id="custom_email_secondary_text_color"
										       value="<?php echo esc_attr( get_option( 'custom_email_secondary_text_color', '#5b616f' ) ); ?>"
										       class="color-input preview-update" data-default-color="#5b616f" />
										<div class="color-preview" style="background-color: <?php echo esc_attr( get_option( 'custom_email_secondary_text_color', '#5b616f' ) ); ?>;">
										</div>
									</div>
								</div>
								
								<div class="email-template-field color-field">
									<label for="custom_email_footer_text_color"><?php echo esc_html__( 'Footer Text', 'custom-email-template' ); ?></label>
									<div class="color-picker-container">
										<input type="text" name="custom_email_footer_text_color" id="custom_email_footer_text_color"
										       value="<?php echo esc_attr( get_option( 'custom_email_footer_text_color', '#9da3af' ) ); ?>"
										       class="color-input preview-update" data-default-color="#9da3af" />
										<div class="color-preview" style="background-color: <?php echo esc_attr( get_option( 'custom_email_footer_text_color', '#9da3af' ) ); ?>;">
										</div>
									</div>
								</div>
							</div>
							
							<div class="email-template-color-group">
								<h3><?php echo esc_html__( 'Elements', 'custom-email-template' ); ?></h3>
								
								<div class="email-template-field color-field">
									<label for="custom_email_header_border_color"><?php echo esc_html__( 'Header Border', 'custom-email-template' ); ?></label>
									<div class="color-picker-container">
										<input type="text" name="custom_email_header_border_color" id="custom_email_header_border_color"
										       value="<?php echo esc_attr( get_option( 'custom_email_header_border_color', '#e0e2ea' ) ); ?>"
										       class="color-input preview-update" data-default-color="#e0e2ea" />
										<div class="color-preview" style="background-color: <?php echo esc_attr( get_option( 'custom_email_header_border_color', '#e0e2ea' ) ); ?>;">
										</div>
									</div>
								</div>
								
								<div class="email-template-field color-field">
									<label for="custom_email_footer_border_color"><?php echo esc_html__( 'Footer Border', 'custom-email-template' ); ?></label>
									<div class="color-picker-container">
										<input type="text" name="custom_email_footer_border_color" id="custom_email_footer_border_color"
										       value="<?php echo esc_attr( get_option( 'custom_email_footer_border_color', '#e0e2ea' ) ); ?>"
										       class="color-input preview-update" data-default-color="#e0e2ea" />
										<div class="color-preview" style="background-color: <?php echo esc_attr( get_option( 'custom_email_footer_border_color', '#e0e2ea' ) ); ?>;">
										</div>
									</div>
								</div>
							</div>
							
							<div class="email-template-color-group">
								<h3><?php echo esc_html__( 'Button Colors', 'custom-email-template' ); ?></h3>
								
								<div class="email-template-field color-field">
									<label for="custom_email_button_bg_color"><?php echo esc_html__( 'Button Background', 'custom-email-template' ); ?></label>
									<div class="color-picker-container">
										<input type="text" name="custom_email_button_bg_color" id="custom_email_button_bg_color"
										       value="<?php echo esc_attr( get_option( 'custom_email_button_bg_color', '#696cff' ) ); ?>"
										       class="color-input preview-update" data-default-color="#696cff" />
										<div class="color-preview" style="background-color: <?php echo esc_attr( get_option( 'custom_email_button_bg_color', '#696cff' ) ); ?>;">
										</div>
									</div>
								</div>
								
								<div class="email-template-field color-field">
									<label for="custom_email_button_text_color"><?php echo esc_html__( 'Button Text', 'custom-email-template' ); ?></label>
									<div class="color-picker-container">
										<input type="text" name="custom_email_button_text_color" id="custom_email_button_text_color"
										       value="<?php echo esc_attr( get_option( 'custom_email_button_text_color', '#ffffff' ) ); ?>"
										       class="color-input preview-update" data-default-color="#ffffff" />
										<div class="color-preview" style="background-color: <?php echo esc_attr( get_option( 'custom_email_button_text_color', '#ffffff' ) ); ?>;">
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="email-template-color-group">
							<h3><?php echo esc_html__( 'Template Exclusions', 'custom-email-template' ); ?></h3>
							<p class="panel-description">
								<?php echo esc_html__( 'Control which plugins use their own email templates instead of this custom template.', 'custom-email-template' ); ?>
							</p>
							
							<div class="email-template-field switch-field">
								<label for="custom_email_exclude_tutor_lms"><?php echo esc_html__( 'Exclude Tutor LMS Emails', 'custom-email-template' ); ?></label>
								<div class="toggle-container">
									<label class="switch">
										<input type="checkbox" name="custom_email_exclude_tutor_lms" id="custom_email_exclude_tutor_lms"
										       value="yes" <?php checked( 'yes', get_option( 'custom_email_exclude_tutor_lms', 'yes' ) ); ?> />
										<span class="slider round"></span>
									</label>
									<div class="email-template-field-note">
										<?php echo esc_html__( 'When enabled, emails from Tutor LMS will use their own template instead of this custom template.', 'custom-email-template' ); ?>
									</div>
								</div>
							</div>
							
							<div class="email-template-field switch-field">
								<label for="custom_email_exclude_woocommerce"><?php echo esc_html__( 'Exclude WooCommerce', 'custom-email-template' ); ?></label>
								<div class="toggle-container">
									<label class="switch">
										<input type="checkbox" name="custom_email_exclude_woocommerce" id="custom_email_exclude_woocommerce"
										       value="yes" <?php checked( 'yes', get_option( 'custom_email_exclude_woocommerce', 'no' ) ); ?> />
										<span class="slider round"></span>
									</label>
									<div class="email-template-field-note">
										<?php echo esc_html__( 'When enabled, emails from WooCommerce will use their own template. Disable to apply your custom template to WooCommerce emails.', 'custom-email-template' ); ?>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- SMTP Settings Tab Content -->
					<div id="smtp" class="settings-tab-content">
						<h2><?php echo esc_html__( 'SMTP Configuration', 'custom-email-template' ); ?></h2>
						<p class="panel-description">
							<?php echo esc_html__( 'Configure SMTP to send emails reliably through an external mail server.', 'custom-email-template' ); ?>
						</p>
						
						<div class="smtp-option-card">
							<div class="email-template-field switch-field">
								<label for="enable-smtp"><?php echo esc_html__( 'Enable SMTP', 'custom-email-template' ); ?></label>
								<div class="toggle-container">
									<label class="switch">
										<input type="checkbox" name="custom_email_use_smtp" id="enable-smtp"
										       value="yes" <?php checked( 'yes', get_option( 'custom_email_use_smtp', 'no' ) ); ?> />
										<span class="slider round"></span>
									</label>
									<div class="email-template-field-note">
										<?php echo esc_html__( 'When enabled, all WordPress emails will be sent using the SMTP server.', 'custom-email-template' ); ?>
									</div>
								</div>
							</div>
						</div>
						
						<div class="smtp-settings-container">
							<div class="email-template-field">
								<label for="custom_email_smtp_host"><?php echo esc_html__( 'SMTP Host', 'custom-email-template' ); ?></label>
								<input type="text" name="custom_email_smtp_host" id="custom_email_smtp_host"
								       value="<?php echo esc_attr( get_option( 'custom_email_smtp_host', '' ) ); ?>"
								       placeholder="smtp.example.com" />
							</div>
							
							<div class="email-template-field">
								<label for="custom_email_smtp_port"><?php echo esc_html__( 'SMTP Port', 'custom-email-template' ); ?></label>
								<input type="number" name="custom_email_smtp_port" id="custom_email_smtp_port"
								       value="<?php echo esc_attr( get_option( 'custom_email_smtp_port', '587' ) ); ?>"
								       min="1" max="65535" step="1" />
								<div class="email-template-field-note">
									<?php echo esc_html__( 'Common ports: 25, 465, 587', 'custom-email-template' ); ?>
								</div>
							</div>
							
							<div class="email-template-field">
								<label for="custom_email_smtp_encryption"><?php echo esc_html__( 'Encryption', 'custom-email-template' ); ?></label>
								<select name="custom_email_smtp_encryption" id="custom_email_smtp_encryption">
									<option value="none" <?php selected( 'none', get_option( 'custom_email_smtp_encryption', 'tls' ) ); ?>>
										<?php echo esc_html__( 'None', 'custom-email-template' ); ?>
									</option>
									<option value="tls" <?php selected( 'tls', get_option( 'custom_email_smtp_encryption', 'tls' ) ); ?>>
										<?php echo esc_html__( 'TLS', 'custom-email-template' ); ?>
									</option>
									<option value="ssl" <?php selected( 'ssl', get_option( 'custom_email_smtp_encryption', 'tls' ) ); ?>>
										<?php echo esc_html__( 'SSL', 'custom-email-template' ); ?>
									</option>
								</select>
							</div>
							
							<div class="email-template-field switch-field">
								<label for="custom_email_smtp_auth"><?php echo esc_html__( 'Authentication', 'custom-email-template' ); ?></label>
								<div class="toggle-container">
									<label class="switch">
										<input type="checkbox" name="custom_email_smtp_auth" id="custom_email_smtp_auth"
										       value="yes" <?php checked( 'yes', get_option( 'custom_email_smtp_auth', 'yes' ) ); ?> />
										<span class="slider round"></span>
									</label>
									<div class="email-template-field-note">
										<?php echo esc_html__( 'Most SMTP servers require authentication.', 'custom-email-template' ); ?>
									</div>
								</div>
							</div>
							
							<div class="smtp-auth-fields">
								<div class="email-template-field">
									<label for="custom_email_smtp_username"><?php echo esc_html__( 'Username', 'custom-email-template' ); ?></label>
									<input type="text" name="custom_email_smtp_username" id="custom_email_smtp_username"
									       value="<?php echo esc_attr( get_option( 'custom_email_smtp_username', '' ) ); ?>" />
								</div>
								
								<div class="email-template-field">
									<label for="custom_email_smtp_password"><?php echo esc_html__( 'Password', 'custom-email-template' ); ?></label>
									<input type="password" name="custom_email_smtp_password" id="custom_email_smtp_password"
									       value="<?php echo esc_attr( get_option( 'custom_email_smtp_password', '' ) ); ?>" />
								</div>
							</div>
							
							<div class="email-template-field switch-field">
								<label for="custom_email_smtp_debug"><?php echo esc_html__( 'Debug Mode', 'custom-email-template' ); ?></label>
								<div class="toggle-container">
									<label class="switch">
										<input type="checkbox" name="custom_email_smtp_debug" id="custom_email_smtp_debug"
										       value="yes" <?php checked( 'yes', get_option( 'custom_email_smtp_debug', 'no' ) ); ?> />
										<span class="slider round"></span>
									</label>
									<div class="email-template-field-note">
										<?php echo esc_html__( 'Logs SMTP connection details to the error log. Use only for troubleshooting.', 'custom-email-template' ); ?>
									</div>
								</div>
							</div>
							
							<div class="email-template-field">
								<button type="button" id="test-smtp-button" class="button button-primary">
									<?php echo esc_html__( 'Test SMTP Connection', 'custom-email-template' ); ?>
								</button>
								<div id="test-smtp-result" class="email-template-notice" style="display: none;"></div>
							</div>
						</div>
					</div>
					
					<div class="email-template-actions">
						<button type="submit" class="button button-primary button-hero">
							<?php echo esc_html__( 'Save Changes', 'custom-email-template' ); ?>
						</button>
					</div>
				</form>
			<?php endif; ?>
		</div>
	</div>
</div>

<!-- Script for tab navigation with improved redirect handling -->
<script type="text/javascript">
jQuery(document).ready(function($) {
    // Set active tab based on URL parameters or hash
    function setActiveTab() {
        var activeTab;
        
        // Check URL parameters first
        var urlParams = new URLSearchParams(window.location.search);
        activeTab = urlParams.get('tab_id');
        
        // If no tab in URL params, check hash
        if (!activeTab && window.location.hash) {
            activeTab = window.location.hash.substring(1);
        }
        
        // Default to general if nothing found
        if (!activeTab) {
            activeTab = 'general';
        }
        
        // Update hidden field with current tab ID
        $('#active-tab-field').val(activeTab);
        
        // Update UI to reflect active tab
        $('.email-template-tabs a').removeClass('active');
        $('.settings-tab-content').removeClass('active');
        $('.email-template-tabs a[data-tab="' + activeTab + '"]').addClass('active');
        $('#' + activeTab).addClass('active');
    }
    
    // Run on page load
    setActiveTab();
    
    // Handle tab clicks
    $('.email-template-tabs a').on('click', function(e) {
        e.preventDefault();
        
        // Don't do anything if in preview mode
        if ($('.email-template-settings-app').hasClass('preview-mode')) {
            return;
        }
        
        var tabId = $(this).data('tab');
        
        // Set active tab in hidden form field - this ensures form submission has the tab ID
        $('#active-tab-field').val(tabId);
        
        // Create nonce for security
        var nonce = '<?php echo esc_js(wp_create_nonce('custom_email_template_tab_nonce')); ?>';
        
        // Update URL for browser history (without reloading)
        var newUrl = new URL(window.location.href);
        newUrl.searchParams.set('tab_id', tabId);
        newUrl.searchParams.set('_wpnonce', nonce);
        history.pushState({}, '', newUrl.toString());
        
        // Update UI
        $('.email-template-tabs a').removeClass('active');
        $(this).addClass('active');
        $('.settings-tab-content').removeClass('active');
        $('#' + tabId).addClass('active');
    });
});
</script>