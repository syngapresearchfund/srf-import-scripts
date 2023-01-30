<?php

require_once '../../wp-load.php';

/**
 * Imports CMS content from Webflow API
 */
class SRF_Content_Imports {
	/**
	 * CMS data from the Webflow API (JSON formatted).
	 */
	private $api_data;

	/**
	 * Content type for importing into WordPress.
	 */
	private $content_type;

	/**
	 * Timestamp for when to upload the latest set of data.
	 */
	private $since_timestamp;

	/**
	 * Constructor.
	 *
	 * @param string $url Path to API endpoint.
	 * @param string $type Post type to parse.
	 * @param string $date Date since last import. Passed in as string with '1970-01-01' format.
	 *
	 * @since 2021-10-03
	 */
	public function __construct( $url, $type, $date = '' ) {
		if ( ! is_string( $url ) || ! is_string( $type ) ) {
			echo 'Error: The type and URL must be passed in as a string!';
			return; // exit early.
		}

		// Set content type.
		$this->content_type = $type;

		// Retrieve the CMS data from a locally downloaded JSON file.
		// $this->api_data = json_decode( file_get_contents( $url ), true );
		// $data_items     = json_decode( file_get_contents( $url ), true );
		// $this->api_data = $data_items['items'];

		// Retrieve the CMS data from the Webflow API.
		$response           = wp_remote_get( $url );
		$body               = wp_remote_retrieve_body( $response );
		$formatted_response = json_decode( $body, true );
		$this->api_data     = $formatted_response['items'];
		// $this->api_data = $body['items'];

		if ( ! empty( $date ) ) {
			$date_time             = new DateTime( $date );
			$this->since_timestamp = $date_time->format( 'U' );
		}

		// Compare with a specific date when needing to only import the latest items.
		// TODO: Compare the $since_timestamp to the item date in the loop for each method.
		// Determine if this needs to go here in the constructor OR directly on the method. - It should go on the method in the iterators.
		// if ( $item_date <= 1638921600 ) {
		// return;
		// }

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
			case 'grants':
				$this->import_grants();
				break;
			case 'movies':
				$this->import_movies();
				break;
			default:
				echo 'No matching type found. Please try again.';
		}
	}

	/**
	 * Uploads images as attachments. Sets Featured Images for Posts.
	 *
	 * See https://www.wpexplorer.com/wordpress-featured-image-url/
	 */
	public function upload_post_images( $image_url, $post_id, $set_featured = true ) {
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

		// TODO: May be able to conditionally omit this for uploading gallery images to Warriors. Will need to update with some conditional logic and params to pass in for Warrior uploads (we already have the content type so we can use that).
		// And finally assign featured image to post.
		if ( $set_featured ) {
			set_post_thumbnail( $post_id, $attach_id );
		}
	}

	/**
	 * Imports Post Categories from Webflow API
	 */
	public function import_post_categories() : void {
		foreach ( $this->api_data as $key => $value ) {
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
			$featured_image_dir = 'images/latest/blog/' . $data['slug'];
			$featured_image     = is_dir( $featured_image_dir ) ? scandir( $featured_image_dir ) : array();

			// Compare with a specific date when needing to only import the latest items.
			if ( isset( $this->since_timestamp ) && $formatted_date <= $this->since_timestamp ) {
				return;
			}

			$args = array(
				'post_author'    => 1,
				'post_date'      => gmdate( 'Y-m-d H:i:s', $formatted_date ),
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
				$this->upload_post_images( $featured_image_dir . '/' . $featured_image[2], $item_id );
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
			$featured_image_dir = 'images/latest/warriors/featured-images/' . $data['slug'];
			$featured_image     = is_dir( $featured_image_dir ) ? scandir( $featured_image_dir ) : array();
			$gallery_dir        = 'images/latest/warriors/galleries/' . $data['slug'];
			$gallery_images     = is_dir( $gallery_dir ) ? scandir( $gallery_dir ) : array();

			// Compare with a specific date when needing to only import the latest items.
			if ( isset( $this->since_timestamp ) && $formatted_date <= $this->since_timestamp ) {
				return;
			}

			$post_content  = '<strong>' . $data['age'] . "</strong>\n";
			$post_content .= '<strong>' . $data['location'] . "</strong>\n";
			$post_content .= isset( $data['full-story'] ) ? $data['full-story'] . "\n" : '';

			$args = array(
				'post_author'    => 1,
				'post_date'      => gmdate( 'Y-m-d H:i:s', $formatted_date ),
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
				$this->upload_post_images( $featured_image_dir . '/' . $featured_image[2], $item_id );
			}
			if ( ! empty( $gallery_images ) ) {
				for ( $i = 2, $ii = count( $gallery_images ); $i < $ii; $i++ ) {
					$this->upload_post_images( $gallery_dir . '/' . $gallery_images[ $i ], $item_id, false );
				}
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
			$formatted_date     = isset( $data['published-on'] ) ? strtotime( $data['published-on'] ) : strtotime( $data['created-on'] );
			$featured_image_dir = 'images/latest/team/' . $data['slug'];
			$featured_image     = is_dir( $featured_image_dir ) ? scandir( $featured_image_dir ) : array();

			// Compare with a specific date when needing to only import the latest items.
			if ( isset( $this->since_timestamp ) && ( $formatted_date <= $this->since_timestamp ) ) {
				return;
			}

			$post_content  = isset( $data['job-title'] ) ? '<h2>' . $data['job-title'] . "</h2>\n" : '';
			$post_content .= $data['bio'] . "\n";
			$post_content .= isset( $data['email'] ) ? '<strong>Email:</strong> <a href="' . $data['email'] . '">' . $data['email'] . "</a>\n" : '';
			$post_content .= isset( $data['twitter-link'] ) ? '<strong>Twitter:</strong> <a href="' . $data['twitter-link'] . '">' . $data['twitter-link'] . "</a>\n" : '';
			$post_content .= isset( $data['facebook-link'] ) ? '<strong>Facebook:</strong> <a href="' . $data['facebook-link'] . '">' . $data['facebook-link'] . "</a>\n" : '';
			$post_content .= isset( $data['linkedin'] ) ? '<strong>LinkedIn:</strong> <a href="' . $data['linkedin'] . '">' . $data['linkedin'] . "</a>\n" : '';

			$args = array(
				'post_author'    => 1,
				'post_date'      => gmdate( 'Y-m-d H:i:s', $formatted_date ),
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
				$this->upload_post_images( $featured_image_dir . '/' . $featured_image[2], $item_id );
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
			$featured_image_dir = 'images/latest/researchers/' . $data['slug'];
			$featured_image     = is_dir( $featured_image_dir ) ? scandir( $featured_image_dir ) : array();

			// Compare with a specific date when needing to only import the latest items.
			if ( isset( $this->since_timestamp ) && $formatted_date <= $this->since_timestamp ) {
				return;
			}

			$post_content  = isset( $data['bio-summary'] ) ? $data['bio-summary'] . "\n" : '';
			$post_content .= isset( $data['external-link'] ) ? '<strong>Website:</strong> <a href="' . $data['external-link'] . '">' . $data['external-link'] . "</a>\n" : '';
			$post_content .= isset( $data['institution'] ) ? '<strong>Institution:</strong> ' . $data['institution'] . "\n" : '';
			$post_content .= isset( $data['institution-link'] ) ? '<strong>Institution Website:</strong>  <a href="' . $data['institution-link'] . '">' . $data['institution-link'] . "</a>\n" : '';

			$args = array(
				'post_author'    => 1,
				'post_date'      => gmdate( 'Y-m-d H:i:s', $formatted_date ),
				'post_title'     => $data['name'],
				'post_name'      => $data['slug'],
				'post_content'   => $post_content,
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'post_status'    => 'publish',
				'post_type'      => 'srf-team',
			);

			$item_id = wp_insert_post( $args );

			// TODO: Continue investigating why it is not working to set categories.
			// if ( $data['sab-member'] ) {
			// wp_set_object_terms( $item_id, array( 52 ), 'category' );
			// }
			// if ( $data['cab-member'] ) {
			// wp_set_object_terms( $item_id, array( 51 ), 'category' );
			// }

			if ( ! empty( $featured_image ) ) {
				$this->upload_post_images( $featured_image_dir . '/' . $featured_image[2], $item_id );
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

			// Compare with a specific date when needing to only import the latest items.
			if ( isset( $this->since_timestamp ) && $formatted_date <= $this->since_timestamp ) {
				return;
			}

			$featured_image_dir = 'images/latest/events/' . $data['slug'];
			$featured_image     = is_dir( $featured_image_dir ) ? scandir( $featured_image_dir ) : array();

			$post_content  = "<h3>Event Time</h3>\n";
			$post_content .= $data['start-date-time-display'] . "\n";
			$post_content .= ! empty( $event_description ) ? "<h3>Description</h3>\n" . $event_description . "\n" : '';
			$post_content .= isset( $data['rsvp-link'] ) ? '<h3>RSVP</h3><a href="' . $data['rsvp-link'] . '">' . $data['rsvp-link'] . '</a>' : '';

			$args = array(
				'post_author'    => 1,
				'post_date'      => gmdate( 'Y-m-d H:i:s', $formatted_date ),
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
				$this->upload_post_images( $featured_image_dir . '/' . $featured_image[2], $item_id );
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
			$formatted_date       = strtotime( $data['created-on'] );
			$formatted_event_date = isset( $data['date-time'] ) ? strtotime( $data['date-time'] ) : '';
			$event_description    = isset( $data['description'] ) ? $data['description'] : '';
			$is_published         = $data['published-on'];

			// Compare with a specific date when needing to only import the latest items.
			if ( isset( $this->since_timestamp ) && $formatted_date <= $this->since_timestamp ) {
				return;
			}

			$post_content  = "<h3>Event Time</h3>\n";
			$post_content .= ! empty( $formatted_event_date ) ? date( 'F j, Y \a\t g:i a', $formatted_event_date ) . "\n" : '';
			$post_content .= ! empty( $event_description ) ? "<h3>Description</h3>\n" . $event_description . "\n" : '';
			$post_content .= isset( $data['webinar-registration-link'] ) ? '<h3>RSVP</h3><a href="' . $data['webinar-registration-link'] . '">' . $data['webinar-registration-link'] . '</a>' : '';

			$args = array(
				'post_author'    => 1,
				'post_date'      => gmdate( 'Y-m-d H:i:s', $formatted_date ),
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

	/**
	 * Imports Grants data
	 */
	public function import_grants(): void {
		foreach ( $this->api_data as $key => $value ) {
			// echo $this->api_data[$key]['name'] . "\n";
			$data = $this->api_data[ $key ];
			// $formatted_date    = strtotime( substr($data['created-on'], 0, -29 ) );
			$formatted_date    = strtotime( $data['created-on'] );
			$grant_number      = isset( $data['grant-number'] ) ? $data['grant-number'] : '';
			$funding_amount    = isset( $data['funding-amount-display'] ) ? $data['funding-amount-display'] : '';
			$percent_dispersed = isset( $data['percent-disbursed'] ) ? strval( $data['percent-disbursed'] ) : '';
			$is_published      = $data['published-on'];

			// Compare with a specific date when needing to only import the latest items.
			if ( isset( $this->since_timestamp ) && $formatted_date <= $this->since_timestamp ) {
				return;
			}

			$featured_image_dir = 'images/latest/grants/' . $data['slug'];
			$featured_image     = is_dir( $featured_image_dir ) ? scandir( $featured_image_dir ) : array();
			$grant_pdf_dir      = 'documents/latest/grants/' . $data['slug'];
			$grant_pdf          = is_dir( $grant_pdf_dir ) ? scandir( $grant_pdf_dir ) : array();

			$post_content  = isset( $data['purpose'] ) ? $data['purpose'] . "\n" : '';
			$post_content .= '<strong>Grant Number:</strong> ' . $grant_number . "\n";
			$post_content .= '<strong>Funding Amount:</strong> ' . $funding_amount . "\n";
			$post_content .= '<strong>Percentage Dispersed:</strong> ' . $percent_dispersed . "% Dispersed\n";
			$post_content .= '<strong>Grant Status:</strong> Signed & active';

			$args = array(
				'post_author'    => 1,
				'post_date'      => gmdate( 'Y-m-d H:i:s', $formatted_date ),
				'post_title'     => $data['name'],
				'post_name'      => $data['slug'],
				'post_content'   => $post_content,
				'post_excerpt'   => ( ! empty( $grant_number ) && ! empty( $funding_amount ) ) ? $grant_number . ' – ' . $funding_amount : '',
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'post_status'    => ! empty( $is_published ) ? 'publish' : 'draft',
				'tax_input'      => array( 'srf-resources-category' => '59' ),
				'post_type'      => 'srf-resources',
			);

			$item_id = wp_insert_post( $args );

			if ( ! empty( $featured_image ) ) {
				$this->upload_post_images( $featured_image_dir . '/' . $featured_image[2], $item_id );
			}
			if ( ! empty( $grant_pdf ) ) {
				$this->upload_post_images( $grant_pdf_dir . '/' . $grant_pdf[2], $item_id, false );
			}
		}
	}

	/**
	 * Imports Movies data
	 */
	public function import_movies(): void {
		foreach ( $this->api_data as $key => $value ) {
			// echo $this->api_data[$key]['name'] . "\n";
			$data = $this->api_data[ $key ];
			// $formatted_date    = strtotime( substr($data['created-on'], 0, -29 ) );
			$formatted_date = strtotime( $data['created-on'] );
			$is_published   = $data['published-on'];

			// Compare with a specific date when needing to only import the latest items.
			if ( isset( $this->since_timestamp ) && $formatted_date <= $this->since_timestamp ) {
				return;
			}

			$featured_image_dir = 'images/latest/movies/' . $data['slug'];
			$featured_image     = is_dir( $featured_image_dir ) ? scandir( $featured_image_dir ) : array();
			$grant_pdf_dir      = 'documents/latest/grants/' . $data['slug'];
			$grant_pdf          = is_dir( $grant_pdf_dir ) ? scandir( $grant_pdf_dir ) : array();

			$post_content  = isset( $data['top-rich-text'] ) ? $data['top-rich-text'] . "\n" : '';
			$post_content .= isset( $data['video-link']['url'] ) ? $data['video-link']['url'] . "\n" : '';

			$args = array(
				'post_author'    => 1,
				'post_date'      => gmdate( 'Y-m-d H:i:s', $formatted_date ),
				'post_title'     => $data['name'],
				'post_name'      => $data['slug'],
				'post_content'   => $post_content,
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'post_status'    => ! empty( $is_published ) ? 'publish' : 'draft',
				'post_type'      => 'srf-resources',
			);

			$item_id = wp_insert_post( $args );

			// wp_set_object_terms( $item_id, 'movies', 'srf-resources', true ); // Didn't work :(

			if ( ! empty( $featured_image ) ) {
				$this->upload_post_images( $featured_image_dir . '/' . $featured_image[2], $item_id );
			}
			if ( ! empty( $grant_pdf ) ) {
				$this->upload_post_images( $grant_pdf_dir . '/' . $grant_pdf[2], $item_id, false );
			}
		}
	}
}
