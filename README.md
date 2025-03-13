# Custom Email Template for WordPress

![Custom Email Template Banner](https://img.shields.io/badge/WordPress-Plugin-blue.svg)

Customize how all emails from your WordPress site look and are delivered with an easy-to-use interface.

## Description

Custom Email Template provides a professional, customizable template for all emails sent from your WordPress site. With a user-friendly interface, you can easily change colors, logo, and other styling elements without having to edit code.

The plugin also includes SMTP configuration to improve email deliverability and ensure your emails make it to recipients' inboxes instead of spam folders.

![Email Preview](/assets/images/screenshot-7.png)

### Features

* Apply a professional HTML template to all WordPress emails
* Customize colors, logo, and layout
* Configure sender information (From Name, From Email, Reply-To)
* Set up SMTP for improved deliverability
* Preview your email template in real-time
* See how your emails will look on desktop and mobile devices
* Send test emails to verify your configuration
* Option to exclude specific plugin emails (WooCommerce, Tutor LMS) 

### How It Works

The plugin hooks into WordPress's email system to apply your custom template to all outgoing emails. It also provides options to modify the sender information and configure SMTP for better deliverability.

## Installation

1. Upload the `custom-email-template` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Email Template to configure your email template

## Configuration

### General Settings
Configure your logo, logo alignment, and footer text that appears at the bottom of every email.

![General Settings](/assets/images/general-settings.png)

### Sender Information
Control who emails appear to be from, including the sender name, email address, and reply-to address.

![Sender Settings](/assets/images/sender-settings.png)

### Email Styling
Customize the colors of your email template, including background colors, text colors, and button colors.

![Styling Settings](/assets/images/styling-settings.png)

### SMTP Configuration
Configure SMTP to send emails reliably through an external mail server.

![SMTP Settings](/assets/images/smtp-settings.png)

## Frequently Asked Questions

### Will this affect all emails sent from my WordPress site?

Yes, this plugin applies your custom template to all emails sent via the WordPress `wp_mail()` function, which is used by WordPress core and most plugins. You can exclude certain plugins like WooCommerce or Tutor LMS.

### Can I use my own custom template?

The current version provides a standard template with customization options. Future versions may include more templates or the ability to create custom templates.

### Does this plugin support SMTP authentication?

Yes, you can configure SMTP settings including authentication, encryption (TLS/SSL), and custom ports.

### Will my emails look good on mobile devices?

Yes, the email template is fully responsive and will look great on all devices. You can preview both desktop and mobile views in the settings.

## Screenshots

1. General Settings - Configure logo, logo alignment, and footer text
2. Sender Information - Set up sender name, email, and reply-to address
3. Styling Settings - Customize colors for backgrounds, text, and elements
4. SMTP Configuration - Configure SMTP settings for improved deliverability
5. Email Preview - Preview how your emails will look with current settings

## Changelog

### 1.0.0
* Initial release

## Credits

Developed by [Àgbà Akin](https://akinolaakeem.com)
managed by [Ssu-Technology Limited](https://swiftspeed.org)

## License

This plugin is licensed under the GPL v2 or later.

```
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
```