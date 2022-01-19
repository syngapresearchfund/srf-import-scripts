<?php

require_once '../wp-load.php';

class SRF_Content_Imports {
	private $data_set;

	public function __construct( $data_path ) {
		if ( ! is_string( $data_path ) ) {
			echo 'Error: The data path must be passed in as a string!';
			return; // exit early
		}

		// $this->data_set = json_decode( file_get_contents( $data_path ), true );
		$data_items     = json_decode( file_get_contents( $data_path ), true );
		$this->data_set = $data_items['items'];
	}

	// Post Featured Images:
	// See https://www.wpexplorer.com/wordpress-featured-image-url/.
	public function generate_featured_image( $image_url, $post_id  ) {
		$upload_dir = wp_upload_dir();
		$image_data = file_get_contents( $image_url );
		$filename = basename( $image_url );

		// Check folder permission and define file location
		if ( wp_mkdir_p( $upload_dir['path'] ) ) {
		  $file = $upload_dir['path'] . '/' . $filename;
		} else {
		  $file = $upload_dir['basedir'] . '/' . $filename;
		}

		// Create the image  file on the server
		file_put_contents( $file, $image_data );
	
		// Check image file type
		$wp_filetype = wp_check_filetype( $filename, null );

		// Set attachment data
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => sanitize_file_name( $filename ),
			'post_content' => '',
			'post_status' => 'inherit'
		
		);
		// Create the attachment
		$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
		
