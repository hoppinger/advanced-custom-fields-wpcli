<?php
/*--------------------------------------------------------------------------------------
*
* ACF XML Export without download. write directory to httpdocs/fieldgroups
*
* @author Hoppinger
* @since 
* 
*-------------------------------------------------------------------------------------*/

// includes
// this is no longer needed in wordpress 3.5
//require_once(ABSPATH . 'wp-load.php');

$output = '';

$output .= '<?xml version="1.0" encoding="' . get_bloginfo('charset') . "\" ?>\n";

//$output .= get_the_generator( 'export' );

$output .= '<rss version="2.0"
  xmlns:excerpt="http://wordpress.org/export/' . WXR_VERSION . '/excerpt/"
  xmlns:content="http://purl.org/rss/1.0/modules/content/"
  xmlns:wfw="http://wellformedweb.org/CommentAPI/"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:wp="http://wordpress.org/export/' . WXR_VERSION . '/">

  <channel>
    <title>' . get_bloginfo_rss( 'name' ) . '</title>
    <link>' . get_bloginfo_rss( 'url' ) . '</link>
    <description>' . get_bloginfo_rss( 'description' ) . '</description>
    <language>' . get_option( 'rss_language' ) . '</language>
    <wp:wxr_version>' . WXR_VERSION . '</wp:wxr_version>
    <wp:base_site_url>' .  wcli_wxr_site_url() . '</wp:base_site_url>
    <wp:base_blog_url>' .  get_bloginfo_rss( 'url' ) . '</wp:base_blog_url>';
  
  $output .= wpcli_wxr_authors_list();

  global $wpdb;
  global $wp_query;
  $where = 'WHERE ID = '.$group->ID.'';
  $posts = $wpdb->get_results( "SELECT * FROM {$wpdb->posts} $where" );
  
  //Begin Loop
  foreach ( $posts as $post ) :
    setup_postdata( $post );

    $output .=  '<item>
      <title>' . apply_filters( 'the_title_rss', $post->post_title ) . '</title>
      <link>' . the_permalink_rss() . '</link>
      <pubDate>' . mysql2date( 'D, d M Y H:i:s +0000', $post->post_date, false ) . '</pubDate>
      <dc:creator>' . get_the_author_meta( 'login' ) . '</dc:creator>
      <guid isPermaLink="false">' . esc_url( $post->guid ) . '</guid>
      <wp:post_id>' . $post->ID . '</wp:post_id>
      <wp:post_date>' . $post->post_date . '</wp:post_date>
      <wp:post_date_gmt>' . $post->post_date_gmt . '</wp:post_date_gmt>
      <wp:comment_status>' . $post->comment_status . '</wp:comment_status>
      <wp:ping_status>' . $post->ping_status . '</wp:ping_status>
      <wp:post_name>' . $post->post_name . '</wp:post_name>
      <wp:status>' . $post->post_status . '</wp:status>
      <wp:post_parent>' . $post->post_parent . '</wp:post_parent>
      <wp:menu_order>' . $post->menu_order . '</wp:menu_order>
      <wp:post_type>' . $post->post_type . '</wp:post_type>
      <wp:post_password>' . $post->post_password . '</wp:post_password>
    ';
  
    $postmeta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE post_id = %d ORDER BY meta_key, meta_value ASC", $post->ID ) );
    foreach( $postmeta as $meta ) : if ( $meta->meta_key != '_edit_lock' ) :
    $output .= '
      <wp:postmeta>
        <wp:meta_key>' . $meta->meta_key . '</wp:meta_key>
        <wp:meta_value>' . wpcli_wxr_cdata( str_replace(array("\r\n"),"  ",$meta->meta_value) ) . '</wp:meta_value>
      </wp:postmeta>
    ';
    endif; endforeach;
    
    $output .=  '</item>';

  endforeach;

$output .= '</channel></rss>';

$fp = fopen( $subpath . '/' ."data.xml", "w" );
fwrite($fp,$output);
fclose($fp);
