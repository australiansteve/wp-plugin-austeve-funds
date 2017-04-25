<?php

/*
Plugin Name: Frequently Asked Questions
Plugin URI: https://github.com/australiansteve/austeve-faqs
Description: Enter and display FAQs easily 
Version: 1.0.0
Author: AustralianSteve
Author URI: http://australiansteve.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


class AUSteve_FAQs {

	function __construct() {

		//Register post type
		add_action( 'init', array($this, 'austeve_create_faqs_post_type'), 0 );

		add_shortcode( 'austeve_faqs', array($this, 'shortcode_output'));

		add_action( 'wp_enqueue_scripts', array($this, 'austeve_faqs_enqueue_style') );

		add_action( 'wp_enqueue_scripts', array($this, 'austeve_faqs_enqueue_script') );
	}


	function austeve_faqs_enqueue_style() {
		wp_enqueue_style( 'jquery-ui-css', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css');
		wp_enqueue_style( 'austeve-faqs', plugin_dir_url( __FILE__ ). 'style.css' , '' , '1.0'); 
	}

	function austeve_faqs_enqueue_script() {
		wp_enqueue_script( 'austeve-faqs-js', plugin_dir_url( __FILE__ ). 'js/faqs.js' , array( 'jquery-ui-accordion', 'jquery' ) , '1.0'); 
	}

	function austeve_create_faqs_post_type() {

		// Set UI labels for Custom Post Type
		$labels = array(
			'name'                => _x( 'FAQs', 'Post Type General Name', 'austeve-faqs' ),
			'singular_name'       => _x( 'FAQ', 'Post Type Singular Name', 'austeve-faqs' ),
			'menu_name'           => __( 'FAQs', 'austeve-faqs' ),
			'all_items'           => __( 'All FAQs', 'austeve-faqs' ),
			'view_item'           => __( 'View FAQ', 'austeve-faqs' ),
			'add_new_item'        => __( 'Add New FAQ', 'austeve-faqs' ),
			'add_new'             => __( 'Add New', 'austeve-faqs' ),
			'edit_item'           => __( 'Edit FAQ', 'austeve-faqs' ),
			'update_item'         => __( 'Update FAQ', 'austeve-faqs' ),
			'search_items'        => __( 'Search FAQs', 'austeve-faqs' ),
			'not_found'           => __( 'Not Found', 'austeve-faqs' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'austeve-faqs' ),
		);
		
		// Set other options for Custom Post Type		
		$args = array(
			'label'               => __( 'FAQs', 'austeve-faqs' ),
			'description'         => __( 'Frequently Asked Questions', 'austeve-faqs' ),
			'labels'              => $labels,
			// Features this CPT supports in Post Editor
			'supports'            => array( 'title', 'author', 'revisions', ),
			// You can associate this CPT with a taxonomy or custom taxonomy. 
			'taxonomies'          => array( ),
			/* A hierarchical CPT is like Pages and can have
			* Parent and child items. A non-hierarchical CPT
			* is like Posts.
			*/	
			'hierarchical'        => false,
			'rewrite'           => array( 'slug' => 'faqs' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 35,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'    => 'post',
			'menu_icon'				=> 'dashicons-editor-help',
		);
		
		// Registering your Custom Post Type
		register_post_type( 'austeve-faqs', $args );


		// Add new taxonomy, make it hierarchical (like categories)
		$categoryLabels = array(
			'name'              => _x( 'FAQ Categories', 'taxonomy general name', 'austeve-faqs' ),
			'singular_name'     => _x( 'FAQ Category', 'taxonomy singular name', 'austeve-faqs' ),
			'search_items'      => __( 'Search FAQ Categories', 'austeve-faqs' ),
			'all_items'         => __( 'All FAQ Categories', 'austeve-faqs' ),
			'parent_item'       => __( 'Parent FAQ Category', 'austeve-faqs' ),
			'parent_item_colon' => __( 'Parent FAQ Category:', 'austeve-faqs' ),
			'edit_item'         => __( 'Edit FAQ Category', 'austeve-faqs' ),
			'update_item'       => __( 'Update FAQ Category', 'austeve-faqs' ),
			'add_new_item'      => __( 'Add New FAQ Category', 'austeve-faqs' ),
			'new_item_name'     => __( 'New FAQ Category Name', 'austeve-faqs' ),
			'menu_name'         => __( 'FAQ Categories', 'austeve-faqs' ),
		);

		$categoryArgs = array(
			'hierarchical'      => true,
			'label'               => __( 'austeve-faqs-category', 'austeve-faqs' ),
			'labels'            => $categoryLabels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'faq-category' ),
			'capability_type'    => 'post',
		);

		register_taxonomy( 'austeve-faqs-category', array( 'austeve-faqs' ), $categoryArgs );
	}

	function shortcode_output($atts, $content)
	{
	    $atts = shortcode_atts( array(
	        'include_category' => '',
	        'exclude_category' => '',
	    ), $atts );
	    
	    extract( $atts );

	    $args = array(
	        'post_type' => 'austeve-faqs',
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
	                'taxonomy' => 'austeve-faqs-category',
	                'field'    => 'slug',
	                'terms'    => explode(',', $include_type),
		        );
		    }

		    if ($exclude_category != '')
		    {
		        $tax_query[] = array(
	                'taxonomy' => 'austeve-faqs-category',
	                'field'    => 'slug',
	                'operator' => 'NOT IN',
	                'terms'    => explode(',', $include_type),
		        );
		    }

		    $args['tax_query'] = $tax_query;
	    }

	    query_posts( $args );
	    
	    if ( have_posts() ):
		    echo "<div id='faqs'>";
		    while ( have_posts() ) :
		        the_post();
?>

				<h3 class='question'><?php the_title(); ?></h3>
				<div class='answer'><?php echo get_field('answer'); ?></div>
        		
<?php
		    endwhile;
		    echo "</div>";
		else:
			    	echo "<div id='faqs'>No FAQs found</div>";
		endif;

		wp_reset_query();
	}

}

// Create FAQs!
$austeveFAQs = new AUSteve_FAQs();

?>