		// Include image.php from Core
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		// Define attachment metadata
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );

		// Assign metadata to attachment
		wp_update_attachment_metadata( $attach_id, $attach_data );

		// And finally assign featured image to post
		set_post_thumbnail( $post_id, $attach_id );
	}

	// Post Categories:
	public function import_post_categories() : void {
		foreach ( $this->data_set as $key => $value ) {
			// echo $this->data_set[$key]['Name'] . "\n";
			wp_insert_term(
				$this->data_set[ $key ]['name'],
				'category',
				array(
					'description' => $this->data_set[ $key ]['description'],
					'slug'        => $this->data_set[ $key ]['slug'],
				)
			);
		}
	}

	// Posts:
	public function import_posts() : void {
		foreach ( $this->data_set as $key => $value ) {
			// echo $this->data_set[$key]['Name'] . "\n";
			$data = $this->data_set[ $key ];
			// $formatted_date = strtotime( substr( $data['published-on'], 0, -29 ) );
			$formatted_date = strtotime( $data['published-on'] );
			$featured_image_dir = 'images/blog/' . $data['slug'];
			$featured_image = is_dir( $featured_image_dir ) ? scandir( $featured_image_dir ) : '';

			$args = array(
				'post_author'    => 1,
				'post_date'      => date( 'Y-m-d H:i:s', $formatted_date ),
				'post_title'     => $data['name'],
				'post_name'      => $data['slug'],
				'post_content'   => $data['post-body'],
				// 'post_category' => array( 5 ),
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'post_status'    => 'publish',
				'post_type'      => 'post',
			);

			$item_id = wp_insert_post( $args );

			if ( ! empty( $featured_image ) ) {
				$this->generate_featured_image( 'images/blog/' . $data['slug'] . '/' . $featured_image[2], $item_id );
			}

			wp_set_post_categories( $item_id, 8 );
		}
	}

	// Warriors:
	public function import_warriors() : void {
		foreach ( $this->data_set as $key => $value ) {
			$data = $this->data_set[ $key ];
			// $formatted_date = strtotime( substr( $data['publication-date'], 0, -29 ) );
			$formatted_date     = strtotime( $data['publication-date'] );
			$featured_image_dir = 'images/warriors/current/featured-images/' . $data['slug'];
			$featured_image     = is_dir( $featured_image_dir ) ? scandir( $featured_image_dir ) : '';

			$post_content  = '<strong>' . $data['age'] . "</strong>\n";
			$post_content .= '<strong>' . $data['location'] . "</strong>\n";
			$post_content .= isset( $data['full-story'] ) ? $data['full-story'] . "\n" : '';

			$args = array(
				'post_author'    => 1,
				'post_date'      => date( 'Y-m-d H:i:s', $formatted_date ),
				'post_title'     => $data['name'],
				'post_name'      => $data['slug'],
				'post_content'   => $post_content,
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'post_status'    => 'publish',
				'post_type'      => 'srf-warriors',
			);

			$item_id = wp_insert_post( $args );

			if ( ! empty( $featured_image ) ) {
				$this->generate_featured_image( 'images/warriors/current/featured-images/' . $data['slug'] . '/' . $featured_image[2], $item_id );
			}
		}
	}

	// Team:
	public function import_team() : void {
		foreach ( $this->data_set as $key => $value ) {
			$data = $this->data_set[ $key ];
			// $formatted_date = strtotime( substr( $data['published-on'], 0, -29 ) );
			$formatted_date     = strtotime( $data['published-on'] );
			$featured_image_dir = 'images/team/' . $data['slug'];
			$featured_image     = is_dir( $featured_image_dir ) ? scandir( $featured_image_dir ) : '';

			$post_content  = isset( $data['job-title'] ) ? '<h2>' . $data['job-title'] . "</h2>\n" : '';
			$post_content .= $data['bio'] . "\n";
			$post_content .= isset( $data['email'] ) ? '<strong>Email:</strong> <a href="' . $data['email'] . '">' . $data['email'] . "</a>\n" : '';
			$post_content .= isset( $data['twitter-link'] ) ? '<strong>Twitter:</strong> <a href="' . $data['twitter-link'] . '">' . $data['twitter-link'] . "</a>\n" : '';
			$post_content .= isset( $data['facebook-link'] ) ? '<strong>Facebook:</strong> <a href="' . $data['facebook-link'] . '">' . $data['facebook-link'] . "</a>\n" : '';
			$post_content .= isset( $data['linkedin'] ) ? '<strong>LinkedIn:</strong> <a href="' . $data['linkedin'] . '">' . $data['linkedin'] . "</a>\n" : '';

			$args = array(
				'post_author'    => 1,
				'post_date'      => date( 'Y-m-d H:i:s', $formatted_date ),
				'post_title'     => $data['name'],
				'post_name'      => $data['slug'],
				'post_excerpt'   => isset( $data['bio-summary'] ) ?: '',
				'post_content'   => $post_content,
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'post_status'    => 'publish',
				'post_type'      => 'srf-team',
			);

			$item_id = wp_insert_post( $args );

			if ( ! empty( $featured_image ) ) {
				$this->generate_featured_image( 'images/team/' . $data['slug'] . '/' . $featured_image[2], $item_id );
			}
		}
	}

	// Researchers:
	public function import_researchers() : void {
		foreach ( $this->data_set as $key => $value ) {
			$data = $this->data_set[ $key ];
			// $formatted_date = strtotime( substr( $data['published-on'], 0, -29 ) );
			$formatted_date     = strtotime( $data['published-on'] );
			$featured_image_dir = 'images/researchers/' . $data['slug'];
			$featured_image     = is_dir( $featured_image_dir ) ? scandir( $featured_image_dir ) : '';

			$post_content  = isset( $data['bio-summary'] ) ? $data['bio-summary'] . "\n" : '';
			$post_content .= isset( $data['external-link'] ) ? '<strong>Website:</strong> <a href="' . $data['external-link'] . '">' . $data['external-link'] . "</a>\n" : '';
			$post_content .= isset( $data['institution'] ) ? '<strong>Institution:</strong> ' . $data['institution'] . "\n" : '';
			$post_content .= isset( $data['institution-link'] ) ? '<strong>Institution Website:</stong>  <a href="' . $data['institution-link'] . '">' . $data['institution-link'] . "</a>\n" : '';
			$post_content .= '<strong>SAB Member:</strong> ' . ( $data['sab-member'] ? 'Yes' : 'No' );

			$args = array(
				'post_author'    => 1,
				'post_date'      => date( 'Y-m-d H:i:s', $formatted_date ),
				'post_title'     => $data['name'],
				'post_name'      => $data['slug'],
				'post_content'   => $post_content,
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'post_status'    => 'publish',
				'post_type'      => 'srf-team',
			);

			$item_id = wp_insert_post( $args );

			if ( ! empty( $featured_image ) ) {
				$this->generate_featured_image( 'images/researchers/' . $data['slug'] . '/' . $featured_image[2], $item_id );
			}
		}
	}

	/**
	 * Imports Events CPT data
	 */
	public function import_events(): void {
		foreach ( $this->data_set as $key => $value ) {
			// echo $this->data_set[$key]['name'] . "\n";
			$data = $this->data_set[ $key ];
			// $formatted_date    = strtotime( substr($data['created-on'], 0, -29 ) );
			$formatted_date    = strtotime( $data['created-on'] );
			$event_description = isset( $data['short-description'] ) ? $data['short-description'] : '';
			$is_published      = $data['published-on'];

			$featured_image_dir = 'images/events/' . $data['slug'];
			$featured_image     = is_dir( $featured_image_dir ) ? scandir( $featured_image_dir ) : '';

			$post_content  = "<h3>Event Time</h3>\n";
			$post_content .= $data['start-date-time-display'] . "\n";
			$post_content .= ! empty( $event_description ) ? "<h3>Description</h3>\n" . $event_description . "\n" : '';
			$post_content .= isset( $data['rsvp-link'] ) ? '<h3>RSVP</h3><a href="' . $data['rsvp-link'] . '">' . $data['rsvp-link'] . '</a>' : '';

			$args = array(
				'post_author'    => 1,
				'post_date'      => date( 'Y-m-d H:i:s', $formatted_date ),
				'post_title'     => $data['name'],
				'post_name'      => $data['slug'],
				'post_content'   => $post_content,
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'post_status'    => ! empty( $is_published ) ? 'publish' : 'draft',
				'post_type'      => 'srf-events',
			);

			$item_id = wp_insert_post( $args );

			if ( ! empty( $featured_image ) ) {
				$this->generate_featured_image( 'images/events/' . $data['slug'] . '/' . $featured_image[2], $item_id );
			}
		}
	}

	/**
	 * Imports Webinars CPT data
	 * 
	 * NOTE: This will probably not end up being used as a CPT. Instead
	 * we will use a taxonomy under events and have the various event types there. We
	 * can then split things up in the same way we have split up the Team CPT.
	 * 
	 * Taxonomies: Fundraisers, Live Events (?), Webinars, anything else? 
	 */
	public function import_webinars(): void {
		foreach ( $this->data_set as $key => $value ) {
			// echo $this->data_set[$key]['name'] . "\n";
			$data = $this->data_set[ $key ];
			// $formatted_date    = strtotime( substr($data['created-on'], 0, -29 ) );
			$formatted_date    = strtotime( $data['created-on'] );
			$event_description = isset( $data['short-description'] ) ? $data['short-description'] : '';
			$is_published      = $data['published-on'];

			$post_content  = "<h3>Event Time</h3>\n";
			$post_content .= $data['start-date-time-display'] . "\n";
			$post_content .= ! empty( $event_description ) ? "<h3>Description</h3>\n" . $event_description . "\n" : '';
			$post_content .= isset( $data['rsvp-link'] ) ? '<h3>RSVP</h3><a href="' . $data['rsvp-link'] . '">' . $data['rsvp-link'] . '</a>' : '';

			$args = array(
				'post_author'    => 1,
				'post_date'      => date( 'Y-m-d H:i:s', $formatted_date ),
				'post_title'     => $data['name'],
				'post_name'      => $data['slug'],
				'post_content'   => $post_content,
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'post_status'    => ! empty( $is_published ) ? 'publish' : 'draft',
				'post_type'      => 'srf-events',
			);

			wp_insert_post( $args );
		}
	}
}
// $post_categories = new SRF_Content_Imports( './data/webflow-api-data/api-srf-blog-categories.json' );
// $post_categories->import_post_categories();

$posts = new SRF_Content_Imports( './data/webflow-api-data/api-srf-posts-1.json' );
// $posts = new SRF_Content_Imports( './data/webflow-api-data/api-srf-posts-2.json' );
$posts->import_posts();

// $warriors = new SRF_Content_Imports( './data/webflow-api-data/api-srf-warriors-3.json' );
// $warriors = new SRF_Content_Imports( './data/webflow-api-data/api-srf-warriors-4.json' );
// $warriors->import_warriors();

// $team = new SRF_Content_Imports( './data/webflow-api-data/api-srf-team.json' );
// $team->import_team();

// $researchers = new SRF_Content_Imports( './data/webflow-api-data/api-srf-researchers.json' );
// $researchers->import_researchers();

// $events = new SRF_Content_Imports( './data/webflow-api-data/api-srf-events.json' );
// $events->import_events();
