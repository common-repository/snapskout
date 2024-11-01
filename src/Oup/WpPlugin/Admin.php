<?php
/**
 * SnapSkout plugin.
 * User: Pierre Jacomet (TalktUp LLC) pierrej@talktup.com
 *
 * Date: 2014-11
 *
 */


class Oup_WpPlugin_Admin {

    const OPTION_DEFAULT          = 0;
    const OPTION_TYPE             = 1;
    const OPTION_VALIDATION_REGEX = 2;
    const OPTION_FRIENDLY_NAME    = 3;
    const OPTION_NEXT             = 4;

    static $_options_map = array(
        'plugin_version'                => array( '1.0.35' ),
        'debug_level'                   => array( Katzgrau_Logger::OFF, 'dropdown', '/^[0-8]$/' ),

        'oup_position'                  => array( '1', 'dropdown', '/^[0-7]$/', 'Snap Skout' ),
        'reddit_position'               => array( '2', 'dropdown', '/^[0-7]$/' ),
        'tumblr_position'               => array( '3', 'dropdown', '/^[0-7]$/' ),
        'pinterest_position'            => array( '4', 'dropdown', '/^[0-7]$/', '' ),
        'fb_position'                   => array( '5', 'dropdown', '/^[0-7]$/', 'Facebook' ),
        'twitter_position'              => array( '6', 'dropdown', '/^[0-7]$/' ),
        'gplus_position'                => array( '7', 'dropdown', '/^[0-7]$/', 'Google+' ),

        'posts'                         => array( '1', 'checkbox', '/^[01]$/' ),
        'pages'                         => array( '1', 'checkbox', '/^[01]$/' ),
        'homepage'                      => array( '0', 'checkbox', '/^[01]$/' ),
        'categories'                    => array( '0', 'checkbox', '/^[01]$/' ),
        'tags'                          => array( '0', 'checkbox', '/^[01]$/' ),
        'taxonomies'                    => array( '0', 'checkbox', '/^[01]$/' ),
        'dates'                         => array( '0', 'checkbox', '/^[01]$/' ),
        'authors'                       => array( '0', 'checkbox', '/^[01]$/' ),
        'searches'                      => array( '0', 'checkbox', '/^[01]$/' ),
        'attachments'                   => array( '0', 'checkbox', '/^[01]$/' ),
        'above_post'                    => array( '1', 'checkbox', '/^[01]$/' ),
        'below_post'                    => array( '0', 'checkbox', '/^[01]$/' ),

        'button_style'                  => array( 'skout', 'dropdown', '/default|another_blue|circle|dark_opal|denim|ipe|mac_and_jacks|plastic|safety_corners|sandy_turquoise|skout|slate|south_pole|square/' ),
        'share_new_window'              => array( '1', 'checkbox', '/^[01]$/' ),
        'local_btn_width'               => array( '70', 'text' ),
        'local_btn_height'              => array( '22', 'text' ),
        'local_btn_custom_styles'       => array( '', 'text' ), 
        'local_btn_div_padding'         => array( '', 'text' ), 
        'local_btn_div_rounded_corners' => array( '', 'text' ), 
        'local_btn_border_width'        => array( '', 'text' ), 
        'local_btn_div_border'          => array( '#59625c', 'text' ),
        'local_btn_div_background'      => array( '', 'text' ), 
        'local_btn_padding'             => array( '6', 'text' ),
        'local_btn_font_family'         => array( 'Indie Flower', 'text' ),
        'local_btn_font_size'           => array( '20', 'text' ),
        'local_btn_font_color'          => array( '', 'text' ), 
        'local_btn_font_weight'         => array( '', 'text' ), 
        'local_btn_show_share_count'    => array( '0', 'checkbox', '/^[01]$/' ),
        'local_btn_share_count_css'     => array( '', 'text' ), 
        'local_btn_share_count_style'   => array( 'default', 'text' ),

        'layout_vertical'               => array( '0', 'checkbox', '/^[01]$/' ),
        'oup_server'                    => array( 'api' ),
        'oup_app_id'                    => array( '', 'text' ),

        'fb_app_id'                     => array( '', 'text' ),
        'fb_width'                      => array( '', 'text' ),
        'fb_layout'                     => array( 'button_count', 'dropdown', '/button_count|box_count/', null ), 
        'fb_show_faces'                 => array( '0', 'checkbox', '/^[01]$/' ),
        'fb_action'                     => array( 'like', 'dropdown', '/like|recommend/', null, 'fb_layout' ),
        'fb_font'                       => array( 'arial', 'dropdown', '/arial|lucida grande|segoe ui|tahoma|trebuchet ms|verdana/' ),
        'fb_colorscheme'                => array( 'light', 'dropdown', '/light|dark/', null ), 
        'fb_use_og_tags'                => array( '1', 'checkbox', '/^[01]$/' ),

        'twitter_size'                  => array( 'medium', 'dropdown', '/medium|large/' ),
        'twitter_count'                 => array( 'horizontal', 'dropdown', '/none|horizontal|vertical/' ),

        'gplus_size'                    => array( 'medium', 'dropdown', '/standard|small|medium|tall/' ),
        'gplus_annotation'              => array( 'bubble', 'dropdown', '/none|bubble|inline/', null ),

        'pinterest_count'               => array( 'horizontal', 'dropdown', '/none|vertical|horizontal/' ),

        'css_all'                       => array( '', 'text' ),
        'css_each'                      => array( '', 'text' ),
        'content_filter_priority'       => array( '10', '/^(?:[0-9]|10)$/' ),
        'default_image'                 => array( '', 'text' ), 
        'default_apple_image'           => array( '', 'text' ), 
        'html5'                         => array( '0', 'checkbox', '/^[01]$/' ),
        'box_title'                     => array( '', 'text' ), 
        'box_title_css'                 => array( '', 'text' ),

        'quantcast_acct_pcode'          => array( '', 'text' ),

        'show_blocks'                   => array( '0', 'checkbox', '/^[01]$/' ),
        'send_data'                     => array( '1', 'checkbox', '/^[01]$/' ),
    );

