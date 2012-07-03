<?php 

/**
 * Version number for the export format.
 *
 * Bump this when something changes that might affect compatibility.
 *
 * @since 2.5.0
 */
define( 'WXR_VERSION', '1.1' );

/**
 * Wrap given string in XML CDATA tag.
 *
 * @since 2.1.0
 *
 * @param string $str String to wrap in XML CDATA tag.
 */
function wpcli_wxr_cdata( $str ) {
  if ( seems_utf8( $str ) == false )
    $str = utf8_encode( $str );

  // $str = ent2ncr(esc_html($str));
  $str = "<![CDATA[$str" . ( ( substr( $str, -1 ) == ']' ) ? ' ' : '' ) . ']]>';

  return $str;
}

/**
 * Return the URL of the site
 *
 * @since 2.5.0
 *
 * @return string Site URL.
 */
function wcli_wxr_site_url() {
  // ms: the base url
  if ( is_multisite() )
    return network_home_url();
  // wp: the blog url
  else
    return get_bloginfo_rss( 'url' );
}

/**
 * Output a tag_description XML tag from a given tag object
 *
 * @since 2.3.0
 *
 * @param object $tag Tag Object
 */
function wpcli_wxr_tag_description( $tag ) {
  if ( empty( $tag->description ) )
    return;

  echo '<wp:tag_description>' . wxr_cdata( $tag->description ) . '</wp:tag_description>';
}

/**
 * Output a term_name XML tag from a given term object
 *
 * @since 2.9.0
 *
 * @param object $term Term Object
 */
function wpcli_wxr_term_name( $term ) {
  if ( empty( $term->name ) )
    return;

  echo '<wp:term_name>' . wxr_cdata( $term->name ) . '</wp:term_name>';
}

/**
 * Output a term_description XML tag from a given term object
 *
 * @since 2.9.0
 *
 * @param object $term Term Object
 */
function wxr_term_description( $term ) {
  if ( empty( $term->description ) )
    return;

  echo '<wp:term_description>' . wxr_cdata( $term->description ) . '</wp:term_description>';
}



/**
 * Output list of authors with posts
 *
 * @since 3.1.0
 */
function wpcli_wxr_authors_list() {
  global $wpdb;

  $authors = array();
  $results = $wpdb->get_results( "SELECT DISTINCT post_author FROM $wpdb->posts" );
  foreach ( (array) $results as $result )
    $authors[] = get_userdata( $result->post_author );

  $authors = array_filter( $authors );
  
  $output = '';
  foreach( $authors as $author ) {
    $output .= "\t<wp:author>";
    $output .= '<wp:author_id>' . $author->ID . '</wp:author_id>';
    $output .= '<wp:author_login>' . $author->user_login . '</wp:author_login>';
    $output .= '<wp:author_email>' . $author->user_email . '</wp:author_email>';
    $output .= '<wp:author_display_name>' . wpcli_wxr_cdata( $author->display_name ) . '</wp:author_display_name>';
    $output .= '<wp:author_first_name>' . wpcli_wxr_cdata( $author->user_firstname ) . '</wp:author_first_name>';
    $output .= '<wp:author_last_name>' . wpcli_wxr_cdata( $author->user_lastname ) . '</wp:author_last_name>';
    $output .= "</wp:author>\n";
  }
  
  return $output;
}