/**
 * Custom Email Template Admin JavaScript
 * 
 * Handles all admin interactions including:
 * - Color picker initialization
 * - Live preview updates
 * - SMTP settings toggles
 * - Test email functionality
 * - Media uploader integration
 */

jQuery(document).ready(function($) {

    /***********************************************************
     *  Immediate Setup for "From" Fields, Button Colors, etc.  *
     ***********************************************************/
    (function() {
        // Fix the "undefined" text issue
        var $fromName  = $('.preview-from-name');
        var $fromEmail = $('.preview-from-email');
        
        // Get values from input fields or placeholders
        var siteName   = $('#custom_email_from_name').val() 
                         || $('#custom_email_from_name').attr('placeholder') 
                         || 'Your Site Name';
        var adminEmail = $('#custom_email_from_email').val() 
                         || $('#custom_email_from_email').attr('placeholder') 
                         || 'admin@example.com';
        
        $fromName.text(siteName);
        $fromEmail.text('<' + adminEmail + '>');
        
        // Fix for potential "undefined" text in subject lines
        var $subjectLine = $('.preview-subject');
        if ($subjectLine.length && $subjectLine.text().indexOf('undefined') >= 0) {
            $subjectLine.text($subjectLine.text().replace('<undefined>', ''));
        }
        
        // Immediately fix button styling
        var buttonBg   = $('#custom_email_button_bg_color').val() || '#696cff';
        var buttonText = $('#custom_email_button_text_color').val() || '#ffffff';
        
        var styleTag = document.createElement('style');
        styleTag.textContent = `
            a[href="#"], .email-content a.button {
                background-color: ${buttonBg} !important;
                color: ${buttonText} !important;
                padding: 10px 20px !important;
                border-radius: 4px !important;
                text-decoration: none !important;
                font-weight: 500 !important;
                display: inline-block !important;
            }
        `;
        document.head.appendChild(styleTag);
    })();

    /***************************************
     *  Color Pickers + Live Preview       *
     ***************************************/
    function initColorPickers() {
        $('.color-input').each(function() {
            var $input       = $(this);
            var defaultColor = $input.data('default-color') || '#000000';
            
            $input.wpColorPicker({
                defaultColor: defaultColor,
                change: function(event, ui) {
                    var color = ui.color.toString();
                    $input.val(color);
                    $input.closest('.color-picker-container')
                          .find('.color-preview')
                          .css('background-color', color);
                    updateEmailPreview();
                },
                clear: function() {
                    updateEmailPreview();
                }
            });
            
            // Force the color to be set correctly on page load
            var currentColor = $input.val() || defaultColor;
            $input.closest('.color-picker-container')
                  .find('.color-preview')
                  .css('background-color', currentColor);
        });
    }
    
    initColorPickers();
    
    // Small fix for WordPress color picker buttons styling
    setTimeout(function() {
        $('.wp-picker-clear, .wp-picker-default').addClass('button-secondary');
    }, 100);
    
    // Clicking the colored circle opens the color picker
    $('.color-preview').click(function() {
        $(this).closest('.color-picker-container').find('.color-input').wpColorPicker('open');
    });
    
    // Update whether the user is in "preview mode" or not
    function isInPreviewMode() {
        return $('.email-template-settings-app').hasClass('preview-mode');
    }

    // SMTP Toggle - Main SMTP toggle for showing/hiding all SMTP settings
    function toggleSmtpSettings() {
        if ($('#enable-smtp').is(':checked')) {
            $('.smtp-settings-container').slideDown();
        } else {
            $('.smtp-settings-container').slideUp();
        }
    }
    
    // Run on page load to initialize SMTP field visibility
    toggleSmtpSettings();
    
    // Run when toggle changes
    $('#enable-smtp').on('change', function() {
        toggleSmtpSettings();
    });
    
    // Show/Hide SMTP Auth fields
    $('#custom_email_smtp_auth').on('change', function() {
        if ($(this).is(':checked')) {
            $('.smtp-auth-fields').slideDown();
        } else {
            $('.smtp-auth-fields').slideUp();
        }
    });
    
    // Initialize SMTP auth fields
    if ($('#custom_email_smtp_auth').is(':checked')) {
        $('.smtp-auth-fields').show();
    } else {
        $('.smtp-auth-fields').hide();
    }

    /************************************************
     *  "Send Test" Email Button (AJAX)            *
     ************************************************/
    $('#test-email-button').on('click', function() {
        var $button = $(this);
        var $result = $('#test-email-result');
        var email   = $('#test-email-address').val();
        
        $button.prop('disabled', true).text('Sending...');
        $result.hide();
        
        $.ajax({
            url: customEmailTemplateSettings.ajaxurl,
            type: 'POST',
            data: {
                action: 'custom_email_template_send_test',
                nonce: customEmailTemplateSettings.nonce,
                email: email
            },
            success: function(response) {
                $button.prop('disabled', false).text('Send Test');
                
                if (response.success) {
                    $result.html(response.data).removeClass('error').addClass('success').show();
                } else {
                    $result.html(response.data).removeClass('success').addClass('error').show();
                }
            },
            error: function() {
                $button.prop('disabled', false).text('Send Test');
                $result.html('Error sending test email').removeClass('success').addClass('error').show();
            }
        });
    });
    
    /************************************************
     *  "Test SMTP" Button (AJAX)                  *
     ************************************************/
    $('#test-smtp-button').on('click', function() {
        var $button = $(this);
        var $result = $('#test-smtp-result');
        
        // Collect SMTP fields
        var host       = $('input[name="custom_email_smtp_host"]').val();
        var port       = $('input[name="custom_email_smtp_port"]').val();
        var encryption = $('select[name="custom_email_smtp_encryption"]').val();
        var auth       = $('input[name="custom_email_smtp_auth"]').is(':checked') ? 'yes' : 'no';
        var username   = $('input[name="custom_email_smtp_username"]').val();
        var password   = $('input[name="custom_email_smtp_password"]').val();
        
        $button.prop('disabled', true).text('Testing...');
        $result.hide();
        
        $.ajax({
            url: customEmailTemplateSettings.ajaxurl,
            type: 'POST',
            data: {
                action: 'custom_email_template_test_smtp',
                nonce: customEmailTemplateSettings.nonce,
                host: host,
                port: port,
                encryption: encryption,
                auth: auth,
                username: username,
                password: password
            },
            success: function(response) {
                $button.prop('disabled', false).text('Test SMTP Connection');
                
                if (response.success) {
                    $result.html(response.data).removeClass('error').addClass('success').show();
                } else {
                    $result.html(response.data).removeClass('success').addClass('error').show();
                }
            },
            error: function() {
                $button.prop('disabled', false).text('Test SMTP Connection');
                $result.html('Error testing SMTP connection').removeClass('success').addClass('error').show();
            }
        });
    });
    
    /************************************************
     *  Live Preview Updates                        *
     ************************************************/
    function updateEmailPreview() {
        var logoUrl           = $('#custom_email_logo_url').val();
        var logoAlignment     = $('input[name="custom_email_logo_alignment"]:checked').val() || 'center';
        var bgColor           = $('#custom_email_bg_color').val() || '#f7f7f7';
        var containerBgColor  = $('#custom_email_container_bg_color').val() || '#ffffff';
        var headerBorderColor = $('#custom_email_header_border_color').val() || '#e0e2ea';
        var primaryTextColor  = $('#custom_email_primary_text_color').val() || '#212327';
        var secondaryTextColor= $('#custom_email_secondary_text_color').val() || '#5b616f';
        var footerBorderColor = $('#custom_email_footer_border_color').val() || '#e0e2ea';
        var footerTextColor   = $('#custom_email_footer_text_color').val() || '#9da3af';
        var buttonBgColor     = $('#custom_email_button_bg_color').val() || '#696cff';
        var buttonTextColor   = $('#custom_email_button_text_color').val() || '#ffffff';
        var footerText        = $('#custom_email_footer_text').val() || 'This is an automatically generated email. Please do not reply.';
        var fromName          = $('#custom_email_from_name').val() 
                                || $('#custom_email_from_name').attr('placeholder') 
                                || 'Your Site Name';
        var fromEmail         = $('#custom_email_from_email').val() 
                                || $('#custom_email_from_email').attr('placeholder') 
                                || 'admin@example.com';
        
        // Desktop
        updatePreviewElements(
            '#email-preview', 
            logoUrl, logoAlignment, bgColor, containerBgColor, headerBorderColor,
            primaryTextColor, secondaryTextColor, footerBorderColor, footerTextColor,
            buttonBgColor, buttonTextColor, footerText
        );
        // Mobile
        updatePreviewElements(
            '#email-preview-mobile', 
            logoUrl, logoAlignment, bgColor, containerBgColor, headerBorderColor,
            primaryTextColor, secondaryTextColor, footerBorderColor, footerTextColor,
            buttonBgColor, buttonTextColor, footerText
        );
        
        // Update Sender info in preview
        $('.preview-from-name').text(fromName);
        $('.preview-from-email').text('<' + fromEmail + '>');
        
        // Fix any leftover <undefined>
        var $subjectLine = $('.preview-subject');
        if ($subjectLine.length && $subjectLine.text().indexOf('undefined') >= 0) {
            $subjectLine.text($subjectLine.text().replace('<undefined>', ''));
        }
    }
    
    function updatePreviewElements(selector, logoUrl, logoAlignment, bgColor, containerBgColor, headerBorderColor,
                                   primaryTextColor, secondaryTextColor, footerBorderColor, footerTextColor,
                                   buttonBgColor, buttonTextColor, footerText) {
        var $preview          = $(selector);
        var $body             = $preview.find('body');
        var $bodyTable        = $preview.find('body > table');
        var $container        = $preview.find('body > table > tbody > tr > td > table');
        var $header           = $preview.find('body > table > tbody > tr > td > table > tbody > tr:first-child > td');
        var $logo             = $preview.find('body > table > tbody > tr > td > table > tbody > tr:first-child > td > img');
        var $content          = $preview.find('.email-content');
        var $footer           = $preview.find('body > table > tbody > tr > td > table > tbody > tr:last-child > td > div');
        var $footerP          = $footer.find('p');
        var $buttons          = $preview.find('a.button, .button, a[style*="background-color"]');
        var $h1               = $preview.find('h1');
        
        // Update basic styles
        $body.css('background-color', bgColor);
        $bodyTable.css('background-color', bgColor);
        $container.css('background-color', containerBgColor);
        $header.css({
            'border-bottom-color': headerBorderColor,
            'text-align': logoAlignment
        });
        if (logoUrl) {
            $logo.attr('src', logoUrl);
        }
        $content.css('color', secondaryTextColor);
        $footer.css('border-top-color', footerBorderColor);
        $footerP.css('color', footerTextColor).html(footerText);
        $h1.css('color', primaryTextColor);
        
        // Update buttons
        $buttons.css({
            'background-color': buttonBgColor,
            'color': buttonTextColor,
            'padding': '10px 20px',
            'border-radius': '4px',
            'text-decoration': 'none',
            'font-weight': '500',
            'display': 'inline-block'
        });
        
        // Inline override
        $preview.find('a[href="#"]').attr('style', 
            `background-color:${buttonBgColor} !important; 
             color:${buttonTextColor} !important; 
             padding:10px 20px !important; 
             border-radius:4px !important; 
             text-decoration:none !important; 
             font-weight:500 !important; 
             display:inline-block !important;`
        );
    }
    
    // Watch changes in preview-update fields
    $('.preview-update').on('input change', function() {
        updateEmailPreview();
    });
    
    // Final initial preview update
    updateEmailPreview();
    
    // Logo Alignment Options
    $('.alignment-option').on('click', function() {
        $('.alignment-option').removeClass('active');
        $(this).addClass('active');
        $(this).find('input[type="radio"]').prop('checked', true).trigger('change');
    });
    
    // Media Uploader: Logo
    $('#upload-logo-button').on('click', function(e) {
        e.preventDefault();
        
        var customUploader = wp.media({
            title: 'Choose Logo',
            button: { text: 'Use this image' },
            multiple: false
        });
        
        customUploader.on('select', function() {
            var attachment = customUploader.state().get('selection').first().toJSON();
            $('#custom_email_logo_url').val(attachment.url).trigger('change');
            updateEmailPreview();
        });
        
        customUploader.open();
    });
    
    // Media Uploader: Avatar
    $('#upload-avatar-button').on('click', function(e) {
        e.preventDefault();
        
        var customUploader = wp.media({
            title: 'Choose Avatar',
            button: { text: 'Use this image' },
            multiple: false
        });
        
        customUploader.on('select', function() {
            var attachment = customUploader.state().get('selection').first().toJSON();
            $('#custom_email_avatar_url').val(attachment.url).trigger('change');
        });
        
        customUploader.open();
    });
    
    /************************************************
     *  Device Preview Toggle (Desktop / Mobile)    *
     ************************************************/
    $('.preview-device-button').on('click', function() {
        $('.preview-device-button').removeClass('active');
        $(this).addClass('active');
        
        var device = $(this).data('device');
        $('.preview-container').hide();
        $('.preview-container.' + device).show();
        
        // Additional fix for the mobile frame
        if (device === 'mobile') {
            $('.mobile-frame table').css('width', '100%');
        }
    });
    
    /************************************************
     *  Tab Navigation                             *
     ************************************************/
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
    
    // Handle tab clicks with nonce security
    $('.email-template-tabs a').on('click', function(e) {
        e.preventDefault();
        
        // Don't do anything if in preview mode
        if ($('.email-template-settings-app').hasClass('preview-mode')) {
            return;
        }
        
        var tabId = $(this).data('tab');
        
        // Set active tab in hidden form field - this ensures form submission has the tab ID
        $('#active-tab-field').val(tabId);
        
        // Update URL for browser history (without reloading)
        var newUrl = new URL(window.location.href);
        newUrl.searchParams.set('tab_id', tabId);
        newUrl.searchParams.set('_wpnonce', customEmailTemplateSettings.tab_nonce);
        history.pushState({}, '', newUrl.toString());
        
        // Update UI
        $('.email-template-tabs a').removeClass('active');
        $(this).addClass('active');
        $('.settings-tab-content').removeClass('active');
        $('#' + tabId).addClass('active');
    });
});