<?php
/**
 * SnapSkout plugin.
 * User: Pierre Jacomet (TalktUp LLC) pierrej@talktup.com
 *
 * Date: 2014-11
 *
 */


class Oup_WpPlugin_ButtonTwitter extends Oup_WpPlugin_Button {
    protected function button_specific_content( $options, $the_link, $the_title ) {
        return <<<eof
      <a href="https://twitter.com/share"
         class="twitter-share-button"
         data-url="{$the_link}" data-text="{$the_title}"
         data-count="{$options['twitter_count']}"
         data-size="{$options['twitter_size']}">
         Tweet
      </a>
eof;
    }

    protected function button_specific_footer( $options ) {
        return <<<eof
		<script>
		!function(d,s,id){
			var js,fjs=d.getElementsByTagName(s)[0];
			if(!d.getElementById(id)){
				js=d.createElement(s);
				js.id=id;js.src="//platform.twitter.com/widgets.js";
				fjs.parentNode.insertBefore(js,fjs);
			}
		}(document,"script","twitter-wjs");
		</script>
eof;
    }

    protected function local_button_content( $options, $the_link, $the_title,
                                             $show_share_count, $a_fixes, $img_fixes,
                                             $count_fixes) {

        $twitterShareText = urlencode( html_entity_decode( $the_title . ' ' . '', ENT_COMPAT, 'UTF-8' ) );

        $href    = esc_url( "http://twitter.com/share?url={$the_link}&text={$the_title}" );
        $content = '<a id="local_btn_twitter_share" href="' . $href . '" ';

        $content .= <<<eof
onclick="window.open('{$href}', 'newwindow', 'width=500, height=400, resizable=yes'); return false;"
eof;

        if ( 0 != strlen( $a_fixes ) ) {
            $content .= "style='{$a_fixes}'";
        }
        $content .= ">\n";

        $content .= '<img title="Twitter" class="local_btn" alt="Twitter" src="' .
            OUP_DIR_URL . 'images/buttons/' . $options['button_style'] . '/twitter.png"' .
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
        $retVal = 0;
        $result = file_get_contents('http://cdn.api.twitter.com/1/urls/count.json?url=' . $the_link);
        if (false !== $result) {
            $share_details = json_decode($result, true);
            if (isset($share_details['count'])) {
                $retVal = (int) $share_details['count'];
            }
        } else {
            Oup_WpPlugin_Utility::flash_add("An error occurred while fetching the Twitter counter for {$the_link}");
            $retVal = -1;
        }
        return $retVal;
    }
}