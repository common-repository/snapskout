<?php
/**
 * SnapSkout plugin.
 * User: Pierre Jacomet (TalktUp LLC) pierrej@talktup.com
 *
 * Date: 2014-11
 *
 */


class Oup_WpPlugin_ButtonReddit extends Oup_WpPlugin_Button {

    protected function button_specific_content( $options, $the_link, $the_title ) {
        return <<<eof
        <a href="http://www.reddit.com/submit"
           onclick="window.location = 'http://www.reddit.com/submit?url=' + encodeURIComponent(window.location); return false">
            <img style='{$this->_base_styles}'
                 src="http://www.reddit.com/static/spreddit7.gif" alt="submit to reddit" border="0" height="20px" />
        </a>
eof;
    }

    protected function local_button_content( $options, $the_link, $the_title,
                                             $show_share_count, $a_fixes, $img_fixes,
                                             $count_fixes) {

        $href    = esc_url( "http://reddit.com/submit?url={$the_link}&title={$the_title}" );
        $content = '<a id="local_btn_reddit_share" href="' . $href . '" ';

        $content .= <<<eof
onclick="window.open('{$href}', 'newwindow', 'width=500, height=450, resizable=yes'); return false;"
eof;

        if ( 0 != strlen( $a_fixes ) ) {
            $content .= "style='{$a_fixes}'";
        }
        $content .= ">\n";

        $content .= '<img title="Reddit" class="local_btn" alt="Reddit" src="'  .
            OUP_DIR_URL . 'images/buttons/' . $options['button_style'] . '/reddit.png"' .
            ( '' == $img_fixes ? '' : ' style="' . $img_fixes . '"' ) .
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
        $retVal = 0;
        $result = file_get_contents('http://buttons.reddit.com/button_info.json?url=' . urlencode($the_link));
        if (false !== $result) {
            $link_data = json_decode($result, true);
            if (isset($link_data['data']['children'])) {
                if (isset($link_data['data']['children']['0']['data']['score'])) {
                    $retVal = (int) $link_data['data']['children']['0']['data']['score'];
                }
            } else {
                Oup_WpPlugin_Utility::flash_add('children node was not set in Reddit API response');
                Oup_WpPlugin_Utility::flash_add("Response");
                Oup_WpPlugin_Utility::flash_add($result);
            }
        } else {
            Oup_WpPlugin_Utility::flash_add("An error occurred while fetching the Reddit counter for {$the_link}");
            $retVal = -1;
        }
        return $retVal;
    }
}