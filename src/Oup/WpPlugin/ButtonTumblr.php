<?php
/**
 * SnapSkout plugin.
 * User: Pierre Jacomet (TalktUp LLC) pierrej@talktup.com
 *
 * Date: 2014-11
 *
 */


class Oup_WpPlugin_ButtonTumblr extends Oup_WpPlugin_Button {
    protected function button_specific_content( $options,  $the_link, $the_title ) {
        return <<<eof
        <a href="http://www.tumblr.com/share" title="Share on Tumblr"
           style="display:inline-block; text-indent:-9999px; overflow:hidden; width:61px; height:20px; background:url('http://platform.tumblr.com/v1/share_2.png') top left no-repeat transparent;">
            Share on Tumblr
        </a>
eof;
    }

    protected function button_specific_footer( $options ) {
        return <<<eof
        <!-- tumblr code -->
        <script src='http://platform.tumblr.com/v1/share.js'></script>
eof;
    }

    protected function local_button_content( $options, $the_link, $the_title,
                                             $show_share_count, $a_fixes, $img_fixes,
                                             $count_fixes) {

        if ( preg_match( '[http://]', $the_link ) ) {
            $the_link = str_replace( 'http://', '', $the_link );
        }
        else if ( preg_match( '[https://]', $the_link ) ) { 
            $the_link = str_replace( 'https://', '', $the_link );
        }

        $the_link = str_replace( "http://", '', $the_link );
        $href     = esc_url( "http://www.tumblr.com/share/link?url={$the_link}&name={$the_title}" );
        $content  = '<a id="local_btn_tumblr_share" href="' . $href . '" ';

        $content .= <<<eof
onclick="window.open('{$href}', 'newwindow', 'width=500, height=400, resizable=yes'); return false;"
eof;

        if ( 0 != strlen( $a_fixes ) ) {
            $content .= "style='{$a_fixes}'";
        }
        $content .= ">\n";

        $content .= '<img title="tumblr" class="local_btn" alt="tumblr" src="' .
            OUP_DIR_URL . 'images/buttons/' . $options['button_style'] . '/tumblr.png"' .
            ($img_fixes == '' ? '' : ' style="' . $img_fixes . '"') .
            '/>';

        $content .= "\n</a>\n";

        return $content;
    }

    public function get_share_count( $the_link ) {
        return 0;
    }
}