    function __construct() {

        add_action( 'admin_init', 'Oup_WpPlugin_Admin::admin_init' );
        add_action( 'admin_menu', 'Oup_WpPlugin_Admin::add_options_page' );

        add_filter( 'plugin_action_links', 'Oup_WpPlugin_Admin::plugin_action_links', 10, 2 );
    }

    static function admin_init() {
        Oup_WpPlugin_Utility::validate_versions();
        Oup_WpPlugin_Admin::init();
    }

    static function init() {
        register_setting( 'oup_plugin_options', 'oup_options', 'Oup_WpPlugin_Admin::validate_options' );
    }

    static function validate_options( $input ) {
        if ( array_key_exists( 'oup_mem_options', $_POST ) ) {
            foreach ( $_POST['oup_button_order'] as $buttonPos => $buttonName ) {
                $input[$buttonName] = array_key_exists( $buttonName, $_POST['oup_mem_options'] ) ?
                    $buttonPos + 1 : 0;
            }
        }

        $input['oup_position'] = wp_filter_nohtml_kses( $input['oup_position'] );
        $input['oup_app_id']   = trim( wp_filter_nohtml_kses( $input['oup_app_id'] ) );

        $input['oup_server'] = $input['oup_server'] == null ? 'button' :
            trim( wp_filter_nohtml_kses( $input['oup_server'] ) );

        $input['fb_app_id']               = wp_filter_nohtml_kses( $input['fb_app_id'] );
        $input['fb_width']                = wp_filter_nohtml_kses( $input['fb_width'] );
        $input['css_all']                 = wp_filter_nohtml_kses( $input['css_all'] );
        $input['css_each']                = wp_filter_nohtml_kses( $input['css_each'] );
        $input['content_filter_priority'] = wp_filter_nohtml_kses( $input['content_filter_priority'] );
        $input['default_image']           = wp_filter_nohtml_kses( $input['default_image'] );
        $input['default_apple_image']     = wp_filter_nohtml_kses( $input['default_apple_image'] );
        $input['box_title']               = wp_filter_nohtml_kses( $input['box_title'] );
        $input['box_title_css']           = wp_filter_nohtml_kses( $input['box_title_css'] );

        $input = self::calculate_btn_dimensions( $input );

        foreach ( self::$_options_map as $opt => $opt_data ) {
            if ( ! array_key_exists( $opt, $input ) ) {

                if ( $opt_data[self::OPTION_TYPE] == 'checkbox' ) {
                    $input[$opt] = '0';
                }

                elseif ( ! array_key_exists( self::OPTION_TYPE, $opt_data ) ||
                    'dropdown' == $opt_data[self::OPTION_TYPE] || 'text' == $opt_data[self::OPTION_TYPE]
                ) {
                    $input[$opt] = $opt_data[self::OPTION_DEFAULT];
                }
            }
        }
        ksort( $input );
        return $input;
    }

    static function load_options() {
        $options = get_option( 'oup_options' );
        if ( ! is_array( $options ) ) {
            $options = array();
        }

        if ( isset( $options['plugin_version'] ) && ! isset( $options['button_style'] ) ) {
            $options['button_style'] = 'default';
        }

        if ( isset( $options['oup_server'] ) && 'button' == $options['oup_server'] ) {
            $options['oup_server'] = 'api';
        }

        $options = self::calculate_btn_dimensions( $options );

        foreach ( self::$_options_map as $opt => $valMap ) {
            if ( ! array_key_exists( $opt, $options ) ) {
                $options[$opt] = $valMap[self::OPTION_DEFAULT];
            }
        }

        foreach ( $options as $opt => $valMap ) {
            if ( ! array_key_exists( $opt, self::$_options_map ) ) {
                unset( $options[$opt] );
            }
        }

        if ( '' == $options['oup_app_id'] ) {
            $options['oup_app_id'] = self::fetch_publisher_id();
        }

        $options['plugin_version'] = self::$_options_map['plugin_version'][self::OPTION_DEFAULT];

        $options = array_merge( $options, self::detect_custom_post_types() );
        ksort( $options );
        update_option( 'oup_options', $options );
        return $options;
    }

