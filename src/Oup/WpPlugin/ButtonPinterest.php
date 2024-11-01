<?php
/**
 * SnapSkout plugin.
 * User: Pierre Jacomet (TalktUp LLC) pierrej@talktup.com
 *
 * Date: 2014-11
 *
 */


class Oup_WpPlugin_ButtonPinterest extends Oup_WpPlugin_Button {
    protected function button_specific_content( $options, $the_link, $the_title ) {

        $image_array = Oup_WpPlugin_ContentInjectors::get_images();
        $image_array = ( array_key_exists( 0, $image_array ) ? $image_array[0] : '' );

        return <<<eof
			<a href="http://pinterest.com/pin/create/button/?url={$the_link}&amp;media={$image_array}&amp;description={$the_title}"
			   class="pin-it-button" count-layout="{$options['pinterest_count']}">
				Pin It
			</a>
eof;
    }

    protected function button_specific_footer( $options ) {
        return <<<eof
        <!-- pinterest code -->
        <script type='text/javascript'>
        (function() {
            window.PinIt = window.PinIt || { loaded:false };
            if (window.PinIt.loaded) return;
            window.PinIt.loaded = true;
            function async_load(){
                var s = document.createElement('script');
                s.type = 'text/javascript';
                s.async = true;
                s.src = 'http://assets.pinterest.com/js/pinit.js';
                var x = document.getElementsByTagName('script')[0];
                x.parentNode.insertBefore(s, x);
            }
            if (window.attachEvent)
                window.attachEvent('onload', async_load);
            else
                window.addEventListener('load', async_load, false);
        })();
        </script>
eof;
    }

    protected function local_button_content( $options, $the_link, $the_title,
                                             $show_share_count, $a_fixes, $img_fixes,
                                             $count_fixes ) {
        $content = "<a id='local_btn_pinterest_share' " .
            " href='javascript:void((function()%7Bvar%20e=document.createElement(&apos;script&apos;);e.setAttribute(&apos;type&apos;,&apos;text/javascript&apos;);e.setAttribute(&apos;charset&apos;,&apos;UTF-8&apos;);e.setAttribute(&apos;src&apos;,&apos;http://assets.pinterest.com/js/pinmarklet.js?r=&apos;+Math.random()*99999999);document.body.appendChild(e)%7D)());' ";

        if ( 0 != strlen( $a_fixes ) ) {
            $content .= "style='{$a_fixes}'";
        }
        $content .= ">\n";

        $content .= '<img title="Pinterest" class="local_btn" alt="Pinterest" src="' .
            OUP_DIR_URL . 'images/buttons/' . $options['button_style'] . '/pinterest.png"' .
            ( '' == $img_fixes ? '' : ' style="' . $img_fixes . '"' ) .
            '/>';

        $content .= "\n</a>\n";

        if ( $show_share_count ) {
            $share_count = $this->get_share_count( $the_link );
            if ( $share_count > - 1 ) {
                $content .= '<span class="local_btn_sharecount" ' .
                    'style = "' . $count_fixes . '">' .
                    $share_count . '</span>';
            }
        }
        return $content;
    }

    public function get_share_count( $the_link ) {
        $retVal = 0;
        $result = file_get_contents( 'http://api.pinterest.com/v1/urls/count.json?url=' . $the_link );
        if ( false !== $result ) {
            $result = preg_replace( "/receiveCount.*({.*}).*/", '$1', $result );
            $share_details = json_decode( $result, true );
            if ( is_array( $share_details ) && array_key_exists( 'count', $share_details ) ) {
                $retVal = (int) $share_details['count'];
            }
        }
        else {
            Oup_WpPlugin_Utility::flash_add( "An error occurred while fetching the Pinterest counter for {$the_link}" );
            $retVal = - 1;
        }
        return $retVal;
    }
}