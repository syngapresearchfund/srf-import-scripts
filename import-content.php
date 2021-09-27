<?php

require_once '../../../wp-load.php';

$post_categories = json_decode( file_get_contents( 'webflow-json/SRF-Blog-Categories.json' ), true );
$posts           = json_decode( file_get_contents( 'webflow-json/SRF-Blog-Posts.json' ), true );
$warriors        = json_decode( file_get_contents( 'webflow-json/SRF-Warriors.json' ), true );

// Post Categories:
foreach ( $post_categories as $key => $value ) {
	// echo $post_categories[$key]['Name'] . "\n";

	wp_insert_term(
		$post_categories[$key]['Name'],
		'category',
		array(
			'description' => $post_categories[$key]['Description'],
			'slug' => $post_categories[$key]['Slug'],
		)
	);
}

// Posts:
foreach ( $posts as $key => $value ) {
	// echo $posts[$key]['Name'] . "\n";
	$formatted_date_posts = strtotime( substr( $posts[$key]['Published On'], 0, -29 ) );

	$post_args = array(
		'post_author'   => 1,
		'post_date' => date( 'Y-m-d H:i:s', $formatted_date_posts ),
		'post_title'    => $posts[$key]['Name'],
		'post_name'    => $posts[$key]['Slug'],
		'post_content'  => $posts[$key]['Post Body'],
		// 'post_category' => array( 5 ),
		'comment_status' => 'closed',
		'ping_status' => 'closed',
		'post_status' => 'publish',
		'post_type' => 'post',
	);
	wp_insert_post( $post_args );
}

// Warriors:
foreach ( $warriors as $key => $value ) {
	// echo $warriors[$key]['Title'] . "\n";
	$formatted_date_warriors = strtotime( substr( $warriors[$key]['Published On'], 0, -29 ) );

	$warrior_args = array(
		'post_author'   => 1,
		'post_date' => date( 'Y-m-d H:i:s', $formatted_date_warriors ),
		'post_title'    => $warriors[$key]['Title'],
		'post_name'    => $warriors[$key]['Slug'],
		'post_content'  => $warriors[$key]['Full Story'],
		'post_category' => array( 2 ),
		'comment_status' => 'closed',
		'ping_status' => 'closed',
		'post_status' => 'publish',
		'post_type' => 'srf-people',
	);

	wp_insert_post( $warrior_args );
}