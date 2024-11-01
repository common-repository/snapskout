<?php
/**
 * SnapSkout plugin.
 * User: Pierre Jacomet (TalktUp LLC) pierrej@talktup.com
 *
 * Date: 2014-11
 *
 */


class Oup_WpPlugin_ButtonFb extends Oup_WpPlugin_Button {

    protected function button_specific_content( $options, $the_link, $the_title  ) {
        $content  = '';
        if ( $options['html5'] == '0' ) {
            $content = <<<eof
        <fb:like href="{$the_link}" send="false"
                 layout="{$options['fb_layout']}"
                 width="{$options['fb_width']}"
                 show_faces="{$options['fb_show_faces']}"
                 action="{$options['fb_action']}"
                 colorscheme="{$options['fb_colorscheme']}"
                 font="{$options['fb_font']}">
        </fb:like>
eof;
        }
        else {
            $content = <<<eof
        <div class="fb-like"
             data-href="{$the_link}" data-send="false"
             data-layout="{$options['fb_layout']}"
             data-width="{$options['fb_width']}"
             data-show-faces="{$options['fb_show_faces']}"
             data-action="{$options['fb_action']}"
             data-colorscheme="{$options['fb_colorscheme']}"
             data-font="{$options['fb_font']}">
        </div>
eof;
        }
        return $content;
    }

    protected function button_specific_footer( $options ) {
        return <<<eof
		<!-- xfbml code -->
		<div id='fb-root'></div>
		<script>(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=',{$options['fb_app_id']},'";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
		</script>
eof;

    }

    protected function local_button_content( $options, $the_link, $the_title,
                                             $show_share_count, $a_fixes, $img_fixes,
                                             $count_fixes) {

        $href    = esc_url( "http://www.facebook.com/sharer.php?u={$the_link}" );
        $content = '<a id="local_btn_facebook_share" href="' . $href . '" ';

        $content .= <<<eof
onclick="window.open('{$href}', 'newwindow', 'width=300, height=250, resizable=yes'); return false;"
eof;

        if ( 0 != strlen( $a_fixes ) ) {
            $content .= "style='{$a_fixes}'";
        }
        $content .= ">\n";

        $content .= '<img title="Facebook" class="local_btn" alt="Facebook" src="' .
            OUP_DIR_URL . 'images/buttons/' . $options['button_style'] . '/facebook.png"' .
            ($img_fixes == '' ? '' : ' style="' . $img_fixes . '"') .
            '/>';

        $content .= "\n</a>\n";

        if ($show_share_count) {
            $share_count = $this->get_share_count($the_link);
            if ($share_count > -1) {
                $content .= '<span class="local_btn_sharecount" ' .
                    'style = "' . $count_fixes . '">' .
                    $share_count . '</span>';
            }
        }
        return $content;
    }

    public function get_share_count( $the_link ) {
        $ret_val = 0;
        $result  = wp_remote_get( 'https://api.facebook.com/method/links.getStats?format=json&urls=' . urlencode($the_link) );
        if ( ! is_wp_error( $result ) && 200 == $result['response']['code'] &&
            isset( $result['body'] )
        ) {
            $fb_share_details_html = $result['body'];
            $fb_share_details      = json_decode( $fb_share_details_html, true );
            if ( isset($fb_share_details[0]) && array_key_exists( 'share_count', $fb_share_details[0] ) ) {
                $ret_val = (int) $fb_share_details[0]['share_count'];
            } else {
                Oup_WpPlugin_Utility::flash_add("share_count was not present in fb response of {$the_link}");
                Oup_WpPlugin_Utility::flash_add("Response");
                Oup_WpPlugin_Utility::flash_add($fb_share_details_html);
                $ret_val = -1;
            }

        }
        else if ( is_wp_error( $result ) ) {
            Oup_WpPlugin_Utility::flash_add("There was an error while calling the FB api of {$the_link}");

            Oup_WpPlugin_Utility::flash_add( $result->get_error_message() );
            $ret_val = -2;
        }
        return $ret_val;
    }
}