<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ClassCube;

LTI_Consumer::init();

class LTI_Consumer {

    public static $base_path = false;
    private static $options = [];
    private static $default_options = [
        'share_email' => true,
        'share_username' => true,
        'css_class' => '',
        'css_style' => 'border:none;width:100%;height:400px;',
        'allow_fullscreen' => true,
        'require_login' => true
    ];
    private static $tools = false;

    public static function init() {
        self::$base_path = dirname( __DIR__ );

        if ( is_admin() ) {
            add_action( 'admin_menu', function() {
                self::admin_menus();
            } );

            add_action( 'admin_enqueue_scripts', '\ClassCube\LTI_Consumer::admin_css' );
            add_action( 'admin_post_add_tool', '\ClassCube\LTI_Consumer::add_edit_form' );
        }

        add_shortcode( 'lti', '\ClassCube\LTI_Consumer::shortcode' );
        add_action( 'admin_post_cc-lti-launch', '\ClassCube\LTI_Consumer::lti_launch' );
        add_action( 'admin_post_nopriv_cc-lti-launch', '\ClassCube\LTI_Consumer::lti_launch' );
    }

    /**
     * Adds the CSS when needed
     * @param type $hook
     */
    public static function admin_css( $hook ) {
        if ( $hook == 'toplevel_page_cc-lti' || $hook == 'lti-consumer_page_cc-lti-settings' ) {
            wp_enqueue_style( 'cc-lti', plugins_url( 'css/style.css', __DIR__ ), [], false );
        }
    }

    private static function admin_menus() {
        add_menu_page( __( 'LTI Consumer', 'cc-lti' ), __( 'LTI Consumer', 'cc-lti' ), 'manage_options', 'cc-lti', function() {
            
        }, '', 99 );

        add_submenu_page( 'cc-lti', __( 'LTI Consumer Tools', 'cc-lti' ), __( 'LTI Tools', 'cc-lti' ), 'manage_options', 'cc-lti', function() {
            self::admin_lti_tools();
        } );

        add_submenu_page( 'cc-lti', __( 'LTI Consumer Settings', 'cc-lti' ), __( 'Settings', 'cc-lti' ), 'manage_options', 'cc-lti-settings', function() {
            require_once(__DIR__ . '/pages/admin-settings.php');
        } );
    }

    /**
     * Outputs the HTML for the admin page where all of the existing 
     * LTI tools are listed and links to create new tools. 
     */
    private static function admin_lti_tools() {
        if ( isset( $_GET[ 'add' ] ) ) {
            require_once(__DIR__ . '/pages/admin-tool-edit.php');
        }
        else if ( isset( $_GET[ 'edit' ] ) ) {
            require_once(__DIR__ . '/pages/admin-tool-edit.php');
        }
        else if ( isset( $_GET[ 'view' ] ) ) {
            require_once(__DIR__ . '/pages/admin-tool-view.php');
        }
        else {
            require_once(__DIR__ . '/pages/admin-tools-table.php');
        }
    }

    /**
     * Updates the settings based on POST data and refreshes the cache
     */
    private static function update_settings() {
        self::update_setting( 'share_email', !empty( $_POST[ 'cc-share-email' ] ), true );
        self::update_setting( 'share_username', !empty( $_POST[ 'cc-share-username' ] ), true );

        self::update_setting( 'css_class', $_POST[ 'cc-css-class' ], true );
        self::update_setting( 'css_style', $_POST[ 'cc-css-style' ], true );
        self::update_setting( 'allow_fullscreen', !empty( $_POST[ 'cc-allow-fullscreen' ] ), false );
        self::update_setting( 'require_login', !empty( $_POST[ 'cc-require-login' ] ) );
    }

    /**
     * Gets a setting for the plugin, storing it in a static if needed for later
     * 
     * @param type $setting_name
     * @param type $default
     * @param boolean $force_refresh
     */
    private static function get_setting( $setting_name, $default = '', $force_refresh = false ) {
        if ( empty( self::$options ) || $force_refresh ) {
            self::$options = array_merge( self::$default_options, (array) get_option( 'classcube-lti' ) );
        }

        if ( array_key_exists( $setting_name, self::$options ) ) {
            return self::$options[ $setting_name ];
        }
        return $default;
    }

    private static function update_setting( $setting_name, $value, $delay = false ) {
        self::$options[ $setting_name ] = $value;

        if ( !$delay ) {
            update_option( 'classcube-lti', self::$options );
        }
    }

