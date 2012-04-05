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
		
		include 'helpers.php';
		
		$field_groups = get_posts(array(
			'numberposts' 	=> 	-1,
			'post_type'		=>	'acf',
			'sort_column' => 'menu_order',
			'order' => 'ASC',
		));
		
		if($field_groups) {
			
			$acf = new Acf();
			$path = ABSPATH . '/field_groups';
			
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
				
				// each field_group gets it's own folder by field_group name
				$subpath = $path . '/' . $title;
				if (!is_dir($subpath) && !mkdir($subpath, 0755, false)) {
					WP_CLI::line( 'fieldgroup subdirectory exists or cant be created!' );
            	}else{
            		
            		// let's write the array to a data.php file so it can be used later on
            		$fp = fopen( $subpath . '/' ."data.php", "w" );
            		$output = "<?php \n\$group = " . var_export( $field_group_array , true ) . ';'; 
					fwrite($fp,$output);
					fclose($fp);
					
					// write the xml
					include 'xml_export.php';
            	}
            	
			endforeach;
		}
		else {
			echo "No field groups were found";
		}
		
	}
	
	function clean( $args = array() ) {
		WP_CLI::success( 'cleanup dabatase!' );
		
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
	}
	
	function import( $args = array() ) {
		WP_CLI::success( 'imported the data.xml field_groups to the dabatase!' );
		
		include 'parser.php';
		include 'wp-importer.php';
		include 'wp_import.php';
			
		$path_pattern = ABSPATH . 'field_groups/*/data.xml';
			
		foreach (glob($path_pattern) as $file) :
			$importer = new WP_Import();
			$importer->import($file);
      	endforeach;
	}
	
	static function help(){
		WP_CLI::success( 'possible subcommands: export, clean, import' );
	}

}