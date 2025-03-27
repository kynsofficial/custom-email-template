=== Custom Email Template ===
Contributors: swiftspeed
Tags: email template, smtp, email customization, email branding,  woocommerce email
Requires at least: 5.6
Tested up to: 6.7
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Customize how all emails from your WordPress site look and are delivered with a professional template and SMTP support.

== Description ==

Custom Email Template provides a professional, customizable template for all emails sent from your WordPress site. With a user-friendly interface, you can easily change colors, logo, and other styling elements without having to edit code.

The plugin also includes SMTP configuration to improve email deliverability and ensure your emails make it to recipients' inboxes instead of spam folders.

= Key Features =

* Apply a professional HTML template to all WordPress emails
* Customize colors, logo, and layout with live preview
* Configure sender information (From Name, From Email, Reply-To)
* Set up SMTP for improved deliverability
* Preview your email template in real-time for both desktop and mobile
* Send test emails to verify your configuration
* Option to exclude specific plugin emails (WooCommerce, Tutor LMS)

= How It Works =

The plugin hooks into WordPress's email system to apply your custom template to all outgoing emails. It also provides options to modify the sender information and configure SMTP for better deliverability.

= Plugin Compatibility =

This plugin works with most WordPress plugins that use the WordPress `wp_mail()` function, including:

* Contact Form 7
* WooCommerce (with option to exclude)
* Tutor LMS (with option to exclude)
* And many more!

== Installation ==

1. Upload the `custom-email-template` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Email Template to configure your email template
4. Upload your logo, customize colors, and set up your sender information
5. Send a test email to verify your configuration works correctly

== Frequently Asked Questions ==

= Will this affect all emails sent from my WordPress site? =

Yes, this plugin applies your custom template to all emails sent via the WordPress `wp_mail()` function, which is used by WordPress core and most plugins. You can exclude certain plugins like WooCommerce or Tutor LMS if needed.

= Can I use my own custom template? =

The current version provides a standard template with customization options. Future versions may include more templates or the ability to create custom templates.

= Does this plugin support SMTP authentication? =

Yes, you can configure SMTP settings including authentication, encryption (TLS/SSL), and custom ports to improve email deliverability.

= Will my emails look good on mobile devices? =

Yes, the email template is fully responsive and will look great on all devices. You can preview both desktop and mobile views in the settings panel.

= Can I test the email template before using it? =

Yes, the plugin includes a "Send Test" function that allows you to send a test email to any address to verify your configuration.

= Will this plugin work with my contact form plugin? =

If your contact form plugin uses the WordPress `wp_mail()` function (most do), then yes, it will work with this plugin.

= Is this plugin compatible with WooCommerce emails? =

Yes, but the plugin gives you the option to exclude WooCommerce emails if you prefer to use WooCommerce's built-in email templates.

== Screenshots ==

1. General Settings - Configure logo, logo alignment, and footer text
2. Sender Information - Set up sender name, email, and reply-to address
3. Styling Settings - Customize colors for backgrounds, text, and elements
4. SMTP Configuration - Configure SMTP settings for improved deliverability
5. Email Preview - Preview how your emails will look with current settings
6. Email Layout - Example of the professional email layout your recipients will see

== Changelog ==

= 1.0.0 =
* Initial release with customizable email template
* Sender information configuration
* Color customization options
* SMTP configuration
* Real-time preview for desktop and mobile
* Test email functionality
* Plugin exclusion options

== Upgrade Notice ==

= 1.0.0 =
Initial release of Custom Email Template for WordPress. Customize all your WordPress emails with a professional, responsive template.

== Credits ==

Developed by [Àgbà Akin](https://akinolaakeem.com)
Managed by [Ssu-Technology Limited](https://swiftspeed.org)