    public static function add_edit_form() {
        $_POST[ 'cc-tool-name' ] = trim( $_POST[ 'cc-tool-name' ] );
        $_POST[ 'cc-base-url' ] = trim( $_POST[ 'cc-base-url' ] );

        if ( empty( $_POST[ 'cc-tool-name' ] ) || empty( $_POST[ 'cc-base-url' ] ) ) {
            if ( !empty( $_POST[ 'cc-id' ] ) ) {
                
            }
            else {
                wp_redirect( admin_url( 'admin.php?page=cc-lti&add&req' ) );
                die();
            }
        }

        $settings = [
            'name' => $_POST[ 'cc-tool-name' ],
            'base_url' => $_POST[ 'cc-base-url' ],
            'consumer_key' => $_POST[ 'cc-consumer-key' ],
            'shared_secret' => $_POST[ 'cc-shared-secret' ],
            'custom_parameters' => $_POST[ 'cc-custom-parameters' ],
            'share_username' => !empty( $_POST[ 'cc-share-username' ] ),
            'share_email' => !empty( $_POST[ 'cc-share-email' ] ),
            'require_login' => !empty( $_POST[ 'cc-require-login' ] ),
            'id' => $_POST[ 'cc-id' ]
        ];

        $tool_id = self::add_tool( $settings );

        wp_redirect( admin_url( 'admin.php?page=cc-lti&view=' . $tool_id ) );
        die();
    }

    /**
     * Adds a new tool to the options table.
     * 
     * This doesn't do any validation that the information passed is valid.
     * That should be taken care of before calling this method.
     * 
     * @param type $tool_info
     */
    public static function add_tool( $tool_info = [] ) {
        if ( empty( $tool_info[ 'id' ] ) ) {
            $tool_info[ 'id' ] = uniqid();
        }

        $tool_id = $tool_info[ 'id' ];

        if ( self::$tools === false ) {
            self::$tools = get_option( 'classcube-lti-tools', [] );
        }

        self::$tools[ $tool_id ] = $tool_info;
        self::$tools[ $tool_id ][ 'id' ] = $tool_id;

        update_option( 'classcube-lti-tools', self::$tools );

        return $tool_id;
    }

    public static function get_tool( $tool_id ) {
        if ( self::$tools === false ) {
            self::$tools = get_option( 'classcube-lti-tools', [] );
        }

        if ( isset( self::$tools[ $tool_id ] ) ) {
            return self::$tools[ $tool_id ];
        }
        return false;
    }

    /**
     * Retrieves information on a tool based on a url.
     * 
     * This looks for the base url field that is found at the beginning
     * of the url value passed. 
     * 
     * @param type $url
     */
    private static function find_tool( $url ) {
        $tools = get_option( 'classcube-lti-tools', [] );

        if ( !empty( $tools ) ) {
            foreach ( $tools as $tool ) {
                if ( strpos( $url, $tool[ 'base_url' ] ) === 0 ) {
                    return $tool;
                }
            }
        }
        return false;
    }

    public static function shortcode( $atts = [] ) {
        global $post;

        $atts = shortcode_atts( [
            'url' => '',
            'allow_fullscreen' => self::get_setting( 'allow_fullscreen' ),
            'css_class' => self::get_setting( 'css_class' ),
            'css_style' => self::get_setting( 'css_style' ),
            'template' => 'cc-lti-login'
                ], $atts );

        if ( empty( $atts[ 'url' ] ) ) {
            return __( 'URL is required for the LTI short code', 'cc-lti' );
        }

        $tool = self::find_tool( $atts[ 'url' ] );
        if ( $tool === false ) {
            return sprintf( __( '%s not found in registered LTI tools', 'cc-lti' ), $atts[ 'url' ] );
        }

        if ( !empty( $tool[ 'require_login' ] ) && !is_user_logged_in() ) {
            if ( !empty( $atts[ 'template' ] ) ) {
                // Template specified, use it if it exists
                if ( !preg_match( '/\.php$/i', $atts[ 'template' ] ) ) {
                    $atts[ 'template' ] .= '.php';
                }
                $template = locate_template( $atts[ 'template' ] );
                if ( !empty( $template ) ) {
                    // It was found, use it. If it wasn't found it'll just pass through
                    ob_start();
                    load_template( $template );
                    return ob_get_clean();
                }
            }
            return __( 'You must be logged in to launch an LTI tool', 'cc-lti' );
        }

        $html = '<iframe style="' . $atts[ 'css_style' ] . '" class="' . $atts[ 'css_class' ] . '" src="' . admin_url( 'admin-post.php?action=cc-lti-launch&tool=' . $tool[ 'id' ] . '&launch=' . urlencode( $atts[ 'url' ] ) ) . '&p=' . $post->ID . '" ' . ($atts[ 'allow_fullscreen' ] ? 'allowfullscreen' : '') . '></iframe>';

        return $html;
    }

