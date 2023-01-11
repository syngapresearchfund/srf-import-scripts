<?php

require_once '../../wp-load.php';

/**
 * Imports CMS content from Webflow API
 */
class SRF_Content_Imports {
	/**
	 * CMS data from the Webflow API (JSON formatted)
	 */
	private $api_data;

	/**
	 * Constructor
	 */
	public function __construct( $url, $type ) {
		if ( ! is_string( $url ) || ! is_string( $type ) ) {
			echo 'Error: The type and URL must be passed in as a string!';
			return; // exit early
		}

		// $this->api_data = json_decode( file_get_contents( $url ), true );
		// $data_items     = json_decode( file_get_contents( $url ), true );
		// $this->api_data = $data_items['items'];

		// Retrieve the CMS data from the Webflow API.
		$response           = wp_remote_get( $url );
		$body               = wp_remote_retrieve_body( $response );
		$formatted_response = json_decode( $body, true );
		$this->api_data     = $formatted_response['items'];
		// $this->api_data = $body['items'];

		// var_dump( $this->api_data );

		switch ( $type ) {
			case 'posts':
				$this->import_posts();
				break;
			case 'post categories':
				$this->import_post_categories();
				break;
			case 'warriors':
				$this->import_warriors();
				break;
			case 'team':
				$this->import_team();
				break;
			case 'webinars':
				$this->import_webinars();
				break;
			case 'researchers':
				$this->import_researchers();
				break;
			case 'events':
				$this->import_events();
				break;
			default:
				echo 'No matching type found. Please try again.';
		}
	}

	/**
	 * Sets Featured Images for Posts
	 *
	 * See https://www.wpexplorer.com/wordpress-featured-image-url/
	 */
	public function generate_featured_image( $image_url, $post_id ) {
		$upload_dir = wp_upload_dir();
		$image_data = file_get_contents( $image_url );
		$filename   = basename( $image_url );

		// Check folder permission and define file location.
		if ( wp_mkdir_p( $upload_dir['path'] ) ) {
			$file = $upload_dir['path'] . '/' . $filename;
		} else {
			$file = $upload_dir['basedir'] . '/' . $filename;
		}

		// Create the image  file on the server.
		file_put_contents( $file, $image_data );

		// Check image file type.
		$wp_filetype = wp_check_filetype( $filename, null );

		// Set attachment data.
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => sanitize_file_name( $filename ),
			'post_content'   => '',
			'post_status'    => 'inherit',

		);
		// Create the attachment.
		$attach_id = wp_insert_attachment( $attachment, $file, $post_id );

		// Include image.php from Core.
		require_once ABSPATH . 'wp-admin/includes/image.php';

		// Define attachment metadata.
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );

		// Assign metadata to attachment.
		wp_update_attachment_metadata( $attach_id, $attach_data );

		// And finally assign featured image to post.
		set_post_thumbnail( $post_id, $attach_id );
	}

	/**
	 * Imports Post Categories from Webflow API
	 */
	public function import_post_categories() : void {
		foreach ( $this->api_data as $key => $value ) {
			// echo $this->api_data[$key]['Name'] . "\n";
			wp_insert_term(
				$this->api_data[ $key ]['name'],
				'category',
				array(
					'description' => $this->api_data[ $key ]['description'],
					'slug'        => $this->api_data[ $key ]['slug'],
				)
			);
		}
	}

	/**
	 * Imports Posts from Webflow API
	 */
	public function import_posts() : void {
		foreach ( $this->api_data as $key => $value ) {
			// echo $this->api_data[$key]['Name'] . "\n";
			$data = $this->api_data[ $key ];
			// $formatted_date = strtotime( substr( $data['published-on'], 0, -29 ) );
			$formatted_date     = strtotime( $data['published-on'] );
			$featured_image_dir = 'images/blog/' . $data['slug'];
			$featured_image     = is_dir( $featured_image_dir ) ? scandir( $featured_image_dir ) : '';

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

			// wp_set_post_categories( $item_id, 8 );
		}
	}

	/**
	 * Imports Warriors from Webflow API
	 */
	public function import_warriors() : void {
		foreach ( $this->api_data as $key => $value ) {
			$data = $this->api_data[ $key ];
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

	/**
	 * Imports Team from Webflow API
	 */
	public function import_team() : void {
		foreach ( $this->api_data as $key => $value ) {
			$data = $this->api_data[ $key ];
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

	/**
	 * Imports Researchers from Webflow API
	 */
	public function import_researchers() : void {
		foreach ( $this->api_data as $key => $value ) {
			$data = $this->api_data[ $key ];
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
		foreach ( $this->api_data as $key => $value ) {
			// echo $this->api_data[$key]['name'] . "\n";
			$data = $this->api_data[ $key ];
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
		foreach ( $this->api_data as $key => $value ) {
			// echo $this->api_data[$key]['name'] . "\n";
			$data = $this->api_data[ $key ];
			// $formatted_date    = strtotime( substr($data['created-on'], 0, -29 ) );
			$formatted_date    = strtotime( $data['created-on'] );
			$event_description = isset( $data['description'] ) ? $data['description'] : '';
			$is_published      = $data['published-on'];

			$post_content  = "<h3>Event Time</h3>\n";
			$post_content .= ! empty( $data['date-time'] ) ? $data['date-time'] . "\n" : '';
			$post_content .= ! empty( $event_description ) ? "<h3>Description</h3>\n" . $event_description . "\n" : '';
			$post_content .= isset( $data['webinar-registration-link'] ) ? '<h3>RSVP</h3><a href="' . $data['webinar-registration-link'] . '">' . $data['webinar-registration-link'] . '</a>' : '';

			$args = array(
				'post_author'    => 1,
				'post_date'      => date( 'Y-m-d H:i:s', $formatted_date ),
				'post_title'     => $data['name'],
				'post_name'      => $data['slug'],
				'post_content'   => $post_content,
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'post_status'    => ! empty( $is_published ) ? 'publish' : 'draft',
				'post_type'      => 'srf-resources',
			);

			wp_insert_post( $args );
		}
	}
}
