<?php

require_once './wp-load.php';

/**
 * Imports podcast content from RSS feed.
 */
class SRF_RSS_Imports {
	/**
	 * Data from the provided RSS Feed URL.
	 */
	private SimplePie|WP_Error $feed_data;

	/**
	 * Content type for importing into WordPress.
	 */
	private string $content_type;

	/**
	 * Content type for importing into WordPress.
	 */
	private mixed $content_taxonomy;

	/**
	 * Content type for importing into WordPress.
	 */
	private mixed $content_terms;

	/**
	 * Timestamp for when to upload the latest set of data.
	 */
	private string $since_timestamp;

	/**
	 * Constructor.
	 *
	 * @param string $url Path to API endpoint.
	 * @param string $type Post type to parse.
	 * @param string $date Date since last import. Passed in as string with '1970-01-01' format.
	 *
	 * @throws DateMalformedStringException
	 * @since 2021-10-03
	 */
	public function __construct( string $url, string $type, $tax = '', $terms = '', string $date = '' ) {
//		if ( ! is_string( $url ) || ! is_string( $type ) ) {
//			echo 'Error: The type and URL must be passed in as a string!';
//
//			return; // exit early.
//		}

		// Set content type.
		$this->content_type     = $type;
		$this->content_taxonomy = $tax;
		$this->content_terms    = $terms;

		// Retrieve the RSS feed data from the provided URL.
		$this->feed_data = fetch_feed( $url );

		if ( ! empty( $date ) ) {
			$date_time             = new DateTime( $date );
			$this->since_timestamp = $date_time->format( 'U' );
		}

		$this->import_posts();
	}

	/**
	 * Imports Posts from RSS Feed URL
	 */
	public function import_posts(): void {
		if ( is_wp_error( $this->feed_data ) ) {
			echo 'Error: ' . $this->feed_data->get_error_message();

			return;
		}
		$max_items = $this->feed_data->get_item_quantity();
		$rss_items = $this->feed_data->get_items( 0, $max_items );

		foreach ( $rss_items as $item ) {
//			$formatted_date     = strtotime( $data['published-on'] );
//			$featured_image_dir = 'images/latest/blog/' . $data['slug'];
//			$featured_image     = is_dir( $featured_image_dir ) ? scandir( $featured_image_dir ) : array();

			$post_title   = $item->get_title();
			$post_content = $item->get_content();
			$post_date    = $item->get_date( 'Y-m-d H:i:s' );

			// Get the enclosure (if it exists)
			$enclosure = $item->get_enclosure();

			if ( $enclosure ) {
				$video_url  = $enclosure->get_link(); // Extract the video URL from the enclosure
				$media_type = $enclosure->get_type(); // Get the MIME type of the enclosure (e.g., video/mp4)

				// Check if the enclosure is a video file
				if ( str_contains( $media_type, 'video' ) ) {
					echo "Video URL: " . $video_url . "\n";
				}
			}

			// Compare with a specific date when needing to only import the latest items.
			if ( isset( $this->since_timestamp ) && $post_date <= $this->since_timestamp ) {
				return;
			}
			// Check if the post already exists to avoid duplicates
//			if ( ! post_exists( $post_title ) ) {
//				return;
//			}

			$args = array(
				'post_title'   => $post_title,
				'post_content' => ! empty( $video_url ) ? $video_url : '' . $post_content,
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_date'    => $post_date,
				'post_type'    => $this->content_type,
			);

			$item_id = wp_insert_post( $args );

			if ( ! empty( $this->content_terms ) ) {
				wp_set_object_terms( $item_id, $this->content_terms, $this->content_taxonomy );
			}

			// TODO: See if we need to add featured images to the posts.
//			if ( ! empty( $featured_image ) ) {
//				$this->upload_post_images( $featured_image_dir . '/' . $featured_image[2], $item_id );
//			}

			// We likely don't need to Set the post categories.
			// wp_set_post_categories( $item_id, 8 );
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

}

$syngap10_posts = new SRF_RSS_Imports( 'https://feed.podbean.com/syngap10/feed.xml', 'srf-podcasts', 'srf-podcasts-category', 'syngap10' );
