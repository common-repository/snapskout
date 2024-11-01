<?php
/**
 * SnapSkout plugin.
 * User: Pierre Jacomet (TalktUp LLC) pierrej@talktup.com
 *
 * Date: 2014-11
 *
 */


class Oup_WpPlugin_Utility {

    static private $_flash = array();

    static public function flash_add( $msg ) {
        if ( ! ( isset( $_GET['debug'] ) && 'oup' == $_GET['debug'] ) ) {
            return;
        }
        self::$_flash[] = $msg;
    }

    static public function flash_get() {
        $retVal       = self::$_flash;
        self::$_flash = array();
        return $retVal;
    }

    static public function flash_as_html_comment( $t = false ) {
        if ( ! ( isset( $_GET['debug'] ) && 'oup' == $_GET['debug'] ) ) {
            return '';
        }

        $flash  = self::flash_get();
        $retVal = '';
        if ( count( $flash ) > 0 ) {
            $retVal .= "\n<!-- Snap Skout flash start \n";
            foreach ( $flash as $f ) {
                $retVal .= "{$f}\n";
            }
            $retVal .= "\n Snap Skout flash end -->\n";
        }

        if ( $t ) {
            return $retVal;
        }
        echo $retVal;
    }

    static public function validate_versions() {
        global $wp_version;

        $plugin      = plugin_basename( OUP_PLUGIN_FILE );
        $plugin_data = get_plugin_data( OUP_PLUGIN_FILE, false );

        if ( is_plugin_active( $plugin ) ) {
            if ( version_compare( $wp_version, '3.4', '<' ) ) {
                deactivate_plugins( $plugin );
                wp_die( "'" . $plugin_data['Name'] . "' requires WordPress 3.4 or higher, and has been deactivated! Please upgrade WordPress and try again.<br /><br />Back to <a href='" . admin_url() . "'>WordPress admin</a>." );
            }
            if ( version_compare( PHP_VERSION, '5.2', '<' ) ) {
                deactivate_plugins( $plugin );
                wp_die( "'" . $plugin_data['Name'] . "' requires PHP 5.2 or higher, and has been deactivated! Please upgrade PHP and try again.<br /><br />Back to <a href='" . admin_url() . "'>WordPress admin</a>." );
            }
        }
    }

    static public function get_logging_level() {
        $options   = get_option( 'oup_options' );
        $log_level = Katzgrau_Logger::OFF;
        if ( $options && array_key_exists( 'debug_level', $options ) ) {
            $log_level = (int) $options['debug_level'];
        }
        return $log_level;
    }

    static public function underscore_to_camel_case( $s ) {
        return str_replace( ' ', '', ucwords( str_replace( '_', ' ', $s ) ) );
    }

    public static function assemble_plugins() {
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }
        $retVal = array();
        foreach ( get_plugins() as $k => $v ) {
            $retVal[$k] = array(
                'Name'      => $v['Name'],
                'PluginURI' => $v['PluginURI'],
                'Version'   => $v['Version'],
                'AuthorURI' => $v['AuthorURI'],
                'Author'    => $v['Author'],
                'Active'    => is_plugin_active( $k )
            );
        }
        return $retVal;
    }

    public static function plugins_as_text() {
        $retVal = "\nPlugins list:\n\n";
        foreach ( self::assemble_plugins() as $k => $v ) {
            $retVal .= "$k: \n";
            $retVal .= "  Name: {$v['Name']}\n" .
                "  PluginURI: {$v['PluginURI']}\n" .
                "  Version: {$v['Version']}\n" .
                "  AuthorURI: {$v['AuthorURI']}\n" .
                "  Author: {$v['Author']}\n" .
                "  Active: {$v['Active']}\n\n";
        }

        return $retVal . "\n";
    }
}
