jQuery(document).ready(function($) {

    // Immediate execution of critical functions to prevent flicker
    (function() {
        // Fix the undefined text issue
        var $fromName = $('.preview-from-name');
        var $fromEmail = $('.preview-from-email');
        
        // Get values from input fields first
        var siteName = $('#custom_email_from_name').val() || $('#custom_email_from_name').attr('placeholder') || 'Your Site Name';
        var adminEmail = $('#custom_email_from_email').val() || $('#custom_email_from_email').attr('placeholder') || 'admin@example.com';
        
        // Always set these values immediately, don't check for undefined
        $fromName.text(siteName);
        $fromEmail.text('<' + adminEmail + '>');
        
        // Fix for issue with BuyNaijaMade <undefined>
        var $subjectLine = $('BuyNaijaMade, .preview-subject');
        if ($subjectLine.length && $subjectLine.text().indexOf('undefined') >= 0) {
            $subjectLine.text($subjectLine.text().replace('<undefined>', ''));
        }
        
        // Immediately fix button styling
        var buttonBg = $('#custom_email_button_bg_color').val() || '#696cff';
        var buttonText = $('#custom_email_button_text_color').val() || '#ffffff';
        
        // Apply styles immediately
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

    // Force wpColorPicker to initialize with proper styles
    function initColorPickers() {
        // Initialize color pickers
        $('.color-input').each(function() {
            var $input = $(this);
            var defaultColor = $input.data('default-color') || '#000000';
            
            $input.wpColorPicker({
                defaultColor: defaultColor,
                change: function(event, ui) {
                    var color = ui.color.toString();
                    $input.val(color);
                    $input.closest('.color-picker-container').find('.color-preview').css('background-color', color);
                    updateEmailPreview();
                },
                clear: function() {
                    updateEmailPreview();
                }
            });
            
            // Force the color to be set correctly initially
            var currentColor = $input.val() || defaultColor;
            $input.closest('.color-picker-container').find('.color-preview').css('background-color', currentColor);
        });
    }
    
    // Initialize all color pickers
    initColorPickers();
    
    // Fix for WordPress color picker buttons
    setTimeout(function() {
        $('.wp-picker-clear, .wp-picker-default').addClass('button-secondary');
    }, 100);
    
    // Color preview click handler
    $('.color-preview').click(function() {
        $(this).closest('.color-picker-container').find('.color-input').wpColorPicker('open');
    });
    
    // Check if we're in preview mode
    function isInPreviewMode() {
        return $('.email-template-settings-app').hasClass('preview-mode');
    }
    
    // Tab Navigation - only works in settings mode
    $('.email-template-tabs a').click(function(e) {
        e.preventDefault();
        
        // Skip if we're in preview mode (sidebar is hidden anyway)
        if (isInPreviewMode()) {
            return;
        }
        
        // Get the tab ID from data attribute
        var tabId = $(this).data('tab');
        
        // Remove active class from all tabs
        $('.email-template-tabs a').removeClass('active');
        
        // Add active class to clicked tab
        $(this).addClass('active');
        
        // Hide all tab content
        $('.settings-tab-content').removeClass('active');
        
        // Show the tab content for the clicked tab
        $('#' + tabId).addClass('active');
        
        // Update URL hash without page refresh
        window.history.pushState(null, null, '#' + tabId);
        
        // Update form action to include the new hash
        var baseAction = $('#email-template-form').attr('action').split('#')[0]; 
        $('#email-template-form').attr('action', baseAction + '#' + tabId);
    });
    
    // Device Preview Toggle
    $('.preview-device-button').click(function() {
        $('.preview-device-button').removeClass('active');
        $(this).addClass('active');
        
        var device = $(this).data('device');
        $('.preview-container').hide();
        $('.preview-container.' + device).show();
        
        // Additional fixes for mobile view
        if (device === 'mobile') {
            // Ensure content fits mobile frame
            $('.mobile-frame table').css('width', '100%');
        }
    });
    
    // Logo Alignment option click handler
    $('.alignment-option').click(function() {
        $('.alignment-option').removeClass('active');
        $(this).addClass('active');
        $(this).find('input[type="radio"]').prop('checked', true).trigger('change');
    });
    
    // Initialize media uploader for logo
    $('#upload-logo-button').click(function(e) {
        e.preventDefault();
        
        var customUploader = wp.media({
            title: 'Choose Logo',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });
        
        customUploader.on('select', function() {
            var attachment = customUploader.state().get('selection').first().toJSON();
            $('#custom_email_logo_url').val(attachment.url).trigger('change');
            updateEmailPreview();
        });
        
        customUploader.open();
    });
    
    // Initialize media uploader for avatar
    $('#upload-avatar-button').click(function(e) {
        e.preventDefault();
        
        var customUploader = wp.media({
            title: 'Choose Avatar',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });
        
        customUploader.on('select', function() {
            var attachment = customUploader.state().get('selection').first().toJSON();
            $('#custom_email_avatar_url').val(attachment.url).trigger('change');
        });
        
        customUploader.open();
    });
    
    // SMTP authentication toggle
    $('#custom_email_smtp_auth').change(function() {
        if ($(this).is(':checked')) {
            $('.smtp-auth-fields').slideDown();
        } else {
            $('.smtp-auth-fields').slideUp();
        }
    });
    
    // Test email functionality
    $('#test-email-button').click(function() {
        var $button = $(this);
        var $result = $('#test-email-result');
        var email = $('#test-email-address').val();
        
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
    
    // Test SMTP connection
    $('#test-smtp-button').click(function() {
        var $button = $(this);
        var $result = $('#test-smtp-result');
        
        // Get SMTP settings from form
        var host = $('input[name="custom_email_smtp_host"]').val();
        var port = $('input[name="custom_email_smtp_port"]').val();
        var encryption = $('select[name="custom_email_smtp_encryption"]').val();
        var auth = $('input[name="custom_email_smtp_auth"]').is(':checked') ? 'yes' : 'no';
        var username = $('input[name="custom_email_smtp_username"]').val();
        var password = $('input[name="custom_email_smtp_password"]').val();
        
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
    
    // Live preview functionality
    function updateEmailPreview() {
        var logoUrl = $('#custom_email_logo_url').val();
        var logoAlignment = $('input[name="custom_email_logo_alignment"]:checked').val() || 'center';
        var bgColor = $('#custom_email_bg_color').val() || '#f7f7f7';
        var containerBgColor = $('#custom_email_container_bg_color').val() || '#ffffff';
        var headerBorderColor = $('#custom_email_header_border_color').val() || '#e0e2ea';
        var primaryTextColor = $('#custom_email_primary_text_color').val() || '#212327';
        var secondaryTextColor = $('#custom_email_secondary_text_color').val() || '#5b616f';
        var footerBorderColor = $('#custom_email_footer_border_color').val() || '#e0e2ea';
        var footerTextColor = $('#custom_email_footer_text_color').val() || '#9da3af';
        var buttonBgColor = $('#custom_email_button_bg_color').val() || '#696cff';
        var buttonTextColor = $('#custom_email_button_text_color').val() || '#ffffff';
        var footerText = $('#custom_email_footer_text').val() || 'This is an automatically generated email. Please do not reply.';
        var fromName = $('#custom_email_from_name').val() || $('#custom_email_from_name').attr('placeholder') || 'Your Site Name';
        var fromEmail = $('#custom_email_from_email').val() || $('#custom_email_from_email').attr('placeholder') || 'admin@example.com';
        
        // Update preview elements - desktop
        updatePreviewElements('#email-preview', logoUrl, logoAlignment, bgColor, containerBgColor, headerBorderColor, 
                             primaryTextColor, secondaryTextColor, footerBorderColor, footerTextColor, 
                             buttonBgColor, buttonTextColor, footerText);
        
        // Update preview elements - mobile
        updatePreviewElements('#email-preview-mobile', logoUrl, logoAlignment, bgColor, containerBgColor, headerBorderColor, 
                             primaryTextColor, secondaryTextColor, footerBorderColor, footerTextColor, 
                             buttonBgColor, buttonTextColor, footerText);
        
        // Update sender info in preview
        $('.preview-from-name').text(fromName);
        $('.preview-from-email').text('<' + fromEmail + '>');
        
        // Fix subject line if needed
        var $subjectLine = $('BuyNaijaMade, .preview-subject');
        if ($subjectLine.length && $subjectLine.text().indexOf('undefined') >= 0) {
            $subjectLine.text($subjectLine.text().replace('<undefined>', ''));
        }
    }
    
    function updatePreviewElements(selector, logoUrl, logoAlignment, bgColor, containerBgColor, headerBorderColor, 
                                  primaryTextColor, secondaryTextColor, footerBorderColor, footerTextColor, 
                                  buttonBgColor, buttonTextColor, footerText) {
        var $preview = $(selector);
        var $body = $preview.find('body');
        var $bodyTable = $preview.find('body > table');
        var $container = $preview.find('body > table > tbody > tr > td > table');
        var $header = $preview.find('body > table > tbody > tr > td > table > tbody > tr:first-child > td');
        var $logo = $preview.find('body > table >tbody > tr > td > table >tbody > tr:first-child > td > img');
        var $content = $preview.find('.email-content');
        var $footer = $preview.find('body > table > tbody > tr > td > table > tbody > tr:last-child > td > div');
        var $footerP = $preview.find('body > table > tbody > tr > td > table > tbody > tr:last-child >td > div > p');
        var $buttons = $preview.find('a.button, .button, a[style*="background-color"]');
        var $h1 = $preview.find('h1');
        
        // Update styles
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
        $footerP.css('color', footerTextColor);
        $footerP.html(footerText);
        $h1.css('color', primaryTextColor);
        
        // Ensure buttons have proper styling with sufficient contrast
        $buttons.attr('style', '');
        $buttons.css({
            'background-color': buttonBgColor,
            'color': buttonTextColor,
            'padding': '10px 20px',
            'border-radius': '4px',
            'text-decoration': 'none',
            'font-weight': '500',
            'display': 'inline-block'
        });
        
        // Also update the inline button in sample content - with !important to ensure visibility
        $preview.find('a[href="#"]').attr('style', 
            'background-color:' + buttonBgColor + ' !important;' + 
            'color:' + buttonTextColor + ' !important;' + 
            'padding: 10px 20px !important;' + 
            'border-radius: 4px !important;' + 
            'text-decoration: none !important;' + 
            'font-weight: 500 !important;' +
            'display: inline-block !important;'
        );
    }
    
    // Initialize SMTP auth fields visibility
    if ($('#custom_email_smtp_auth').is(':checked')) {
        $('.smtp-auth-fields').show();
    } else {
        $('.smtp-auth-fields').hide();
    }
    
    // Update preview when any field changes
    $('.preview-update').on('input change', function() {
        updateEmailPreview();
    });
    
    // Initial preview update
    updateEmailPreview();
    
    // Set initial active tab based on URL hash or default to first tab
    var initialTab = window.location.hash ? window.location.hash.substring(1) : 'general';
    $('.email-template-tabs a[data-tab="' + initialTab + '"]').addClass('active');
    $('#' + initialTab).addClass('active');

    // Ajax Save Button Handler
    $('#save-button').click(function(e) {
        e.preventDefault();
        var $button = $(this);
        $button.prop('disabled', true).text('Saving...');
        
        var formData = $('#custom-email-template-form').serialize();
        
        $.ajax({
            url: customEmailTemplateSettings.ajaxurl,
            type: 'POST',
            data: {
                action: 'custom_email_template_save',
                nonce: customEmailTemplateSettings.nonce,
                form_data: formData
            },
            success: function(response) {
                if (response.success) {
                    alert('Settings saved successfully.');
                } else {
                    alert('Error saving settings: ' + response.data);
                }
                $button.prop('disabled', false).text('Save');
            },
            error: function() {
                alert('An unexpected error occurred while saving settings.');
                $button.prop('disabled', false).text('Save');
            }
        });
    });
});