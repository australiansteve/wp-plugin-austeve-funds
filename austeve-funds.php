<?php

/*
Plugin Name: Community Foundation Funds
Plugin URI: https://github.com/australiansteve/austeve-funds
Description: Enter and display Funds 
Version: 1.0.0
Author: AustralianSteve
Author URI: http://australiansteve.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


class AUSteve_Funds {

	function __construct() {

		//Register post type
		add_action( 'init', array($this, 'austeve_create_funds_post_type'), 0 );

		add_shortcode( 'austeve_funds', array($this, 'shortcode_output'));

		add_action( 'wp_enqueue_scripts', array($this, 'austeve_funds_enqueue_style') );

		add_action( 'wp_enqueue_scripts', array($this, 'austeve_funds_enqueue_script') );
	}


	function austeve_funds_enqueue_style() {
		wp_enqueue_style( 'austeve-funds', plugin_dir_url( __FILE__ ). 'style.css' , '' , '1.0'); 
	}

	function austeve_funds_enqueue_script() {
		wp_enqueue_script( 'austeve-funds-js', plugin_dir_url( __FILE__ ). 'js/funds.js' , array( 'jquery-ui-accordion', 'jquery' ) , '1.0'); 
	}

	function austeve_create_funds_post_type() {

		// Set UI labels for Custom Post Type
		$labels = array(
			'name'                => _x( 'Funds', 'Post Type General Name', 'austeve-funds' ),
			'singular_name'       => _x( 'Fund', 'Post Type Singular Name', 'austeve-funds' ),
			'menu_name'           => __( 'Funds', 'austeve-funds' ),
			'all_items'           => __( 'All Funds', 'austeve-funds' ),
			'view_item'           => __( 'View Fund', 'austeve-funds' ),
			'add_new_item'        => __( 'Add New Fund', 'austeve-funds' ),
			'add_new'             => __( 'Add New', 'austeve-funds' ),
			'edit_item'           => __( 'Edit Fund', 'austeve-funds' ),
			'update_item'         => __( 'Update Fund', 'austeve-funds' ),
			'search_items'        => __( 'Search Funds', 'austeve-funds' ),
			'not_found'           => __( 'Not Found', 'austeve-funds' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'austeve-funds' ),
		);
		
		// Set other options for Custom Post Type		
		$args = array(
			'label'               => __( 'Funds', 'austeve-funds' ),
			'description'         => __( 'Community Foundation Funds', 'austeve-funds' ),
			'labels'              => $labels,
			// Features this CPT supports in Post Editor
			'supports'            => array( 'title', 'author', 'revisions', 'editor'),
			// You can associate this CPT with a taxonomy or custom taxonomy. 
			'taxonomies'          => array( ),
			/* A hierarchical CPT is like Pages and can have
			* Parent and child items. A non-hierarchical CPT
			* is like Posts.
			*/	
			'hierarchical'        => false,
			'rewrite'           => array( 'slug' => 'funds' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'    => 'post',
			'menu_icon'				=> 'dashicons-performance',
		);
		
		// Registering your Custom Post Type
		register_post_type( 'austeve-funds', $args );


		// Add new taxonomy, make it hierarchical (like categories)
		$categoryLabels = array(
			'name'              => _x( 'Fund Categories', 'taxonomy general name', 'austeve-funds' ),
			'singular_name'     => _x( 'Fund Category', 'taxonomy singular name', 'austeve-funds' ),
			'search_items'      => __( 'Search Fund Categories', 'austeve-funds' ),
			'all_items'         => __( 'All Fund Categories', 'austeve-funds' ),
			'parent_item'       => __( 'Parent Fund Category', 'austeve-funds' ),
			'parent_item_colon' => __( 'Parent Fund Category:', 'austeve-funds' ),
			'edit_item'         => __( 'Edit Fund Category', 'austeve-funds' ),
			'update_item'       => __( 'Update Fund Category', 'austeve-funds' ),
			'add_new_item'      => __( 'Add New Fund Category', 'austeve-funds' ),
			'new_item_name'     => __( 'New Fund Category Name', 'austeve-funds' ),
			'menu_name'         => __( 'Fund Categories', 'austeve-funds' ),
		);

		$categoryArgs = array(
			'hierarchical'      => true,
			'label'               => __( 'austeve-funds-category', 'austeve-funds' ),
			'labels'            => $categoryLabels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'fund-category' ),
			'capability_type'    => 'post',
		);

		register_taxonomy( 'austeve-funds-category', array( 'austeve-funds' ), $categoryArgs );
	}

	function shortcode_output($atts, $content)
	{
	    $atts = shortcode_atts( array(
	        'include_category' => '',
	        'exclude_category' => '',
	    ), $atts );
	    
	    extract( $atts );

	    $args = array(
	        'post_type' => 'austeve-funds',
	        'post_status' => array('publish'),
	        'posts_per_page' => -1,
	        'paged'         => false,

	    );

	    if ($include_category != '' || $exclude_category != '')
	    {
	    	$tax_query = array(
				'relation' => 'AND',
			);

		    if ($include_category != '')
		    {
		        $tax_query[] = array(
	                'taxonomy' => 'austeve-funds-category',
	                'field'    => 'slug',
	                'terms'    => explode(',', $include_category),
		        );
		    }

		    if ($exclude_category != '')
		    {
		        $tax_query[] = array(
	                'taxonomy' => 'austeve-funds-category',
	                'field'    => 'slug',
	                'operator' => 'NOT IN',
	                'terms'    => explode(',', $exclude_category),
		        );
		    }

		    $args['tax_query'] = $tax_query;
	    }

	    query_posts( $args );
	    
	    if ( have_posts() ):
		    echo "<div id='funds'>";
		    while ( have_posts() ) :
		        the_post();

		        $terms = get_the_terms( get_the_ID(), 'austeve-funds-category' );
	        	$categories = "";
		        if ($terms):
		        	foreach($terms as $term)
		        	{
						$categories .= $term->slug." ";
		        	}
		        endif;
?>

				<h3 class='title <?php echo $categories; ?>'><?php the_title(); ?></h3>
        		
<?php
		    endwhile;
		    echo "</div>";
		else:
			    	echo "<div id='funds'>No Funds found</div>";
		endif;

		wp_reset_query();
	}

}

// Create Funds!
$austeveFunds = new AUSteve_Funds();

?>