    public static function calculate_btn_dimensions( $options ) {
        $rel_icon_path = 'images/buttons/' . $options['button_style'] . '/twitter.png';
        $icon_file     = str_replace( '\\', '/', dirname( OUP_PLUGIN_FILE ) . '/' . $rel_icon_path );

        if ( file_exists( $icon_file ) ) {
            if ( false != ( $btn_dims = getimagesize( $icon_file ) ) ) {
                $options['local_btn_width']  = $btn_dims[0];
                $options['local_btn_height'] = $btn_dims[1];
            }
        }
        return $options;
    }

    static function publisher_id_request_url() {
        $api_version    = 1;
        $site_uri       = urlencode( get_option( 'siteurl' ) );
        $plugin_version = urlencode( self::$_options_map['plugin_version'][self::OPTION_DEFAULT] );
        $site_name      = urlencode( get_option( 'blogname' ) );
        $ps             = trim( $ps = get_option( 'permalink_structure' ) ) == '' ? 'default' : urlencode( $ps );

        $url = "http://api.snapskout.com/plugin_install/v/{$api_version}/?" .
            "plugin_type=WORDPRESS_SHARE&" .
            "site_uri={$site_uri}&" .
            "plugin_version={$plugin_version}&" .
            "site_name={$site_name}&" .
            "ps={$ps}";

        return $url;
    }

    static function fetch_publisher_id() {

        $wp_http = new WP_Http();
        $result  = $wp_http->get( self::publisher_id_request_url() );

        $retVal = self::$_options_map['oup_app_id'][self::OPTION_DEFAULT];
        if ( array_key_exists( 'body', $result ) && $result['body'] != '' ) {
            $json_resp = json_decode( $result['body'], true );
            if ( array_key_exists( 'publisher_id', $json_resp ) && ! array_key_exists( 'error', $json_resp ) ) {
                $retVal = $json_resp['publisher_id'];
            }
        }
        return $retVal;
    }

    static function sign_up_arguments( $oup_app_id ) {
        $retVal = '';
        if ( null != $oup_app_id && is_string( $oup_app_id ) && trim( $oup_app_id ) != '' &&
            preg_match( '/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $oup_app_id ) == 1
        ) {
            $package = array(
                "site_uri"     => urlencode( get_option( 'siteurl' ) ),
                "publisher_id" => $oup_app_id,
                "user_type"    => 'publisher'
            );
            $retVal  = '&token=' . base64_encode( json_encode( $package ) );
        }
        return $retVal;
    }

    static function add_options_page() {
        add_options_page( 'Snap Skout Options Page',
            'Snap Skout Sharebar + Recommended Offers',
            'manage_options', __FILE__, 'Oup_WpPlugin_Admin::render_admin_form' );
        wp_enqueue_script( 'jquery-ui-sortable' );
    }

    static function delete_plugin_options() {
        delete_option( 'oup_options' );
    }

    static function plugin_action_links( $links, $plugin_file ) {

        if ( strstr( $plugin_file, basename( OUP_PLUGIN_FILE ) ) ) {
            $dp             = dirname( plugin_dir_path( OUP_PLUGIN_FILE ) ) . DIRECTORY_SEPARATOR;
            $rel_admin_link = str_replace( $dp, '', __FILE__ );
            $oup_links      = '<a href="' . get_admin_url() . 'options-general.php?page=' . $rel_admin_link . '">' . __( 'Settings' ) . '</a>';
            array_unshift( $links, $oup_links );
        }

        return $links;
    }

    static function gen_data_entry( $opt, $options ) {
        $ret_val = '';
        if ( 'dropdown' == self::$_options_map[$opt][self::OPTION_TYPE] ) {
            $entry_title = preg_replace( '/^[^_]*_/', '', $opt );
            $ret_val .= "\n{$entry_title}: <select name='oup_options[{$opt}]'>";
            $validate_regex = self::$_options_map[$opt][self::OPTION_VALIDATION_REGEX];
            $choices        = preg_replace( ',^/(.*)/,', '$1', $validate_regex );
            $choices        = preg_split( '/\|/', $choices );
            foreach ( $choices as $choice ) {
                $words = str_replace( '_', ' ', $choice );
                $ret_val .= '
                <option ' . selected( $choice, $options[$opt], false ) . " value='{$choice}'>{$words}</option>\n";
            }
            $ret_val .= "</select>\n";
        }
        return $ret_val;
    }

    static function extra_data_entry( $opt, $options ) {
        $ret_val  = '';
        $opt_data = self::$_options_map[$opt];
        if ( count( $opt_data ) == 5 && array_key_exists( self::OPTION_NEXT, $opt_data ) ) {
            $next_opt = $opt_data[self::OPTION_NEXT];
            $ret_val  = self::gen_data_entry( $next_opt, $options );
            $ret_val .= self::extra_data_entry( $next_opt, $options );
        }
        return $ret_val;
    }

    static function detect_custom_post_types() {
        $args        = array(
            'public'   => true,
            '_builtin' => false
        );
        $post_types  = get_post_types( $args );
        $options_arr = array();
        foreach ( $post_types as $post_type ) {
            $options_arr["custom_type_{$post_type}"] = '0';
        }
        return $options_arr;
    }

    static private function option_2_share_name( $opt ) {
        $ret_val  = '';
        $opt_data = self::$_options_map[$opt];
        if ( count( $opt_data ) >= 4 &&
            array_key_exists( self::OPTION_FRIENDLY_NAME, $opt_data ) &&
            ! empty( $opt_data[self::OPTION_FRIENDLY_NAME] )
        ) {
            $ret_val = $opt_data[self::OPTION_FRIENDLY_NAME];
        }
        else {
            $ret_val = ucwords( str_replace( '_', ' ', preg_replace( '/_position$/', '', $opt ) ) );
        }
        return $ret_val;
    }

    static function sorted_shares( $options ) {
        $sorted_shares      = array();
        $non_display_shares = array();
        foreach ( $options as $opt => $val ) {
            if ( preg_match( '/_position$/', $opt ) > 0 ) {
                if ( (int) $val > 0 ) {
                    $sorted_shares[$opt] = $val;
                }
                else {
                    $non_display_shares[$opt] = $val;
                }
            }
        }
        asort( $sorted_shares );
        $sorted_shares = array_merge( $sorted_shares, $non_display_shares );
        foreach ( $sorted_shares as $opt => $val ) {
            $sorted_shares[$opt] = self::option_2_share_name( $opt );
        }
        return $sorted_shares;
    }

    static function render_admin_form() {
        wp_enqueue_style( 'oup_plugin-admin', plugins_url( '/css/settings_screen.css',
            dirname( dirname( dirname( __FILE__ ) ) ) ) );
        wp_enqueue_script( 'oup_plugin-admin',
            plugins_url( '/scripts/jquery.smartWizard-2.0.js',
                dirname( dirname( dirname( __FILE__ ) ) ) ),
            array( 'jquery' ) );
        $options = get_option( 'oup_options' );
        $step    = 0;

        ?>
        <div class="wrap">
        <div class="icon32" id="icon-options-general"><br></div>
        <h2>Snap Skout Sharebar + Recommended Offers</h2>

        <p>
            <em>A digital concierge that helps connect consumers with the brands they love,<br>through the stories
                they read online.</em></p>

        <form method="post" action="options.php">
        <?php settings_fields( 'oup_plugin_options' ); ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery('#wizard').smartWizard({
                    enableAllSteps  : true,
                    labelFinish     : "SAVE CHANGES",
                    transitionEffect: 'fade',
                    keyNavigation   : false
                });
            });

