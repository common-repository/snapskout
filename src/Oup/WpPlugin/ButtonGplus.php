<?php
/**
 * SnapSkout plugin.
 * User: Pierre Jacomet (TalktUp LLC) pierrej@talktup.com
 *
 * Date: 2014-11
 *
 */


class Oup_WpPlugin_ButtonGplus extends Oup_WpPlugin_Button {
    protected function button_specific_content( $options, $the_link, $the_title ) {
        $content  = '';
        if ( '0' == $options['html5'] ) {
            $content = <<<eof
        <g:plusone size="{$options['gplus_size']}"
                   annotation="{$options['gplus_annotation']}"
                   href="{$the_link}">
        </g:plusone>
eof;

        }
        else {
            $content = <<<eof
        <div class="g-plusone" data-size="{$options['gplus_size']}"
             data-annotation= "{$options['gplus_annotation']}"
             data-href="{$the_link}">
        </div>
eof;
        }

        return $content;
    }

    protected function button_specific_footer( $options ) {
        return <<<eof
		<!-- google +1 code -->
		<script type='text/javascript'>
		(function() {
			var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
			po.src = 'https://apis.google.com/js/plusone.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
		})();
		</script>
eof;

    }

    protected function local_button_content( $options, $the_link, $the_title,
                                             $show_share_count, $a_fixes, $img_fixes,
                                             $count_fixes) {
        $href    = esc_url( "https://plus.google.com/share?url={$the_link}" );
        $content = '<a id="local_btn_google_share" href="' . $href . '" ';

        $content .= <<<eof
onclick="window.open('{$href}', 'newwindow', 'width=500, height=400, resizable=yes'); return false;"
eof;

        if ( 0 != strlen( $a_fixes ) ) {
            $content .= "style='{$a_fixes}'";
        }
        $content .= ">\n";

        $content .= '<img title="Google+" class="local_btn" alt="Google+" src="' .
            OUP_DIR_URL . 'images/buttons/' . $options['button_style'] .  '/google.png"' .
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
        $args = array(
            'method' => 'POST',
            'headers' => array(
                'Content-Type' => 'application/json'
            ),

            'body' => json_encode(array(
                'method' => 'pos.plusones.get',
                'id' => 'p',
                'method' => 'pos.plusones.get',
                'jsonrpc' => '2.0',
                'key' => 'p',
                'apiVersion' => 'v1',
                'params' => array(
                    'nolog'=>true,
                    'id'=> $the_link,
                    'source'=>'widget',
                    'userId'=>'@viewer',
                    'groupId'=>'@self'
                )
            )),
            'sslverify'=>false
        );

        $response = wp_remote_post("https://clients6.google.com/rpc", $args);

        if (is_wp_error($response)){
            Oup_WpPlugin_Utility::flash_add("API post to google failed for {$the_link}");

            Oup_WpPlugin_Utility::flash_add( $response->get_error_message() );
            return -1;
        } else {
            $json = json_decode($response['body'], true);
            return intval( $json['result']['metadata']['globalCounts']['count'] );
        }
    }
}