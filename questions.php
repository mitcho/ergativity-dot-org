<?php

// setup ergativity questionaire environment
add_action('init', 'erg_init');
function erg_init() {
	register_post_type( 'question', array(
		'label' => 'Questions',
		'labels' => array(
			'name' => 'Questions',
			'singular_name' => 'Question',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Question',
			'edit_item' => 'Edit Question',
			'new_item' => 'New Question',
			'view_item' => 'View Question',
			'search_items' => 'Search Questions',
			'not_found' => 'No questions found',
			'not_found_in_trash' => 'No questions found in Trash',
// 			'parent_item_colon' => '',
			'all_items' => 'All Questions'
		),
		'description' => 'A question in the Ergativity questionaire',
		'public' => true,
		'hierarchical' => false,
		'menu_icon' => 'dashicons-editor-help',
		'rewrite' => array(
			'slug' => 'questions',
			'with_front' => true
		)
	) );
}

function erg_lang_shortcode( $atts ) {
// @todo display registered language name
    return "your language";
}
add_shortcode( 'your language', 'erg_lang_shortcode' );