            jQuery(document).ready(function ($) {
                $('#oup-sortable').sortable();
            });

        </script>
        <div id="wizard" class="swMain">
        <ul>
            <li><a href="#step-1">
                          <span class="stepDesc">
                             SKOUT OPTIONS<br>
                          </span>
                </a></li>
            <li><a href="#step-2">
                          <span class="stepDesc">
                             POSITION OPTIONS<br>
                          </span>
                </a></li>
            <li><a href="#step-3">
                          <span class="stepDesc">
                             DISPLAY OPTIONS<br>
                          </span>
                </a></li>
            <li><a href="#step-4">
                          <span class="stepDesc">
                             CSS OPTIONS<br>
                          </span>
                </a></li>
            <li><a href="#step-5">
                          <span class="stepDesc">
                             ACCOUNT OPTIONS<br>
                          </span>
                </a></li>

            <?php if ( array_key_exists( 'debug', $_GET ) && $_GET['debug'] == 'oup' ) { ?>

                <li><a href="#step-6">
                          <span class="stepDesc">
                             ADDITIONAL OPTIONS<br>
                          </span>
                    </a></li>
            <?php } ?>

        </ul>
        <div id="step-1">
            <h2 class="StepTitle">Step <?php echo ++$step; ?>: Snap Skout Options</h2>
            <table class="form-table">
                <tr>
                    <td scope="colgroup" colspan="2">
                        Important: In order to receive payments you must
                        activate your Publisher ID. Below is your assigned
                        Snap Skout Publisher ID. Take 2 minutes and
                        <a href="https://publisher.tacklocal.com/users/sign_up?selected=publisher<?php echo self::sign_up_arguments( $options['oup_app_id'] ) ?>">
                            activate now
                        </a>.
                        <br><br>
                        The red Skout button in your sharebar matches your
                        stories with offers from around the web. The offers can
                        be related or a direct match to the businesses mentioned
                        in your post. With the Snap Skout button enabled your
                        readers get the best and most relevant offers, at
                        exactly the right place and time. When they click, you
                        generate revenue.
                        [<a href="http://wordpress.org/plugins/snapskout/faq">
                            Visit our FAQ page
                        </a>].
                    </td>
                </tr>
                <?php if ( array_key_exists( 'debug', $_GET ) && $_GET['debug'] == 'oup' ) { ?>
                    <tr>
                        <td>
                            <label for="oup_options[oup_server]">Snap Skout Server</label>
                        </td>
                        <td>
                            <input type="text" style="width: 390px" name="oup_options[oup_server]" value="<?php echo $options['oup_server']; ?>" />
                        </td>
                    </tr>
                <?php
                }
                else {
                    ?>
                    <input type="hidden" name="oup_options[oup_server]" value="<?php echo $options['oup_server']; ?>" />
                <?php
                } ?>
                <tr>
                    <td>
                        <label for="oup_options[oup_app_id]">Snap Skout Publisher ID</label><br>
                    </td>
                    <td>
                        <input type="text" style="width: 390px" name="oup_options[oup_app_id]" value="<?php echo $options['oup_app_id']; ?>" />
                    </td>
                </tr>
                <tr class="oup-tr-border">
                    <td scope="colgroup" colspan="2">
                        We strive to make our plugin better every day. The
                        Wordpress universe has many plugins and themes and
                        your site may be configured with plugins and themes
                        in a unique way which causes our plugin to mis-behave.

