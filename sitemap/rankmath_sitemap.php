<?php

//  ┌─────────────────────────────────────────────────────────────────────────┐
//  │                                                                         │
//  │ Create Sitemap for generated class pages.                               │
//  │                                                                         │
//  │ - Register for rankmath to create classschema-sitemap.xml  			  │
//  │ - Dynamically add URLs to this sitemap using filters below.             │
//  │                                                                         │
//  │                                                                         │
//  └─────────────────────────────────────────────────────────────────────────┘

/**
 * Register Custom Taxonomy
 * 
 * This will create a custom taxonomy called class_schema undeer the 'posts' type.
 */
function add_classes_taxonomy() {

	$args = array(
		'labels'                     => array(
            'name'                       => 'Class Schema',
            'singular_name'              => 'Class Schema'
        ),
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => false,
		'rewrite'                    => true,
		'show_in_rest'               => true,
	);
	register_taxonomy( 'classschema', array( 'post' ), $args );

}
add_action( 'init', 'add_classes_taxonomy', 0 );



/**
 * Filter if XML sitemap transient cache is enabled. Turn it off.
 *
 * @param boolean $unsigned Enable cache or not, defaults to true
 */
add_filter( 'rank_math/sitemap/enable_caching', '__return_false');



/**
 * Get the new URLs from the options table and add it to the sitemap.
 * londonparkour.com/classschema-sitemap.xml
 */
function tb_rm_get_options(){	
	$urls = get_option( 'tb_rm_sitemap_urls' );
	return $urls;
}
add_action( 'rank_math/sitemap/classschema_content', 'tb_rm_get_options' );