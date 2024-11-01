<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Pierre Jacomet (TalktUp LLC)
 * Date: 8/13/13
 * Time: 12:56 PM
 *
 */

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit;
if ( get_option( 'oup_options' ) != false ) {
    delete_option( 'oup_options' );
}