<?php

require_once '../wp-load.php';

class SRF_Content_Imports {
	private $data_path;

	public function __construct( $data_path ) {
		$this->data_path = $data_path;
	}

	// Post Categories:
	public function import_post_categories() : void {
		if ( ! is_string( $this->data_path ) ) {
			echo 'Error: The data path must be passed in as a string!';
			return; // exit early
		}

		$post_categories = json_decode( file_get_contents( $this->data_path ), true );

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
	}

	// Posts:
	public function import_posts() : void {
		if ( ! is_string( $this->data_path ) ) {
			echo 'Error: The data path must be passed in as a string!';
			return; // exit early
		}

		$posts = json_decode( file_get_contents( $this->data_path ), true );

		foreach ( $posts as $key => $value ) {
			// echo $posts[$key]['Name'] . "\n";
			$formatted_date = strtotime( substr( $posts[$key]['Published On'], 0, -29 ) );

			$args = array(
				'post_author'   => 1,
				'post_date' => date( 'Y-m-d H:i:s', $formatted_date ),
				'post_title'    => $posts[$key]['Name'],
				'post_name'    => $posts[$key]['Slug'],
				'post_content'  => $posts[$key]['Post Body'],
				// 'post_category' => array( 5 ),
				'comment_status' => 'closed',
				'ping_status' => 'closed',
				'post_status' => 'publish',
				'post_type' => 'post',
			);
			wp_insert_post( $args );
		}
	}

	// Warriors:
	public function import_warriors() : void {
		if ( ! is_string( $this->data_path ) ) {
			echo 'Error: The data path must be passed in as a string!';
			return; // exit early
		}

		$warriors = json_decode( file_get_contents( $this->data_path ), true );

		foreach ( $warriors as $key => $value ) {
			$formatted_date = strtotime( substr( $warriors[$key]['Published On'], 0, -29 ) );

			$args = array(
				'post_author'   => 1,
				'post_date' => date( 'Y-m-d H:i:s', $formatted_date ),
				'post_title'    => $warriors[$key]['Title'],
				'post_name'    => $warriors[$key]['Slug'],
				'post_content'  => $warriors[$key]['Full Story'],
				// TODO: Set post category to an argument
				// 'post_category' => array( 8 ),
				'comment_status' => 'closed',
				'ping_status' => 'closed',
				'post_status' => 'publish',
				'post_type' => 'srf-people',
			);

			wp_insert_post( $args );
		}
	}

	// Team:
	public function import_team() : void {
		if ( ! is_string( $this->data_path ) ) {
			echo 'Error: The data path must be passed in as a string!';
			return; // exit early
		}

		$team = json_decode( file_get_contents( $this->data_path ), true );

		foreach ( $team as $key => $value ) {
			$formatted_date = strtotime( substr( $team[$key]['Published On'], 0, -29 ) );
			$post_content = $team[$key]['Full Bio'] . "\n";
			$post_content .= 'Job Title: ' . $team[$key]['Job Title'] . "\n";
			$post_content .= 'Team: ' . $team[$key]['Team'] . "\n";
			$post_content .= 'Email: ' . $team[$key]['Email'] . "\n";
			$post_content .= 'Phone Number: ' . $team[$key]['Phone Number'] . "\n";
			$post_content .= 'Twitter: ' . $team[$key]['Twitter Link'] . "\n";
			$post_content .= 'Facebook: ' . $team[$key]['Facebook Link'] . "\n";
			$post_content .= 'LinkedIn: ' . $team[$key]['LinkedIn'];

			$args = array(
				'post_author'   => 1,
				'post_date' => date( 'Y-m-d H:i:s', $formatted_date ),
				'post_title'    => $team[$key]['Name'],
				'post_name'    => $team[$key]['Slug'],
				'post_excerpt'  => $team[$key]['Bio Summary'],
				'post_content'  => $post_content,
				// TODO: Set post category to an argument
				// 'post_category' => array( 9 ),
				'comment_status' => 'closed',
				'ping_status' => 'closed',
				'post_status' => 'publish',
				'post_type' => 'srf-people',
			);

			wp_insert_post( $args );
		}
	}

	// Researchers:
	public function import_researchers() : void {
		if ( ! is_string( $this->data_path ) ) {
			echo 'Error: The data path must be passed in as a string!';
			return; // exit early
		}

		$researchers = json_decode( file_get_contents( $this->data_path ), true );

		foreach ( $researchers as $key => $value ) {
			$formatted_date = strtotime( substr( $researchers[$key]['Published On'], 0, -29 ) );
			$post_content = $researchers[$key]['Bio Summary'] . "\n";
			$post_content .= 'Researcher URI: ' . $researchers[$key]['External Link'] . "\n";
			$post_content .= 'Institution: ' . $researchers[$key]['Institution'] . "\n";
			$post_content .= 'Institution URI: ' . $researchers[$key]['Institution Link'] . "\n";
			$post_content .= 'SAB Member: ' . ( $researchers[$key]['SAB Member?'] ? 'Yes' : "No" );

			$args = array(
				'post_author'   => 1,
				'post_date' => date( 'Y-m-d H:i:s', $formatted_date ),
				'post_title'    => $researchers[$key]['Name'],
				'post_name'    => $researchers[$key]['Slug'],
				'post_content'  => $post_content,
				// TODO: Set post category to an argument
				// 'post_category' => array( 9 ),
				'comment_status' => 'closed',
				'ping_status' => 'closed',
				'post_status' => 'publish',
				'post_type' => 'srf-people',
			);

			wp_insert_post( $args );
		}
	}
}
// $post_categories = new SRF_Content_Imports( './data/webflow-json/SRF-Blog-Categories.json' );
// $post_categories->import_post_categories();

// $posts = new SRF_Content_Imports( './data/webflow-json/SRF-Blog-Posts.json' );
// $posts->import_posts();

// $warriors = new SRF_Content_Imports( './data/webflow-json/SRF-Warriors.json' );
// $warriors->import_warriors();

// $team = new SRF_Content_Imports( './data/webflow-json/SRF-Team-Members.json' );
// $team->import_team();

$researchers = new SRF_Content_Imports( './data/webflow-json/SRF-Researchers.json' );
$researchers->import_researchers();