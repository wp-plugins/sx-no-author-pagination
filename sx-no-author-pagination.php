<?php
/*
Plugin Name:  SX No author Pagination
Version:      1.2.1
Plugin URI:   http://www.seomix.fr
Description:  SX No author Pagination removes properly any author pagination and redirects useless paginated content.
Availables languages : en_EN
Tags: author, pagination, page, auteur, paginated
Author: Daniel Roch
Author URI: http://www.seomix.fr
Requires at least: 3.3
Tested up to: 4.2
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
 Compatibility check
 * © Julio Potier https://github.com/BoiteAWeb/ActivationTester
 */
add_action( 'admin_init', 'sx_no_author_check_version' );
function sx_no_author_check_version() {
  // This is where you set you needs
  $mandatory = array(
      'PluginName'=>'SX No author Pagination', 
      'WordPress'=>'3.3' 
    );
  // Avoid Notice error
  $errors = array();
  // loop the mandatory things
  foreach( $mandatory as $what => $how ) {
    switch( $what ) {
      case 'WordPress':
          if( version_compare( $GLOBALS['wp_version'], $how ) < 0 )
          {
            $errors[$what] = $how;
          }
        break;
    }
  }
  // Add a filter for devs
  $errors = apply_filters( 'validate_errors', $errors, $mandatory['PluginName'] );
  // We got errors!
  if( !empty( $errors ) ) {
    global $current_user;
    // We add the plugin name for late use
    $errors['PluginName'] = $mandatory['PluginName'];
    // Set a transient with these errors
    set_transient( 'myplugin_disabled_notice' . $current_user->ID, $errors );
    // Remove the activate flag
    unset( $_GET['activate'] );
    // Deactivate this plugin
    deactivate_plugins( plugin_basename( __FILE__ ) );
  }
}
add_action( 'admin_notices', 'sx_no_author_disabled_notice' );
function sx_no_author_disabled_notice() {
  global $current_user;
  // We got errors!
  if( $errors = get_transient( 'myplugin_disabled_notice' . $current_user->ID ) ) {
    // Remove the transient
    delete_transient( 'myplugin_disabled_notice' . $current_user->ID );
    // Pop the plugin name
    $plugin_name = array_pop( $errors );
    // Begin the buffer output
    $error = '<ul>';
    // Loop on each error, you can change the "i18n domain" here -> my_plugin (i would like to avoid this)
    foreach( $errors as $what => $how) {
      $error .= '<li>'.sprintf( __( '&middot; Requires %s: <code>%s</code>', 'my_plugin' ), $what, $how ).'</li>';
    }
    // End the buffer output
    $error .= '</ul>';
    // Echo the output using a WordPress string (no i18n needed)
    echo '<div class="error"><p>' . sprintf( __( 'The plugin <code>%s</code> has been <strong>deactivated</strong> due to an error: %s' ), $plugin_name, $error ) . '</p></div>';
  }
}