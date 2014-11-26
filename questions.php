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
		),
		'has_archive' => true
	) );
}

class ErgAnswer {
	var $value = '';
	var $label = '';
	function ErgAnswer($data = null) {
		if ( !is_null($data) && is_array($data) ) {
			$this->value = $data['value'];
			$this->label = $data['label'];
		}
	}
	function meta_print() {
		global $erg_meta_print_key;
		$erg_meta_print_key++;
	
		echo "<tr>";
		echo "<td><input type='text' size='3' name='erg_answers[{$erg_meta_print_key}][value]' value='" . esc_attr($this->value) . "'/></td>";
		echo "<td><input type='text' size='30' name='erg_answers[{$erg_meta_print_key}][label]' value='" . esc_attr($this->label) . "'/></td>";
		echo "</tr>";
	}
}

add_action('add_meta_boxes', 'erg_meta_boxes');
function erg_meta_boxes() {
	add_meta_box( 'erg_answers', 'Answers', 'erg_answers_metabox', 'question' );
}
function erg_answers_metabox( $post ) {
	wp_nonce_field( 'erg_answers_metabox', 'erg_answers_metabox_nonce' );
	$answers = get_post_meta( $post->ID, '_erg_answers', true );

	echo '<table><tr><th><abbr title="Used internally; not shown to the user">Value</abbr></th><th>Answer text</th></tr>';

	global $erg_meta_print_key;
	$erg_meta_print_key = 0;
	foreach ($answers as $answer) {
		(new ErgAnswer($answer))->meta_print();
	}
	
	// print dummy answers:
	(new ErgAnswer())->meta_print();
	if ($erg_meta_print_key == 1)
		(new ErgAnswer())->meta_print();
	
	echo '</table>';
}
add_action( 'save_post', 'erg_answers_save' );
function erg_answers_save( $post_id ) {

	// Check if our nonce is set.
	if ( ! isset( $_POST['erg_answers_metabox_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['erg_answers_metabox_nonce'], 'erg_answers_metabox' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	/* OK, it's safe for us to save the data now. */
	
	// Make sure that it is set.
	if ( ! isset( $_POST['erg_answers'] ) || ! is_array( $_POST['erg_answers'] ) ) {
		return;
	}

	$answers = array_filter($_POST['erg_answers'], function ($x) {
		if ( !is_array($x) )
			return false;
		if ( empty($x['value']) || empty($x['label']) )
			return false;
		return true;
	});

	// Update the meta field in the database.
	add_post_meta( $post_id, '_erg_answers', $answers, true );
}

add_shortcode( 'your language', 'erg_lang_shortcode' );
function erg_lang_shortcode( $atts ) {
// @todo display registered language name
    return "your language";
}

