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
// 		'capability_type' => 'post',
// 		'map_meta_cap' => true,
		'has_archive' => true,
		'supports' => array('title', 'editor', 'revisions')
	) );
	
	$role = get_role( 'author' );
	$role->add_cap( 'edit_others_posts' );
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

	function visitor_print( $post_id, $current_value = false ) {
		return "<p><input type='radio' name='erg_answer[{$post_id}]' class='erg_answer' data-post='{$post_id}' id='erg_answers-{$this->value}' value='{$this->value}' " . disabled( !is_user_logged_in(), true, false ) . ' ' . checked($this->value, $current_value, false) . "/> <label for='erg_answers-{$this->value}'>" . esc_html($this->label) . "</label></p>";
	}
}

add_action('add_meta_boxes', 'erg_meta_boxes');
function erg_meta_boxes() {
	add_meta_box( 'erg_answers', 'Answers', 'erg_answers_metabox', 'question', 'normal' );
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

add_shortcode( 'answer', 'erg_answer_shortcode' );
function erg_answer_shortcode( $atts ) {
	$answers = get_post_meta( get_the_ID(), '_erg_answers', true );
	
	if ( empty($answers) || !is_array($answers) )
		return;
	
	$return = '';
	if ( !is_user_logged_in() ) {
		$return .= "<div class='warning'>";
		$return .= "<p>By logging in to ergativity.org, you will be able to save your answers to individual questions and the questionnaire will guide you through relevant questions.</p>";
			$return .= "<p><a href='" . esc_url(wp_login_url()) . "'>Log in</a> or <a href='" . esc_url(site_url( 'wp-login.php?action=register', 'login' )) . "'>create a new account</a></p>";
		// @todo make these urls come back to the right page
		$return .= "</div>";
		$current_value = false;
	} else {
		wp_nonce_field( 'erg_answers', 'erg_answers_nonce' );
		$user_answers = get_user_meta( get_current_user_id(), '_erg_answers', true );
		if ( is_array($user_answers) && isset($user_answers[get_the_ID()]) )
			$current_value = $user_answers[get_the_ID()];
		else
			$current_value = false;
	}
	
	$return .= "<div class='answer'>";

	foreach ($answers as $answer) {
		$return .= (new ErgAnswer($answer))->visitor_print( get_the_ID(), $current_value );
	}
	
	$return .= "</div>";
    return $return;
}

add_shortcode( 'your language', 'erg_lang_shortcode' );
function erg_lang_shortcode( $atts ) {
// @todo display registered language name
    return "your language";
}

add_action( 'wp_ajax_erg_submit', 'erg_submit' );
function erg_submit() {
	// Check if our nonce is set.
	if ( ! isset( $_POST['nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['nonce'], 'erg_answers' ) ) {
		return;
	}

	$answers = get_user_meta( get_current_user_id(), '_erg_answers', true );
	if ( !is_array($answers) )
		$answers = array();
	$answers[(int) $_POST['id']] = $_POST['value'];
	// seems like deleting and then adding is the most reliable way?
	delete_user_meta( get_current_user_id(), '_erg_answers' );
	add_user_meta( get_current_user_id(), '_erg_answers', $answers, true );

	echo '1';
	exit;
}
