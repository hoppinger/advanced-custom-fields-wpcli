<?php
/**
 * Implement ACF command
 *
 * @package wp-cli
 * @subpackage commands/community
 * @maintainer Hoppinger (http://www.hoppinger.com)
 */

class ACFCommand extends WP_CLI_Command {
	/**
	 * Example subcommand
	 *
	 * @param array $args
	 */
	
	function status( $args = array() ) {
		WP_CLI::success( "status command displays all the custom field groups in the database \n" );
				
		$field_groups = get_posts(array(
			'numberposts' 	=> 	-1,
			'post_type'		=>	'acf',
			'sort_column' => 'menu_order',
			'order' => 'ASC',
		));
		
		if(!empty($field_groups))
			var_dump($field_groups);
		
		WP_CLI::success( count($field_groups) . " field_groups have been found in the database \n" );
	}
	
	function export( $args = array() ) {
		WP_CLI::success( "export command! \n" );
		
		include 'bin/helpers.php';
		
		if ( is_multisite() ) {
			$blog_list = get_blog_list( 0, 'all' );
		}
		else{
			$blog_list = array();
			$blog_list[] = array('blog_id' => 1);
		}
		
		foreach ( $blog_list as $blog ) :
			if ( is_multisite() ) switch_to_blog($blog['blog_id']) ;
			$field_groups = get_posts(array(
				'numberposts' 	=> 	-1,
				'post_type'		=>	'acf',
				'sort_column' => 'menu_order',
				'order' => 'ASC',
			));
			
			if($field_groups) {
				
				$acf = new Acf();
				$path = ABSPATH . '/field_groups/' ;
				
			if (!is_dir($path) && !mkdir($path, 0755, false)) {
					WP_CLI::line( 'fieldgroup directory exists or cant be created!' );
				}
				
				$blog_id_path = ABSPATH . '/field_groups/' . $blog['blog_id'];
				
				if (!is_dir($blog_id_path) && !mkdir($blog_id_path, 0755, false)) {
					WP_CLI::line( 'fieldgroup directory exists or cant be created!' );
				}
				
				if (!is_dir($path) && !mkdir($path, 0755, false)) {
					WP_CLI::line( 'fieldgroup directory exists or cant be created!' );
				}
				
				foreach($field_groups as $group) :
					$title = get_the_title($group->ID);			
				
					$field_group_array = array(
						'id' => uniqid(),
						'title' => $title,
						'fields' => $acf->get_acf_fields($group->ID),
						'location' => $acf->get_acf_location($group->ID),
						'options' => $acf->get_acf_options($group->ID),
						'menu_order' => $group->menu_order,
					);
					
					$sanitized_title = sanitize_title( $title );

					// each field_group gets it's own folder by field_group name
					$subpath = $blog_id_path . '/' . $sanitized_title;
					if (!is_dir($subpath) && !mkdir($subpath, 0755, false)) {
						WP_CLI::line( 'fieldgroup subdirectory exists or cant be created!' );
	            	}else{
	            		
	            		// let's write the array to a data.php file so it can be used later on
	            		$fp = fopen( $subpath . '/' ."data.php", "w" );
	            		$output = "<?php \n\$group = " . var_export( $field_group_array , true ) . ';'; 
									fwrite($fp,$output);
									fclose($fp);
						
									// write the xml
									include 'bin/xml_export.php';
	            	}
	            	
				endforeach;
			}
			else {
				echo "No field groups were found";
			}
			if ( is_multisite() ) restore_current_blog();
		endforeach;
	}
	
	function clean( $args = array() ) {
		WP_CLI::success( 'cleanup dabatase!' );
		
		if ( is_multisite() ) {
			$blog_list = get_blog_list( 0, 'all' );
		}
		else{
			$blog_list = array();
			$blog_list[] = array('blog_id' => 1);
		}
		
		foreach ( $blog_list as $blog ) :
			if ( is_multisite() ) switch_to_blog($blog['blog_id']);
			
			$field_groups = get_posts(array(
				'numberposts' 	=> 	-1,
				'post_type'		=>	'acf',
				'sort_column' => 'menu_order',
				'order' => 'ASC',
			));

			foreach($field_groups as $group) :
				global $wpdb;
				$wpdb->query("DELETE FROM $wpdb->postmeta WHERE post_id = $group->ID");
				$wpdb->query("DELETE FROM $wpdb->posts WHERE ID = $group->ID");
			endforeach;
		
			if ( is_multisite() ) restore_current_blog();
		endforeach;
	}
	
	function import( $args, $assoc_args ) {
		include 'bin/parser.php';
		include 'bin/wp-importer.php';
		include 'bin/wp_import.php';

		if ( is_multisite() ) {
			$blog_list = get_blog_list( 0, 'all' );
		}
		else{
			$blog_list = array();
			$blog_list[] = array('blog_id' => 1);
		}
		
		// args[0] is the sanitized name of the field_group to import into the database
		if( isset( $args[0] ) ) {
			// we have a field_group name argument, let's load this xml into the database
			// set a new var with a decent name that makes sense farther down the line
			$field_group_name  = $args[0];

			if( $field_group_name == 'all' ){
				foreach ( $blog_list as $blog ) :
					if ( is_multisite() ) switch_to_blog($blog['blog_id']);

					$path_pattern = ABSPATH . 'field_groups/' . $blog['blog_id'] . '/*/data.xml';
						
					foreach (glob($path_pattern) as $file) :
						$importer = new WP_Import();
						$importer->import($file);
			    endforeach;
			      	
			    if ( is_multisite() ) restore_current_blog();
			    WP_CLI::success( 'imported all the data.xml field_groups to the dabatase!' );
				endforeach;
			} else {
				$path_pattern = ABSPATH . 'field_groups/1/' . $field_group_name . '/data.xml';
				foreach (glob($path_pattern) as $file) :
					$importer = new WP_Import();
					$importer->import($file);
					WP_CLI::success( 'imported the data.xml for "' . $field_group_name .'" into to the dabatase!' );
		    endforeach;	
			}
		
		} else {
			WP_CLI::error( 'You need to provide an argument: "field-group-name" or use "all" to import all field groups 
Example: wp acf impport field-group-name' );
		}

	}
	
	static function help(){
		WP_CLI::success( 'possible subcommands: status, export, clean, import' );
	}

}