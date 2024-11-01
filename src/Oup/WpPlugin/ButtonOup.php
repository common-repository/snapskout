<?php
/**
 * SnapSkout plugin.
 * User: Pierre Jacomet (TalktUp LLC) pierrej@talktup.com
 *
 * Date: 2014-11
 *
 */


class Oup_WpPlugin_ButtonOup extends Oup_WpPlugin_Button {
    protected function button_specific_content( $options, $the_link, $the_title ) {
        $oup_server = $options['oup_server'] ? $options['oup_server'] : 'api';

        $ret = '';
        if ($options['oup_app_id'] && strlen($options['oup_app_id']) > 0) {
            $rel_icon_path = 'images/buttons/' . $options['button_style'] . '/snapskout.png';
            $icon_file = str_replace('\\', '/', dirname(OUP_PLUGIN_FILE) . '/' . $rel_icon_path);
            $args = 'default' == $options['button_style'] || ! file_exists($icon_file) ?
                array('target_article_url' => $the_link) :
                array('target_article_url' => $the_link,
                    'icon_url' => OUP_DIR_URL . $rel_icon_path,
                    'icon_width' => $options['local_btn_width'],
                    'icon_height' => $options['local_btn_height']);

            $query = http_build_query($args);
            $ret = <<<eof
        <div class="btn-offerdup">
            <script language='javascript'
                    src='http://{$oup_server}.snapskout.com/i/{$options['oup_app_id']}.js?{$query}'
                    >
            </script>
        </div>
eof;

        } else {
            $ret = <<<eof
        <!-- SnapSkout button is selected, but no SnapSkout Publisher ID entered -->
eof;

        }
        return $ret;
    }

    protected function local_button_content( $options, $the_link, $the_title,
                                             $show_share_count, $a_fixes, $img_fixes,
                                             $count_fixes) {
        return $this->button_specific_content($options, $the_link, $the_title);
    }

    public function get_share_count( $the_link ) {
        return 0;
    }
}