                        If you allow us to collect data about the themes
                        you are using as well as your installed plugins,
                        you will be helping us make our plugin better.
                        We share your data with no-one.
                    </td>

                </tr>
                <tr>
                    <td colspan="2">
                        <label><input name='oup_options[send_data]' type='checkbox' value='1'
                                <?php isset( $options['send_data'] ) && checked( '1', $options['send_data'] ); ?>
                                /> Allow plugin to collect site configuration
                        </label>
                    </td>
                </tr>
                <?php if ( array_key_exists( 'debug', $_GET ) && $_GET['debug'] == 'bloks' ) { ?>
                    <tr class="oup-tr-border">
                        <td colspan="2">
                            <label><input name='oup_options[show_blocks]' type='checkbox' value='1'
                                    <?php isset( $options['show_blocks'] ) && checked( '1', $options['show_blocks'] ); ?>
                                    /> Enable Skout Blocks display
                            </label>
                        </td>
                    </tr>
                <?php
                }
                else {
                    ?>
                    <input type="hidden" name="oup_options[show_blocks]" value="<?php echo $options['show_blocks']; ?>" />
                <?php } ?>
            </table>

        </div>
        <!-- div id="step-1" -->
        <div id="step-2">
            <h2 class="StepTitle">Step <?php echo ++$step; ?>: Drag and drop the buttons to select the display order</h2>

            <p class="StepDesc">You can drag and drop the buttons to match the order
                in which you would like them to show up on the Snap Skout sharebar. You can also
                select which buttons show up on the sharebar by using the checkbox on the left.
            </p>
            <ul id="oup-sortable">
                <?php
                $all_shares = self::sorted_shares( $options );
                $img_path = plugins_url( '/images', OUP_PLUGIN_FILE );

                foreach ( $all_shares as $share_id => $share_name ) {
                    $opt_checked      = $options[$share_id] > 0;
                    $opt_checked      = checked( '1', $opt_checked, false );
                    $extra_data_entry = self::extra_data_entry( $share_id, $options );

                    echo "
                                <li>
                                  <img class='admin-ui-icon' src='{$img_path}/up_down_16x16.png' alt='' />
                                  <input name='oup_mem_options[{$share_id}]' type='checkbox' value='1' {$opt_checked} />
                                {$share_name}
                                {$extra_data_entry}
                                  <input type='hidden' name='oup_button_order[]' value='{$share_id}' />
                                </li>
                                ";
                }

                ?>
            </ul>

        </div>
        <!-- div id='step-2' -->
        <div id="step-3">
            <h2 class="StepTitle">Step <?php echo ++$step; ?>: Choose in which content types the buttons display</h2>

            <p class="StepDesc">Use the checkboxes below to indicate where the
                sharebar should be displayed.<br>
                Note: If you select homepage, the sharebar will show in the home page
                only if there are posts on the homepage.
            </p>

            <table class="form-table">
                <tr>
                    <td>
                        <?php
                        $locations = array( 'posts' );
                        foreach ( $locations as $loc ) {
                            ?>
                            <label><input name="oup_options[<?php echo $loc ?>]" type="checkbox" value="1"
                                    <?php isset( $options[$loc] ) && checked( '1', $options[$loc] ); ?> />
                                Display on <?php echo $loc ?></label><br>
                        <?php
                        }
                        ?>
                    </td>
                    <td>
                        <label><input name="oup_options[above_post]" type="checkbox" value="1" <?php if ( isset( $options['above_post'] ) ) {
                                checked( '1', $options['above_post'] );
                            } ?> /> Display above post</label><br>
                        <label><input name="oup_options[below_post]" type="checkbox" value="1" <?php if ( isset( $options['below_post'] ) ) {
                                checked( '1', $options['below_post'] );
                            } ?> /> Display below post</label><br>
                    </td>
                </tr>
                <br>
                <tr style="border-top:#dddddd 1px solid">

                    <td>
                        <?php
                        $locations = array( 'homepage', 'pages', 'categories', 'tags', 'taxonomies' );
                        foreach ( $locations as $loc ) {
                            ?>
                            <label><input name="oup_options[<?php echo $loc ?>]" type="checkbox" value="1"
                                    <?php isset( $options[$loc] ) && checked( '1', $options[$loc] ); ?> />
                                Display on <?php echo $loc ?></label><br>
                        <?php
                        }

