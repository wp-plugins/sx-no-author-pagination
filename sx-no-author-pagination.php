<?php
/*
Plugin Name:  SX No author Pagination
Version:      1.0
Plugin URI:   http://www.seomix.fr
Description:  SX No author Pagination removes properly any author pagination and redirects useless paginated content.
Availables languages : en_EN
Tags: author, pagination
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
 * Redirect author pagination
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