<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    die;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?php echo esc_html( $subject ); ?></title>
</head>
<body style="margin: 0; padding: 0; background-color: <?php echo esc_attr( $bg_color ); ?>; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;">
    <table width="100%" border="0" cellspacing="8" cellpadding="8" style="background-color: <?php echo esc_attr( $bg_color ); ?>; margin: 0; padding: 5px 0; table-layout: fixed; width: 100%; max-width: 100%;">
        <tr>
            <td align="center" valign="top">
                <table width="600" border="0" cellspacing="0" cellpadding="0" style="max-width: 600px; width: 100%; background-color: <?php echo esc_attr( $container_bg_color ); ?>; border-radius: 12px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); table-layout: fixed;">
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
                                        'style' => 'height: 44px; width: auto; display: inline-block; max-width: 100%;',
                                    )
                                );
                            } else {
                                // Fallback to a normal <img> if there's no valid attachment.
                                ?>
                                <img
                                    src="<?php echo esc_url( $logo_url ); ?>"
                                    alt="<?php echo esc_attr( $site_name ); ?> Logo"
                                    style="height: 44px; width: auto; display: inline-block; max-width: 100%;"
                                />
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px;">
                            <div class="email-content" style="font-size: 16px; line-height: 1.6; color: <?php echo esc_attr( $secondary_text_color ); ?>; word-wrap: break-word; overflow-wrap: break-word; word-break: break-word;">
                                <?php 
                                // Add specific styling for buttons in the content
                                $button_styled_content = preg_replace(
                                    '/<a([^>]*?)href="([^"]*?)"([^>]*?)>(.*?)<\/a>/i',
                                    '<a$1href="$2"$3 style="word-break: break-all; max-width: 100%; display: inline-block;">$4</a>',
                                    $content
                                );
                                
                                // Style buttons specially
                                $button_styled_content = preg_replace(
                                    '/<a([^>]*?)href="#"([^>]*?)>(.*?)<\/a>/i',
                                    '<a$1href="#"$2 style="background-color: ' . esc_attr($button_bg_color) . ' !important; color: ' . esc_attr($button_text_color) . ' !important; padding: 10px 20px !important; border-radius: 4px !important; text-decoration: none !important; font-weight: 500 !important; display: inline-block !important;">$3</a>',
                                    $button_styled_content
                                );
                                
                                // Also style class="button" elements
                                $button_styled_content = preg_replace(
                                    '/<a([^>]*?)class="([^"]*?)button([^"]*?)"([^>]*?)>(.*?)<\/a>/i',
                                    '<a$1class="$2button$3"$4 style="background-color: ' . esc_attr($button_bg_color) . ' !important; color: ' . esc_attr($button_text_color) . ' !important; padding: 10px 20px !important; border-radius: 4px !important; text-decoration: none !important; font-weight: 500 !important; display: inline-block !important;">$5</a>',
                                    $button_styled_content
                                );
                                
                                echo wp_kses_post( $button_styled_content );
                                ?>
                            </div>
                            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid <?php echo esc_attr( $footer_border_color ); ?>; text-align: center;">
                                <p style="font-size: 14px; color: <?php echo esc_attr( $footer_text_color ); ?>; margin: 0; word-wrap: break-word; overflow-wrap: break-word;">
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