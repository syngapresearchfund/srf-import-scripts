<?php

require_once '../wp-load.php';

class SRF_Content_Imports {
	private $data_set;

	public function __construct( $data_path ) {
		if ( ! is_string( $data_path ) ) {
			echo 'Error: The data path must be passed in as a string!';
			return; // exit early
		}

		$this->data_set = json_decode( file_get_contents( $data_path ), true );
	}

	// Post Categories:
	public function import_post_categories() : void {
		foreach ( $this->data_set as $key => $value ) {
			// echo $this->data_set[$key]['Name'] . "\n";
			wp_insert_term(
				$this->data_set[$key]['Name'],
				'category',
				array(
					'description' => $this->data_set[$key]['Description'],
					'slug' => $this->data_set[$key]['Slug'],
				)
			);
		}
	}

	// Posts:
	public function import_posts() : void {
		foreach ( $this->data_set as $key => $value ) {
			// echo $this->data_set[$key]['Name'] . "\n";
			$formatted_date = strtotime( substr( $this->data_set[$key]['Published On'], 0, -29 ) );

			$args = array(
				'post_author'   => 1,
				'post_date' => date( 'Y-m-d H:i:s', $formatted_date ),
				'post_title'    => $this->data_set[$key]['Name'],
				'post_name'    => $this->data_set[$key]['Slug'],
				'post_content'  => $this->data_set[$key]['Post Body'],
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
		foreach ( $this->data_set as $key => $value ) {
			$formatted_date = strtotime( substr( $this->data_set[$key]['Published On'], 0, -29 ) );

			$args = array(
				'post_author'   => 1,
				'post_date' => date( 'Y-m-d H:i:s', $formatted_date ),
				'post_title'    => $this->data_set[$key]['Title'],
				'post_name'    => $this->data_set[$key]['Slug'],
				'post_content'  => $this->data_set[$key]['Full Story'],
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
		foreach ( $this->data_set as $key => $value ) {
			$formatted_date = strtotime( substr( $this->data_set[$key]['Published On'], 0, -29 ) );
			$post_content = $this->data_set[$key]['Full Bio'] . "\n";
			$post_content .= 'Job Title: ' . $this->data_set[$key]['Job Title'] . "\n";
			$post_content .= 'Team: ' . $this->data_set[$key]['Team'] . "\n";
			$post_content .= 'Email: ' . $this->data_set[$key]['Email'] . "\n";
			$post_content .= 'Phone Number: ' . $this->data_set[$key]['Phone Number'] . "\n";
			$post_content .= 'Twitter: ' . $this->data_set[$key]['Twitter Link'] . "\n";
			$post_content .= 'Facebook: ' . $this->data_set[$key]['Facebook Link'] . "\n";
			$post_content .= 'LinkedIn: ' . $this->data_set[$key]['LinkedIn'];

			$args = array(
				'post_author'   => 1,
				'post_date' => date( 'Y-m-d H:i:s', $formatted_date ),
				'post_title'    => $this->data_set[$key]['Name'],
				'post_name'    => $this->data_set[$key]['Slug'],
				'post_excerpt'  => $this->data_set[$key]['Bio Summary'],
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
		foreach ( $this->data_set as $key => $value ) {
			$formatted_date = strtotime( substr( $this->data_set[$key]['Published On'], 0, -29 ) );
			$post_content = $this->data_set[$key]['Bio Summary'] . "\n";
			$post_content .= 'Researcher URI: ' . $this->data_set[$key]['External Link'] . "\n";
			$post_content .= 'Institution: ' . $this->data_set[$key]['Institution'] . "\n";
			$post_content .= 'Institution URI: ' . $this->data_set[$key]['Institution Link'] . "\n";
			$post_content .= 'SAB Member: ' . ( $this->data_set[$key]['SAB Member?'] ? 'Yes' : "No" );

			$args = array(
				'post_author'   => 1,
				'post_date' => date( 'Y-m-d H:i:s', $formatted_date ),
				'post_title'    => $this->data_set[$key]['Name'],
				'post_name'    => $this->data_set[$key]['Slug'],
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

	// Events:
	public function import_events(): void {
		foreach ( $this->data_set as $key => $value ) {
			// echo $this->data_set[$key]['name'] . "\n";
			$formatted_date        = strtotime( substr($this->data_set[$key]['Created On'], 0, -29 ) );
			// $formatted_event_date  = strtotime( substr($this->data_set[$key]['Start Date/Time'], 0, -29 ) );
			$event_description     = $this->data_set[$key]['Short Description'];
			$is_published          = $this->data_set[$key]['Published On'];

			$post_content = "<h3>Event Time</h3>\n";
			$post_content .= $this->data_set[$key]['Start Date/Time Display'] . "\n";
			$post_content .= !empty( $event_description ) ? $event_description . "\n" : '';
			$post_content .= "<h3>RSVP</h3>\n";
			$post_content .= $this->data_set[$key]['RSVP Link'] . "\n";

			$args = array(
				'post_author'   => 1,
				'post_date' => date('Y-m-d H:i:s', $formatted_date),
				'post_title'    => $this->data_set[$key]['Name'],
				'post_name'    => $this->data_set[$key]['Slug'],
				'post_content'  => $post_content,
				// TODO: Set post category to an argument
				// 'post_category' => array( 9 ),
				'comment_status' => 'closed',
				'ping_status' => 'closed',
				'post_status' => ! empty( $is_published ) ? 'publish' : 'draft',
				'post_type' => 'srf-events',
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

// $researchers = new SRF_Content_Imports( './data/webflow-json/SRF-Researchers.json' );
// $researchers->import_researchers();

$events = new SRF_Content_Imports( './data/webflow-json/SRF-Events.json' );
$events->import_events();