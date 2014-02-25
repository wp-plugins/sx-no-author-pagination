<?php
/*
Plugin Name:  SX No author Pagination
Version:      1.2
Plugin URI:   http://www.seomix.fr
Description:  SX No author Pagination removes properly any author pagination and redirects useless paginated content.
Availables languages : en_EN
Tags: author, pagination, page, auteur, paginated
Author: Daniel Roch
Author URI: http://www.seomix.fr
Requires at least: 3.3
Tested up to: 3.8.1
License: GPL v3

SX No author Pagination - SeoMix
Copyright (C) 2014, Daniel Roch - contact@seomix.fr

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


/**
  Security
*/
if ( ! defined( 'ABSPATH' ) ) exit;


/**
  Don't paginate author page
  * © Daniel Roch
  */
function seomix_remove_author_pagination($query) { 
  if ( is_author() && $query->is_main_query() )
    // Don't paginate author pages
    $query->set('no_found_rows', true);
}
add_action('pre_get_posts', 'seomix_remove_author_pagination');


/**
 Redirect author pagination
 * © Daniel Roch 
 */ 
function seomix_redirect_author_pagination () {
  global $paged, $page;
  // Are we on an author pagination ?
  if ( is_author () && ( $paged >= 2 || $page >= 2 ) ) {
    $url = get_author_posts_url( get_the_author_meta ( 'ID' ) );
    wp_redirect( $url , '301' );
    die;
  }
}
add_action( 'template_redirect', 'seomix_redirect_author_pagination' );


/**
 Check Compatibility
 * http://wptavern.com/how-to-prevent-wordpress-plugins-from-activating-on-sites-with-incompatible-hosting-environments
 */
class seomix_noauthor_checkcompatibility {
    function __construct() {
        add_action( 'admin_init', array( $this, 'check_version' ) );

        // Don't run anything else in the plugin, if we're on an incompatible WordPress version
        if ( ! self::compatible_version() ) {
            return;
        }
    }

    // The primary sanity check, automatically disable the plugin on activation if it doesn't
    // meet minimum requirements.
    static function activation_check() {
        if ( ! self::compatible_version() ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die('SX No Author Pagination requires WordPress 3.3 or higher!');
        }
    }

    // The backup sanity check, in case the plugin is activated in a weird way,
    // or the versions change after activation.
    function check_version() {
        if ( ! self::compatible_version() ) {
            if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
                deactivate_plugins( plugin_basename( __FILE__ ) );
                add_action( 'admin_notices', array( $this, 'disabled_notice' ) );
                if ( isset( $_GET['activate'] ) ) {
                    unset( $_GET['activate'] );
                }
            }
        }
    }

    function disabled_notice() {
       echo '<div class="updated"><p><strong>SX No Author Pagination requires WordPress 3.3 or higher!</strong></p></div>';
    }

    static function compatible_version() {
        if ( version_compare( $GLOBALS['wp_version'], '3.3', '<' ) ) {
             return false;
         }
        return true;
    }
}

global $sxnoauthorcheck;
$sxnoauthorcheck = new seomix_noauthor_checkcompatibility();
register_activation_hook( __FILE__, array( 'seomix_noauthor_checkcompatibility', 'activation_check' ) );