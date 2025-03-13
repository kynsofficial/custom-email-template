=== Custom Email Template ===
Contributors: ssutechnology
Tags: email, template, smtp, html email, wordpress email, email template, email customization, responsive email
Requires at least: 5.6
Tested up to: 6.4
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Customize how all WordPress emails look and are delivered with an easy-to-use interface.

== Description ==

Custom Email Template provides a professional, customizable template for all emails sent from your WordPress site. With a user-friendly interface, you can easily change colors, logo, and other styling elements without having to edit code.

The plugin also includes SMTP configuration to improve email deliverability and ensure your emails make it to recipients' inboxes instead of spam folders.

= Features =

* Apply a professional HTML template to all WordPress emails
* Customize colors, logo, and layout using a simple interface
* Configure sender information (From Name, From Email, Reply-To)
* Set up SMTP for improved deliverability
* Preview your email template in real-time
* See how your emails will look on desktop and mobile devices
* Send test emails to verify your configuration
* Option to exclude specific plugin emails (WooCommerce, Tutor LMS)

= How It Works =

The plugin hooks into WordPress's email system to apply your custom template to all outgoing emails. It also provides options to modify the sender information and configure SMTP for better deliverability.

No more unstyled, plain-text emails from your WordPress site! With Custom Email Template, all your emails will have a consistent, professional look that matches your brand.

= Use Cases =

* **Professional Branding**: Ensure all emails from your site have consistent branding
* **Improved Deliverability**: Configure SMTP to increase the chances of emails reaching the inbox
* **Better User Experience**: Create visually appealing, responsive emails that look great on all devices
* **Time-Saving**: No need to customize individual email templates for different plugins
* **Consistency**: Maintain a consistent look and feel across all your site's communications

= Compatibility =

Custom Email Template works with any WordPress email sent via the `wp_mail()` function. For plugins that use their own email systems, we offer configuration options to exclude them from the template.

== Installation ==

1. Upload the `custom-email-template` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Email Template to configure your email template

== Frequently Asked Questions ==

= Will this affect all emails sent from my WordPress site? =

Yes, this plugin applies your custom template to all emails sent via the WordPress `wp_mail()` function, which is used by WordPress core and most plugins. You can exclude certain plugins like WooCommerce or Tutor LMS.

= Can I use my own custom template? =

The current version provides a standard template with customization options. Future versions may include more templates or the ability to create custom templates.

= Does this plugin support SMTP authentication? =

Yes, you can configure SMTP settings including authentication, encryption (TLS/SSL), and custom ports.

= Will my emails look good on mobile devices? =

Yes, the email template is fully responsive and will look great on all devices. You can preview both desktop and mobile views in the settings.

= Can I preview my email template before sending? =

Yes, the plugin includes a real-time preview feature that shows how your emails will look with your current settings. You can also send test emails to verify everything is working correctly.

= Will this plugin work with WooCommerce emails? =

Yes, you can choose to apply your custom template to WooCommerce emails or keep WooCommerce's own templates.

= Does this plugin handle email delivery issues? =

By configuring the SMTP settings, you can improve email deliverability. However, for persistent delivery issues, please check with your hosting provider or consider a dedicated email service.

== Screenshots ==

1. General Settings - Configure logo, logo alignment, and footer text
2. Sender Information - Set up sender name, email, and reply-to address
3. Styling Settings - Customize colors for backgrounds, text, and elements
4. SMTP Configuration - Configure SMTP settings for improved deliverability
5. Email Preview - Preview how your emails will look with current settings

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release