<?php
/**
 * SnapSkout plugin.
 * User: Pierre Jacomet (TalktUp LLC) pierrej@talktup.com
 *
 * Date: 2014-11
 *
 */


abstract class Oup_WpPlugin_Button {

    private $_options = array();

    private $_logger = null;

    private $_opt_base_name = '';

    protected $_base_styles = 'border:0px; vertical-align: middle; margin: 0px; background: none; cursor: pointer;float: left;';

    function __construct( array $options, Katzgrau_Logger $logger = null) {
        global $is_safari;
        $this->_options       = $options;
        $this->_logger        = $logger;
        $descendant           = get_class( $this );
        $this->_opt_base_name = strtolower( preg_replace( '/^.*Button/', '', $descendant ) );
        $this->_base_styles .= esc_attr( "height: {$options['local_btn_height']}x; " );

        if ((! $this instanceof Oup_WpPlugin_ButtonOup) && $is_safari &&
            'default' != $options['button_style']) {
            $this->_base_styles .= 'width: 112px;';
        }
        if ( '1' == $options['layout_vertical'] ) {
            $this->_base_styles .= 'padding: 0px 0px 17px 0px;';
        } else {
            if ( 'skout' == $options['button_style'] ) {

                $this->_base_styles .= 'padding: 2px 1px 2px 1px;';
            } else {
                $this->_base_styles .= 'padding: 0px 1px 0px 1px;';
            }
        }
    }

    public function base_name() {
        return $this->_opt_base_name;
    }

    abstract protected function button_specific_content( $options, $the_link, $the_title );

    abstract protected function local_button_content( $options, $the_link, $the_title,
                                                      $show_share_count, $a_fixes,
                                                      $img_fixes, $count_fixes);

    abstract public function get_share_count($the_link);

    function content_snippet( $the_link, $the_title ) {
        if ( $this->_options['button_style'] == 'default' ||
            $this instanceof Oup_WpPlugin_ButtonOup) {
            $button_specific_content = $this->button_specific_content( $this->_options, $the_link, $the_title );
        } else {

            $dynamic_fixes = $this->dynamic_a_fixes();
            $img_fixes = $this->dynamic_img_fixes();
            $count_fixes = $this->dynamic_count_fixes();
            $button_specific_content = $this->local_button_content( $this->_options, $the_link, $the_title,
                $this->_options['local_btn_show_share_count'] == '1', $dynamic_fixes,
                $img_fixes, $count_fixes);
        }

        $content = <<<eof
<!-- start {$this->_opt_base_name} button code -->
<div class='oup-button-group oup-{$this->_opt_base_name}'
     style='{$this->_base_styles};{$this->_options['css_each']}'>
    <div class='{$this->_opt_base_name}-button'>
{$button_specific_content}
    </div>
</div>
<!-- end {$this->_opt_base_name} button code -->

eof;

        return "\n" . $content . "\n";
    }

    private function dynamic_a_fixes() {
        $fixes = '';
        return $fixes;
    }

    private function dynamic_img_fixes() {
        global $is_gecko, $is_trident,$is_winIE, $is_macIE, $is_opera, $is_safari, $is_chrome;

        $fixes = '';
        return $fixes;
    }

    private function dynamic_count_fixes() {
        global $is_gecko, $is_trident,$is_winIE, $is_macIE, $is_opera, $is_safari, $is_chrome;

        $fixes = '';
        return $fixes;
    }

    function footer_snippet() {
        $footer_snippet = '';
        if ( 'default' == $this->_options['button_style'] ) {
            $footer_snippet .= "<!-- start {$this->_opt_base_name} footer snippet -->\n";
            $footer_snippet .= $this->button_specific_footer( $this->_options );
            $footer_snippet .= "\n<!-- end {$this->_opt_base_name} footer snippet -->\n";

        }
        else {

        }
        return $footer_snippet;
    }

    protected function button_specific_footer( $options ) {
        return '';
    }
}