    public static function lti_launch() {
        $tool_id = $_GET[ 'tool' ];
        $launch_url = html_entity_decode( urldecode( $_GET[ 'launch' ] ) );

        $tool = self::get_tool( $tool_id );

        if ( $tool === false ) {
            die( __( 'Cannot find requested LTI tool', 'cc-lti' ) );
        }
        else if ( strpos( $launch_url, $tool[ 'base_url' ] ) !== 0 ) {
            die( __( 'Requested tool does not match url', 'cc-lti' ) );
        }
        require_once(__DIR__ . '/OAuth.php');
        $current_user = wp_get_current_user();
        $post = get_post( $_GET[ 'p' ] );
        $plugin_data = get_plugin_data( self::$base_path . '/classcube-lti-consumer.php' );
        $post_data = [
            'lti_message_type' => 'basic-lti-launch-request',
            'lti_version' => 'LTI-1p0',
            'lis_person_sourcedid' => !empty( $current_user->user_login ) ? $current_user->user_login : '',
            'resource_link_id' => $post->ID,
            'resource_link_title' => $post->post_title,
            'resource_link_description' => $post->post_title,
            'context_title' => get_bloginfo( 'name' ),
            'context_id' => get_bloginfo( 'home' ),
            'roles' => 'Learner',
            'user_id' => md5( get_bloginfo( 'home' ) . !empty( $current_user->ID ) ? $current_user->ID : 0 ),
            'launch_presentation_locale' => get_locale(),
            'tool_consumer_info_product_family_code' => 'wordpress',
            'tool_consumer_info_version' => get_bloginfo( 'version' ),
            'tool_consumer_instance_url' => get_permalink( $post ),
            'tool_consumer_instance_name' => get_bloginfo( 'name' ),
            // Strip https? in case they change later
            'tool_consumer_instance_guid' => preg_replace( '/^https?:\/\//i', '', get_bloginfo( 'home' ) ),
            'classcube_version' => $plugin_data[ 'Version' ],
            'classcube_info' => 'https://classcube.com',
            'oauth_nonce' => md5( uniqid( '', true ) ),
            'oauth_consumer_key' => $tool[ 'consumer_key' ],
            'oauth_timestamp' => time(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_version' => '1.0',
            'oauth_callback' => 'about:blank'
        ];


        if ( $tool[ 'share_email' ] ) {
            $post_data[ 'lis_person_contact_email_primary' ] = $current_user->user_email;
        }
        if ( $tool[ 'share_username' ] ) {
            $post_data[ 'lis_person_name_given' ] = $current_user->user_firstname;
            $post_data[ 'lis_person_name_family' ] = $current_user->user_lastname;
            $post_data[ 'lis_person_name_full' ] = $current_user->display_name;
        }

        if ( !empty( $tool[ 'custom_parameters' ] ) ) {
            $params = explode( "\n", trim( $tool[ 'custom_parameters' ] ) );
            foreach ( $params as $param ) {
                $param = explode( "=", trim( $param ) );
                $post_data[ 'custom_' . $param[ 0 ] ] = $param[ 1 ];
            }
        }

        $consumer = new OAuthConsumer( $tool[ 'consumer_key' ], $tool[ 'shared_secret' ], 'about:blank' );
        $oauth_request = OAuthRequest::from_consumer_and_token(
                        $consumer, null, 'POST', $launch_url, $post_data );
        $oauth_request->sign_request(
                new OAuthSignatureMethod_HMAC_SHA1(), $consumer, null );
        $params = $oauth_request->get_parameters();
        ?>
        <noscript>
        <style>
            input { visibility: visible !important; }
        </style>
        </noscript>
        <form id="cc-launch" action="<?php echo $_GET[ 'launch' ]; ?>" method="POST">
            <?php
            foreach ( $params as $k => $v ) {
                echo '<input type="hidden" name="' . $k . '" value="' . $v . '">';
            }
            ?>
            <input type="submit" style="visibility: hidden;"  value="<?php _e( 'Launch Tool', 'cc-lti' ); ?>">
        </form>
        <script type="text/javascript">
            document.getElementById('cc-launch').submit();
        </script>
        <?php
        die();
    }

    private static function generate_hmac_signature( $post_data, $secret, $launch_url ) {
        $post_string = '';
        ksort( $post_data );
        foreach ( $post_data as $key => $value ) {
            $post_string .= $key . '=' . ($value) . '&';
        }
        $post_string = rtrim( $post_string, '&' );
//        var_dump( $post_string );
        $base_string = 'POST&' . self::urlencodeRFC3986( $launch_url ) . '&' . self::urlencodeRFC3986( $post_string );

        $signature = base64_encode( hash_hmac( 'sha1', $base_string, $secret, true ) );

        return $signature;

        $key_parts = urlencodeRFC3986( $secret );
//$key = implode('&', $key_parts);
        $key = $key_parts . '&';
        $base_string = 'GET&' . urlencodeRFC3986( $url ) . '&' . urlencodeRFC3986( $post_string );
        $signature = base64_encode( hash_hmac( 'sha1', $base_string, $key, true ) );
    }

    private static function urlencodeRFC3986( $string ) { /* {{{ */
        return str_replace( '%7E', '~', rawurlencode( $string ) );
    }

}
