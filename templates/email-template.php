<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?php echo esc_html( $subject ); ?></title>
    <?php echo isset( $button_css ) ? wp_kses_post( $button_css ) : ''; ?>
</head>
<body style="margin: 0; padding: 0; background-color: <?php echo esc_attr( $bg_color ); ?>; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;">
    <table width="100%" border="0" cellspacing="8" cellpadding="8" style="background-color: <?php echo esc_attr( $bg_color ); ?>; margin: 0; padding: 5px 0;">
        <tr>
            <td align="center" valign="top">
                <table width="600" border="0" cellspacing="0" cellpadding="0" style="max-width: 600px; background-color: <?php echo esc_attr( $container_bg_color ); ?>; border-radius: 12px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);">
                    <tr>
                        <td style="padding: 24px 30px; border-bottom: 1px solid <?php echo esc_attr( $header_border_color ); ?>; text-align: <?php echo esc_attr( $logo_alignment ); ?>;">
                            <?php
                            // Try to get an attachment ID for the logo URL.
                            $logo_attachment_id = attachment_url_to_postid( $logo_url );

                            if ( $logo_attachment_id ) {
                                // Use wp_get_attachment_image() if an attachment is found.
                                echo wp_get_attachment_image(
                                    $logo_attachment_id,
                                    'full', // Or any other size you prefer.
                                    false,  // No icon.
                                    array(
                                        'alt'   => esc_attr( $site_name . ' Logo' ),
                                        'style' => 'height: 44px; width: auto; display: inline-block;',
                                    )
                                );
                            } else {
                                // Fallback to a normal <img> if there's no valid attachment.
                                ?>
                                <img
                                    src="<?php echo esc_url( $logo_url ); ?>"
                                    alt="<?php echo esc_attr( $site_name ); ?> Logo"
                                    style="height: 44px; width: auto; display: inline-block;"
                                />
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px;">
                            <div class="email-content" style="font-size: 16px; line-height: 1.6; color: <?php echo esc_attr( $secondary_text_color ); ?>;">
                                <?php echo wp_kses_post( $content ); ?>
                            </div>
                            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid <?php echo esc_attr( $footer_border_color ); ?>; text-align: center;">
                                <p style="font-size: 14px; color: <?php echo esc_attr( $footer_text_color ); ?>; margin: 0;">
                                    <?php echo esc_html( $footer_text ); ?>
                                </p>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
