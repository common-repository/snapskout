<?php
/**
 * SnapSkout plugin.
 * User: Pierre Jacomet (TalktUp LLC) pierrej@talktup.com
 *
 * Date: 2014-11
 *
 */


class Oup_WpPlugin_ContentInjectors {

    static public $_logger = null;

    static private $_buttonList = array();

    static private $_options = null;

    function __construct() {
    }

    function setup_hooks() {

        add_action( 'wp_head', array($this, 'all_heads' ));

        add_filter( 'the_content', 'Oup_WpPlugin_ContentInjectors::oup_content_filter' );
        add_filter( 'the_content', 'Oup_WpPlugin_ContentInjectors::insert_blocks' );
        add_filter( 'language_attributes', 'Oup_WpPlugin_ContentInjectors::filter_html_tag' );
        add_action( 'wp_footer', 'Oup_WpPlugin_ContentInjectors::all_10_footers');
        add_action( 'wp_footer', 'Oup_WpPlugin_ContentInjectors::quantcast_footer', 10000 );
    }

    function all_heads(){
         Oup_WpPlugin_ContentInjectors::initialize();
         Oup_WpPlugin_ContentInjectors::tags_header();
         Oup_WpPlugin_ContentInjectors::get_local_btn_styles();
         Oup_WpPlugin_ContentInjectors::quantcast_header();
         Oup_WpPlugin_ContentInjectors::opengraph_tags();
    }

    static function initialize() {
        self::$_options    = $options = get_option( 'oup_options' );
        self::$_buttonList = array();
        $matches           = null;
        foreach ( $options as $k => $v ) {
            if ( preg_match( '/(\w+)_position/', $k, $matches ) > 0 && $v > 0 ) {
                $class_name = 'Oup_WpPlugin_Button' . Oup_WpPlugin_Utility::underscore_to_camel_case( $matches[1] );

                self::$_buttonList[$v] = new $class_name( $options, self::$_logger );
            }
        }

        ksort( self::$_buttonList );
    }

    static function tags_header( $t = false ) {
        if ( ! is_single() ) {
            return '';
        }
        $options = self::$_options;
        if ( '0' == $options['oup_position'] ) {
            return '';
        }

        global $post;
        $a_tags = get_the_tags( $post->ID );
        $retVal = '';
        if ( is_array( $a_tags ) && count( $a_tags ) > 0 ) {
            $retVal .= "\n" . '<meta name="snapskout" content="';
            foreach ( $a_tags as $tag ) {
                $retVal .= $tag->name . ', ';
            }
            $retVal = substr( $retVal, 0, strlen( $retVal ) - 2 ) . '" />' . "\n";
        }

        if ( $t ) {
            return $retVal;
        }
        else {
            echo $retVal;
        }
    }