                        foreach ( array_keys( Oup_WpPlugin_Admin::detect_custom_post_types() ) as $type ) {
                            ?>
                            <label><input name="oup_options[<?php echo $type; ?>]" type="checkbox"
                                          value="1" <?php if ( isset( $options[$type] ) ) {
                                    checked( '1', $options[$type] );
                                } ?> /> Display on <?php echo str_replace( 'custom_type_', '', $type ); ?> index pages</label>
                            <br>
                        <?php
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        $locations = array( 'authors', 'dates', 'searches', 'attachments' );
                        foreach ( $locations as $loc ) {
                            ?>
                            <label><input name="oup_options[<?php echo $loc ?>]" type="checkbox" value="1"
                                    <?php isset( $options[$loc] ) && checked( '1', $options[$loc] ); ?> />
                                Display on <?php echo $loc ?></label><br>
                        <?php
                        }
                        ?>

                    </td>
                </tr>
                <tr style="border-top:#dddddd 1px solid">

                    <td colspan="2">
                        <label><input name='oup_options[layout_vertical]' type='checkbox' value='1'
                                <?php isset( $options['layout_vertical'] ) && checked( '1', $options['layout_vertical'] ); ?>
                                /> Layout sharebar vertically
                        </label>
                    </td>
                </tr>
                <tr style="border-top:#dddddd 1px solid">

                    <td>
                        <label for="oup_options[button_style]">Button style: </label>
                    </td>
                    <td>
                        <select name="oup_options[button_style]">
                            <option value="default" <?php selected( 'default', $options['button_style'] ); ?>>Default</option>
                            <option value="another_blue" <?php selected( 'another_blue', $options['button_style'] ); ?>>Another Blue</option>
                            <option value="circle" <?php selected( "circle", $options['button_style'] ); ?>>Circle</option>
                            <option value="dark_opal" <?php selected( "dark_opal", $options['button_style'] ); ?>>Dark Opal</option>
                            <option value="denim" <?php selected( "denim", $options['button_style'] ); ?>>Denim</option>
                            <option value="ipe" <?php selected( "ipe", $options['button_style'] ); ?>>Ipe</option>
                            <option value="mac_and_jacks" <?php selected( "mac_and_jacks", $options['button_style'] ); ?>>Mac and Jacks</option>
                            <option value="plastic" <?php selected( "plastic", $options['button_style'] ); ?>>Plastic</option>
                            <option value="safety_corners" <?php selected( "safety_corners", $options['button_style'] ); ?>>Safety Corners</option>
                            <option value="sandy_turquoise" <?php selected( "sandy_turquoise", $options['button_style'] ); ?>>Sandy Turquoise</option>
                            <option value="skout" <?php selected( "skout", $options['button_style'] ); ?>>Skout</option>
                            <option value="slate" <?php selected( "slate", $options['button_style'] ); ?>>Slate</option>
                            <option value="south_pole" <?php selected( 'south_pole', $options['button_style'] ); ?>>South Pole</option>
                            <option value="square" <?php selected( "square", $options['button_style'] ); ?>>Square</option>
                        </select>
                    </td>

                </tr>
                <tr style="border-top:#dddddd 1px solid">

                    <td colspan="2">
                        <label><input name='oup_options[local_btn_show_share_count]' type='checkbox' value='1'
                                <?php isset( $options['local_btn_show_share_count'] ) && checked( '1', $options['local_btn_show_share_count'] ); ?>
                                /> Show share counters
                        </label>
                    </td>
                </tr>
            </table>
        </div>
        <!-- div id="step-3" -->
        <div id="step-4">
            <h2 class="StepTitle">Step <?php echo ++$step; ?>: Custom CSS Options</h2>

            <p class="StepDesc">
                These expert options allow you to perform fine grained optimization to the layout of the
                sharebar. You need knowledge of CSS and time for trial and error to get to the
                presentation you desire.
            </p>

            <table class="form-table">
                <tr class="oup-css-options">
                    <td><label>Optional text for box title.</label></td>
                    <td>
                        <input type="text" size="50" name="oup_options[box_title]" value="<?php echo $options['box_title']; ?>" />
                    </td>
                </tr>
                <tr class="oup-css-options">
                    <td><label> Optional css for box title.</label></td>
                    <td>
                        <input type="text" size="50" name="oup_options[box_title_css]" value="<?php echo $options['box_title_css']; ?>" />
                    </td>
                </tr>
                <tr class="oup-css-options">
                    <td><label>Css styles to apply to the group.</label></td>
                    <td>
                        <input type="text" size="50" name="oup_options[css_all]" value="<?php echo $options['css_all']; ?>" />
                    </td>
                </tr>
                <tr class="oup-css-options">
                    <td>
                        <label>Css styles to apply to each item in the group.</label>
                    </td>
                    <td>
                        <input type="text" size="50" name="oup_options[css_each]" value="<?php echo $options['css_each']; ?>" />
                    </td>
                </tr>
                <tr class="oup-css-options">
                    <td>
                        <label>URL to your favicon. This is also the default image to share if an image can't be found.</label>
                    </td>
                    <td>
                        <input type="text" size="50" name="oup_options[default_image]" value="<?php echo $options['default_image']; ?>" />
                    </td>
                </tr>
                <tr class="oup-css-options">
                    <td><label>URL to your favicon for Apple iDevices.</label>
                    </td>
                    <td>
                        <input type="text" size="50" name="oup_options[default_apple_image]" value="<?php echo $options['default_apple_image']; ?>" />
                    </td>
                </tr>
                <tr class="oup-css-options">
                    <td><label>Content filter priority.</label></td>
                    <td>
                        <input type="text" size="5" name="oup_options[content_filter_priority]" value="<?php echo $options['content_filter_priority']; ?>" />
                    </td>
                </tr>
                <tr class="oup-css-options">
                    <td><label>Use html5</label></td>
                    <td>
                        <input name="oup_options[html5]" type="checkbox" value="1" <?php isset( $options['html5'] ) && checked( '1', $options['html5'] ); ?> />
                    </td>
                </tr>

            </table>
        </div>
        <!-- div id="step-4" -->
        <div id="step-5">
            <h2 class="StepTitle">Step <?php echo ++$step; ?>: Account Options</h2>

            <p class="StepDesc">
                To get optimal results, enter login data for Quantcast and
                Facebook. Read below to learn how and why you should enable
                these services.
            </p>

            <table class="form-table">
                <tr>
                    <th scope="colgroup" colspan="2">
                        <h4>Quantcast Account P-Code</h4>
                        If you want a powerful website statistics capturing and reporting tool
                        that will help you understand your website traffic and how to optimize,
                        then sign-up for the free analytics tool Quantcast.
                        To get a P-Code, you need to first
                        <a href="https://www.quantcast.com/user/signup">create a free account.</a>
                    </th>
                </tr>
                <tr class="oup-account-options">
                    <td>
                        <label for="oup_options[quantcast_acct_pcode]">Quantcast Account P-Code</label><br>
                    </td>
                    <td>
                        <input type="text" style="width: 390px" name="oup_options[quantcast_acct_pcode]" value="<?php echo $options['quantcast_acct_pcode']; ?>" />
                    </td>

                </tr>
                <tr>
                    <th scope="colgroup" colspan="2"><h4>Facebook App ID</h4>
                        If you want to track the number of shared links, likes, comments or the number
                        of times a Facebook account viewed your site content shared on Facebook,
                        you should get a Facebook App ID. Learn more about
                        <a href="https://developers.facebook.com/apps/?action=create">getting a Facebook ID</a>
                        and
                        <a href="https://developers.facebook.com/docs/wordpress/register-facebook-application/">
                            how it works.
                        </a>
                    </th>
                </tr>
                <tr>
                    <td>
                        <label for="oup_options[fb_app_id]">Facebook App ID</label><br>
                    </td>
                    <td>
                        <input type="text" style="width: 390px" name="oup_options[fb_app_id]" value="<?php echo $options['fb_app_id']; ?>" />
                    </td>
                </tr>
                <tr class="oup-account-options">
                    <td><label>Use Open Graph tags</label></td>
                    <td>
                        <input name="oup_options[fb_use_og_tags]" type="checkbox" value="1"
                            <?php isset( $options['fb_use_og_tags'] ) &&
                            checked( '1', $options['fb_use_og_tags'] ); ?>
                            />
                    </td>
                </tr>
            </table>

        </div>
        <!-- div id="step-5" -->

        <?php if ( array_key_exists( 'debug', $_GET ) && $_GET['debug'] == 'oup' ) { ?>

            <div id="step-6">
                <h2 class="StepTitle">Step <?php echo ++$step; ?>: Additional Options</h2>

                <p class="StepDesc">
                </p>

                <table class="form-table">
                    <tr>
                        <th scope="colgroup" colspan="2"><h4>Snap Skout</h4>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <label for="oup_options[debug_level]">Debug Level: </label>
                        </td>
                        <td>
                            <select name="oup_options[debug_level]">
                                <option value="0" <?php selected( 0, $options['debug_level'] ); ?>>Emergency: system is unusable</option>
                                <option value="1" <?php selected( 1, $options['debug_level'] ); ?>>Alert: action must be taken immediately</option>
                                <option value="2" <?php selected( 2, $options['debug_level'] ); ?>>Critical: critical conditions</option>
                                <option value="3" <?php selected( 3, $options['debug_level'] ); ?>>Error: error conditions</option>
                                <option value="4" <?php selected( 4, $options['debug_level'] ); ?>>Warning: warning conditions</option>
                                <option value="5" <?php selected( 5, $options['debug_level'] ); ?>>Notice: normal but significant condition</option>
                                <option value="6" <?php selected( 6, $options['debug_level'] ); ?>>Informational: informational messages</option>
                                <option value="7" <?php selected( 7, $options['debug_level'] ); ?>>Debug: debug messages</option>
                                <option value="8" <?php selected( 8, $options['debug_level'] ); ?>>Debugging is Off</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th scope="colgroup" colspan="2"><h4>Facebook</h4></th>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" size="15" name="oup_options[fb_width]" value="<?php echo $options['fb_width']; ?>" />
                        </td>
                        <td>
                            <label for="oup_options[fb_width]">Width in pixels</label>
                            <em>(standard layout: min=225, default=450; button_count min:90, default:90; box_count min:55 default:55)</em>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input name="oup_options[fb_show_faces]" type="checkbox" value="1" <?php if ( isset( $options['fb_show_faces'] ) ) {
                                checked( '1', $options['fb_show_faces'] );
                            } ?> /><label for="oup_options[fb_show_faces]">Show faces</label>
                        </td>
                        <td><em>(only for standard layout)</em></td>
                    </tr>
                    <tr>
                        <td>
                            <label for="oup_options[fb_layout]">Layout: </label>
                        </td>
                        <td>
                            <select name="oup_options[fb_layout]">
                                <option value="button_count" <?php selected( 'button_count', $options['fb_layout'] ); ?>>Button w/ count</option>
                                <option value="box_count" <?php selected( 'box_count', $options['fb_layout'] ); ?>>Box Count</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="oup_options[fb_action]">Action: </label>
                        </td>
                        <td>
                            <select name="oup_options[fb_action]">
                                <option value="like" <?php selected( 'like', $options['fb_action'] ); ?>>Like</option>
                                <option value="recommend" <?php selected( 'recommend', $options['fb_action'] ); ?>>Recommend</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="oup_options[fb_font]">Font: </label>
                        </td>
                        <td>
                            <select name="oup_options[fb_font]">
                                <option value="arial" <?php selected( 'arial', $options['fb_font'] ); ?>>arial</option>
                                <option value="lucida grande" <?php selected( 'lucida grande', $options['fb_font'] ); ?>>lucida grande</option>
                                <option value="segoe ui" <?php selected( 'segoe ui', $options['fb_font'] ); ?>>segoe ui</option>
                                <option value="tahoma" <?php selected( 'tahoma', $options['fb_font'] ); ?>>tahoma</option>
                                <option value="trebuchet ms" <?php selected( 'trebuchet ms', $options['fb_font'] ); ?>>trebuchet ms</option>
                                <option value="verdana" <?php selected( 'verdana', $options['fb_font'] ); ?>>verdana</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="oup_options[fb_colorscheme]">Color scheme: </label>
                        </td>
                        <td>
                            <select name="oup_options[fb_colorscheme]">
                                <option value="light" <?php selected( 'light', $options['fb_colorscheme'] ); ?>>light</option>
                                <option value="dark" <?php selected( 'dark', $options['fb_colorscheme'] ); ?>>dark</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th scope="colgroup" colspan="2"><h4>Twitter</h4></th>
                    </tr>
                    <tr>
                        <td>
                            <label for="oup_options[twitter_size]">Size: </label>
                        </td>
                        <td>
                            <select name="oup_options[twitter_size]">
                                <option value="medium" <?php selected( 'medium', $options['twitter_size'] ); ?>>medium</option>
                                <option value="large" <?php selected( 'large', $options['twitter_size'] ); ?>>large</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="oup_options[twitter_count]">Count: </label>
                        </td>
                        <td>
                            <select name="oup_options[twitter_count]">
                                <option value="none" <?php selected( 'none', $options['twitter_count'] ); ?>>none</option>
                                <option value="horizontal" <?php selected( 'horizontal', $options['twitter_count'] ); ?>>horizontal</option>
                                <option value="vertical" <?php selected( 'vertical', $options['twitter_count'] ); ?>>vertical</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="colgroup" colspan="2"><h4>Google+</h4></th>
                    </tr>
                    <tr>
                        <td>
                            <label for="oup_options[gplus_size]">Size: </label>
                        </td>
                        <td>
                            <select name="oup_options[gplus_size]">
                                <option value="standard" <?php selected( 'standard', $options['gplus_size'] ); ?>>standard</option>
                                <option value="small" <?php selected( 'small', $options['gplus_size'] ); ?>>small</option>
                                <option value="medium" <?php selected( 'medium', $options['gplus_size'] ); ?>>medium</option>
                                <option value="tall" <?php selected( 'tall', $options['gplus_size'] ); ?>>tall</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="oup_options[gplus_annotation]">Count: </label>
                        </td>
                        <td>
                            <select name="oup_options[gplus_annotation]">
                                <option value="none" <?php selected( 'none', $options['gplus_annotation'] ); ?>>none</option>
                                <option value="bubble" <?php selected( 'bubble', $options['gplus_annotation'] ); ?>>bubble</option>
                                <option value="inline" <?php selected( 'inline', $options['gplus_annotation'] ); ?>>inline</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="colgroup" colspan="2"><h4>Pinterest</h4></th>
                    </tr>
                    <tr>
                        <td>
                            <label for="oup_options[pinterest_count]">Count: </label>
                        </td>
                        <td>
                            <select name="oup_options[pinterest_count]">
                                <option value="none" <?php selected( 'none', $options['pinterest_count'] ); ?>>none</option>
                                <option value="vertical" <?php selected( 'vertical', $options['pinterest_count'] ); ?>>vertical</option>
                                <option value="horizontal" <?php selected( 'horizontal', $options['pinterest_count'] ); ?>>horizontal</option>
                            </select>

                        </td>
                    </tr>
                </table>

            </div> <!-- div id="step-6" -->

        <?php } ?>

        </div>
        <!-- id="wizard" -->
        </form>
        </div> <!-- id="wrap" -->
    <?php
    }
}