    static function quantcast_header() {
        $options = self::$_options;
        if ( strlen( trim( $options['quantcast_acct_pcode'] ) ) != 0 ) {
            echo '
<!-- Quantcast Tag, part 1 -->
<script type="text/javascript">
  var _qevents = _qevents || [];

  (function() {
   var elem = document.createElement(\'script\');

   elem.src = (document.location.protocol == "https:" ? "https://secure" : "http://edge") + ".quantserve.com/quant.js";
   elem.async = true;
   elem.type = "text/javascript";
   var scpt = document.getElementsByTagName(\'script\')[0];
   scpt.parentNode.insertBefore(elem, scpt);
  })();
</script>
<!-- End Quantcast tag -->
            ';
        }
    }

    static function quantcast_footer() {
        $options = self::$_options;
        if ( strlen( trim( $options['quantcast_acct_pcode'] ) ) != 0 ) {
            echo <<<eof
        <!-- Quantcast Tag, part 2 -->
        <script type='text/javascript'>
            _qevents.push({qacct:'{$options['quantcast_acct_pcode']}'});
        </script>
        <noscript>
            <div style='display:none;'>
                <img src='//pixel.quantserve.com/pixel/{$options['quantcast_acct_pcode']}.gif'
                     border='0' height='1' width='1' alt='Quantcast' />
            </div>
        </noscript>
        <!-- End Quantcast tag -->
eof;
        }
    }

    static function opengraph_tags( $t = false ) {
        global $post;

        $retVal = '';

        $options = self::$_options;
        if ( '' == $options['fb_app_id'] || '0' == $options['fb_use_og_tags'] ) {
            if ( $t ) {
                return $retVal;
            }
            else {
                return;
            }
        }

        $retVal .= "\n<!-- Snap Skout open graph tags start -->\n";
        if ( is_single() || is_page() ) { 
            if ( have_posts() ) {
                while ( have_posts() ) {
                    the_post();
                    $retVal .= "\n\t<meta property='og:title' content='" . get_the_title( $post->post_title ) . "' />" .
                        "\n\t<meta property='og:url' content='" . get_permalink() . "' />" .
                        "\n\t<meta property='og:site_name' content='" . get_option( 'blogname' ) . "' />" .
                        "\n\t<meta property='og:description' content='" . self::oup_excerpt_max_charlength( 300 ) . "' />" .
                        "\n\t<meta property='og:type' content='article' />" .
                        "\n\t<meta itemprop='name' content='" . get_the_title( $post->post_title ) . "' />" .
                        "\n\t<meta itemprop='description' content='" . self::oup_excerpt_max_charlength( 300 ) . "' />";
                    $images_array = self::get_images();
                    foreach ( $images_array as $image ) {
                        if ( $image != '' ) {
                            $retVal .= "\n\t<meta property='og:image' content='$image' />";
                        }
                    }

                }
            }
        }
        elseif ( is_home() || is_front_page() ) {
            $retVal .= "\n\t<meta property='og:title' content='" . get_option( 'blogname' ) . "' />" .
                "\n\t<meta property='og:url' content='" . get_option( 'siteurl' ) . "' />" .
                "\n\t<meta property='og:site_name' content='" . get_option( 'blogname' ) . "' />" .
                "\n\t<meta property='og:description' content='" . get_option( 'blogdescription' ) . "' />" .
                "\n\t<meta property='og:type' content='blog' />" .
                "\n\t<meta itemprop='name' content='" . get_option( 'siteurl' ) . "' />" .
                "\n\t<meta itemprop='description' content='" . get_option( 'blogdescription' ) . "' />";

        }
        else {
            $retVal .= "\n\t<meta property='og:title' content='" . get_option( 'blogname' ) . "' />" .
                "\n\t<meta property='og:url' content='" . get_option( 'siteurl' ) . "' />" .
                "\n\t<meta property='og:site_name' content='" . get_option( 'blogname' ) . "' />" .
                "\n\t<meta property='og:description' content='" . get_option( 'blogdescription' ) . "' />" .
                "\n\t<meta property='og:type' content='article' />" .
                "\n\t<meta itemprop='name' content='" . get_option( 'siteurl' ) . "' />" .
                "\n\t<meta itemprop='description' content='" . get_option( 'blogdescription' ) . "' />";
        }

        if ( $options['default_image'] != '' )
            $retVal .= "\n\t<link rel='shortcut icon' href='" . $options['default_image'] . "' />";
        if ( $options['default_apple_image'] != '' )
            $retVal .= "\n\t<link rel='apple-touch-icon' href='" . $options['default_apple_image'] . "' />";

        $retVal .= "\n<!-- Snap Skout open graph tags end -->\n";

        if ( $t ) {
            return $retVal;
        }
        echo $retVal;

    }

    static function get_local_btn_styles( $t = false ) {

        $options = self::$_options;

        if ( 'default' == $options['button_style'] ) {
            if ( $t ) {
                return '';
            }
            else {
                echo '';
                return;
            }
        }

        $local_styles = "\n<!-- Snap Skout local_btn styles -->\n";
        $local_styles .= '<style type="text/css">';

        if ( '' != $options['local_btn_custom_styles'] ) {
            $local_styles .= $options['local_btn_custom_styles'];
        }
        else {
            $local_styles .= '
#local_btn {
  padding: 5px 0px 5px 0px;
  box-sizing: content-box;
  line-height: normal;
    ';
            if ( '' != $options['local_btn_div_padding'] ) {
                $local_styles .= 'padding: ' . $options['local_btn_div_padding'] . "px;\n";
            }

            if ( '' != $options['local_btn_border_width'] ) {
                $local_styles .= 'border: ' . $options['local_btn_border_width'] . 'px solid ' .
                    $options['local_btn_div_border'] . ";\n";
            }

            if ( '' != $options['local_btn_div_background'] ) {
                $local_styles .= 'background-color: ' . $options['local_btn_div_background'] . ";\n";
            }

            if ( 'Y' == $options['local_btn_div_rounded_corners'] ) {
                $local_styles .= '-moz-border-radius: 10px;' .
                    ' -webkit-border-radius: 10px;' .
                    ' -khtml-border-radius: 10px;' .
                    '  border-radius: 10px;' .
                    ' -o-border-radius: 10px;' . "\n";
            }

            $local_styles .= '
}
#local_btn img
{
    width: ' . $options['local_btn_width'] . 'px !important;
    padding: ' . $options['local_btn_padding'] . 'px;
    border:  none !important;
    box-shadow: none !important;
    display: inline;
    vertical-align: middle;
    -moz-box-sizing: content-box;
    -o-box-sizing: content-box;
    -webkit-box-sizing: content-box;
    box-sizing: content-box;
    border-radius: 3px;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    -ms-border-radius: 3px;
    margin: 0px;
    line-height: normal;
    min-width: 0px;
    max-width: 9000px;
    min-height: 0px;
    max-height: 9000px;
    height: ' . $options['local_btn_height'] . 'px;
}

#local_btn div {
  box-sizing: content-box !important;
  -moz-box-sizing: content-box !important;
  -webkit-box-sizing: content-box !important;
  -o-box-sizing: content-box !important;
}

#local_btn, #local_btn a
{
';
            if ( '' == $options['local_btn_div_background'] ) {
                $local_styles .= "  background: none;\n";
            }

            if ( '' != $options['local_btn_font_family'] ) {
                $local_styles .= '  font-family: ' . $options['local_btn_font_family'] . ";\n";
            }

            if ( '' != $options['local_btn_font_size'] ) {
                $local_styles .= '  font-size: ' . $options['local_btn_font_size'] . "px;\n";
            }

            if ( '' != $options['local_btn_font_color'] ) {
                $local_styles .= '  color: ' . $options['local_btn_font_color'] . '!important;' . "\n";
            }

            if ( '' != $options['local_btn_font_weight'] ) {
                $local_styles .= '  font-weight: ' . $options['local_btn_font_weight'] . ";\n";
            }

            $local_styles .= '
            text-decoration: none;
}

#local_btn a:hover {
  text-decoration: none;
}

#local_btn .oup-button > div:first-child {
  padding: 6px;
}

#local_btn iframe {
  min-height:0px;
  max-height: 9000px;
  min-width: 0px;
  max-width: 9000px;
}
';
        }

        if ( '1' == $options['local_btn_show_share_count'] ) {

            if ( '' != $options['local_btn_share_count_css'] ) {
                $local_styles .= $options['local_btn_share_count_css'];
            }
            else {
                $local_styles .= '
.local_btn_sharecount:after, .local_btn_sharecount:before {
    right: 100%;
    border: solid transparent;
    content: " ";
    height: 0;
    width: 0;
    position: absolute;
    pointer-events: none;
}
.local_btn_sharecount:after {
    border-color: rgba(224, 221, 221, 0);
    border-right-color: #f5f5f5;
    border-width: 5px;
    top: 50%;
    margin-top: -5px;
}
.local_btn_sharecount:before {
    border-color: rgba(85, 94, 88, 0);
    border-right-color: #e0dddd;
    border-width: 6px;
    top: 50%;
    margin-top: -6px;
}
.local_btn_sharecount {
    font: 11px Arial, Helvetica, sans-serif;

    padding: 5px;
    -khtml-border-radius: 6px;
    -o-border-radius: 6px;
    -webkit-border-radius: 6px;
    -moz-border-radius: 6px;
    border-radius: 6px;
    position: relative;
    border: 1px solid #e0dddd;
';

                if ( 'default' == $options['local_btn_share_count_style'] ) {
                    $local_styles .= '
  color: #555e58;
  background: #f5f5f5;
}
.local_btn_sharecount:after {
    border-right-color: #f5f5f5;
}
';
                }
                elseif ( 'white' == $options['local_btn_share_count_style'] ) {
                    $local_styles .= '
  color: #555e58;
  background: #ffffff;
}
.local_btn_sharecount:after {
  border-right-color: #ffffff;
}
';
                }
                elseif ( 'blue' == $options['local_btn_share_count_style'] ) {
                    $local_styles .= '
  color: #ffffff;
  background: #42a7e2;
}
.local_btn_sharecount:after {
  border-right-color: #42a7e2;
}
';
                } 

            } 

        } 

        $local_styles .= "\n</style>\n";

        if ( $t ) {
            return $local_styles;
        }
        else {
            echo $local_styles;
        }
    }

    static function oup_excerpt_max_charlength( $charlength ) {
        $content      = get_the_content(); 
        $content      = strip_tags( $content ); 
        $quotes       = array( '/"/', "/'/" );
        $replacements = array( '&quot;', '&#39;' );
        $content      = preg_replace( $quotes, $replacements, $content );
        $regex        = "#([[]caption)(.*)([[]/caption[]])#e"; 
        $content      = preg_replace( $regex, '', $content ); 
        $content      = preg_replace( '/\r\n/', ' ', trim( $content ) ); 

        $excerpt = $content;
        $charlength ++;
        if ( strlen( $excerpt ) > $charlength ) {
            $subex   = substr( $excerpt, 0, $charlength - 5 );
            $exwords = explode( ' ', $subex );
            $excut   = - ( strlen( $exwords[count( $exwords ) - 1] ) );
            if ( $excut < 0 ) {
                return substr( $subex, 0, $excut ) . '...';
            }
            else {
                return $subex . '...';
            }
        }
        else {
            return $excerpt;
        }
    }

    static function get_images() {
        global $post, $posts;
        global $current_blog;
        $options = self::$_options;

        $the_images = array();

        $args = array(
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'numberposts'    => - 1,
            'order'          => 'ASC',
            'post_status'    => null,

        );

        $attachments = get_posts( $args );

        if ( $attachments ) {
            for ( $i = 0, $size = sizeof( $attachments ); $i < $size; ++$i ) {
                $the_images[$i] = wp_get_attachment_url( $attachments[$i]->ID );

                if ( substr( $the_images[$i], 0, 1 ) == '/' ) {
                    $the_images[$i] = get_option( 'siteurl' ) . $the_images[$i]; 
                }
            }
        }
        else {

            if ( '' != $options['default_image'] ) {
                $the_images[0] = $options['default_image']; 
            }
        }
        return $the_images;
    }

    static function filter_html_tag() {
        $options = self::$_options;
        if ( '0' == $options['html5'] ) {
            echo ' xmlns:fb="http://ogp.me/ns/fb#" ';
        }

        if ( is_single() || is_page() ) {
            echo ' itemscope itemtype="http://schema.org/Article" ';
        }
        else {
            echo ' itemscope itemtype="http://schema.org/Blog" ';
        }
    }

    static function all_10_footers() {
        Oup_WpPlugin_ContentInjectors::wp_footer();
        if (isset($_GET['debug']) && 'oup' == $_GET['debug']) {
            Oup_WpPlugin_Utility::flash_add(Oup_WpPlugin_Utility::plugins_as_text());
            Oup_WpPlugin_Utility::flash_as_html_comment();
        }
    }

    static function wp_footer() {
        $ret_val = '
        <!-- Snap Skout footer snippets start -->';

        foreach ( self::$_buttonList as $button ) {
            $ret_val .= $button->footer_snippet();
        }
        $ret_val .= '
        <!-- Snap Skout footer snippets end -->
';

        echo $ret_val;
    }

    static function oup_content( $atts = array() ) {
        $options     = self::$_options;
        $oup_content = "\n<!-- Snap Skout plugin version: {$options['plugin_version']} injection Start -->\n";

        $vertical_layout = '';
        if ( '1' == $options['layout_vertical'] ) {
            $w = (int) $options['local_btn_width'] +
                ( $options['local_btn_show_share_count'] == '1' ? 60 : 30 );
            $vertical_layout = "float:left; width: {$w}px; padding: 10px; margin-right: 20px;";
        }

        $oup_box_style = $vertical_layout . $options['css_all'];
        $oup_box_style = strlen( trim( $oup_box_style ) ) > 0 ?
            ( ' style="' . $oup_box_style . '"' ) :
            '';

        $oup_content .= '<div class="oup-box" ' . $oup_box_style . '>';
        $box_title_style = strlen( $options['box_title_css'] ) ? ( ' style="' . $options['box_title_css'] . '"' ) : '';

        $oup_content .= '<div class="oup-title" ' . $box_title_style . '>' . $options['box_title'] . '</div>';
        $oup_content .= '<div id="local_btn">';

        $the_link  = strip_tags( get_permalink() );
        $the_title = strip_tags( get_the_title() );

        foreach ( self::$_buttonList as $button ) {
            $oup_content .= $button->content_snippet( $the_link, $the_title );
        }

        $oup_content .= '<br style="clear:left;"/>' . "\n";
        $oup_content .= '</div> <!-- id="local_btn" -->';
        $oup_content .= '</div> <!-- class="oup-box" -->';

        $oup_content .= "\n<!-- Snap Skout plugin injection End -->\n";

        return $oup_content;
    }

    static public function insert_blocks($content, $t = false)
    {
        $options     = self::$_options;
        $ss_id = $options['oup_app_id'];
        $oup_server = $options['oup_server'] ? $options['oup_server'] : 'api';

        if (36 == strlen($ss_id)) {
            if ( '1' == $options['show_blocks'] && ( is_single() || is_page() ) ) {
                $content .= <<<eof
<!-- Skoutblocks start -->
<div class="ss-skout-blocks"></div> <!-- end class="ss-skout-blocks" -->
<script language="javascript" src="http://{$oup_server}.snapskout.com/i/{$ss_id}.js?blox=true" defer="defer"></script>
<!-- Skoutblocks end -->

eof;
            }
        }
        return $content;
    }

    static function oup_content_filter( $content, $force = false ) {
        if ( ! self::should_share_bar_show( $force ) ) {
            return $content;
        }

        $options = self::$_options;
        $content = <<<eof
<div class='oup-content'>
{$content}
</div> <!-- class='oup-content' -->

eof;

        $oup_sharebar = self::oup_content();
        if ( 1 == $options['above_post'] ) {
            $content = $oup_sharebar . $content;
        }
        if ( 1 == $options['below_post'] ) {
            $content .= $oup_sharebar;
        }

        return $content;
    }

    static function should_share_bar_show( $t = false ) {
        $options = self::$_options;

        if ( $t || ( $options['posts'] && is_single() ) ||
            ( $options['pages'] && is_page() )
        ) {
            return true;
        }

        if ( $options['homepage'] && is_home() ) {
            $th         = wp_get_theme();
            $theme_name = strtolower( $th->get( 'Name' ) );

            $retVal = ! ( 'attitude' == $theme_name || 'customizr' == $theme_name ||
                'destro' == $theme_name || 'expound' == $theme_name ||
                'hueman' == $theme_name || 'swift basic' == $theme_name );

            return $retVal;
        }

        if ( ( $options['categories'] && is_category() ) ||
            ( $options['tags'] && is_tag() ) ||
            ( $options['taxonomies'] && is_tax() ) ||
            ( $options['dates'] && is_date() ) ||
            ( $options['authors'] && is_author() ) ||
            ( $options['searches'] && is_search() ) ||
            ( $options['attachments'] && is_attachment() )
        ) {
            return true;
        }

        $is_custom_post_type = false;
        foreach ( array_keys( Oup_WpPlugin_Admin::detect_custom_post_types() ) as $type ) {
            if ( $options[$type] && is_post_type_archive( str_replace( 'custom_type_', '', $type ) ) ) {
                $is_custom_post_type = true;
            }
        }

        if ( $is_custom_post_type ) {
            return true;
        }

        return false;
    }
}
