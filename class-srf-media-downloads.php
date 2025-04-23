<?php
/* phpcs:ignoreFile -- auto-generated build file - OK to ignore linting. */

class SRF_Media_Downloads {
	private $data_set;
	private $data_key;
	private $output_path;
	private $since_timestamp; // timestamp format - e.g., 1638921600
	private $is_gallery;

	/**
	 * Constructor.
	 * 
	 * @param string $data_path Path to API endpoint.
	 * @param string $data_key CMS content type for API endpoint path.
	 * @param string $output_path Local location to download images to.
	 * @param string $date Date since last import. Passed in as string with '1970-01-01' format.
	 * @param string $is_gallery Is media a part of an image gallery or not.
	 *
	 * @since 2021-10-03
	 */
	public function __construct( $data_path, $data_key, $output_path, $date = '', $is_gallery = false ) {
		if ( ! is_string( $data_path ) ) {
			echo 'Error: The data path must be passed in as a string!';
			return; // exit early.
		}
		if ( ! is_string( $data_key ) ) {
			echo 'Error: The data key must be passed in as a string!';
			return; // exit early.
		}
		if ( ! is_string( $output_path ) ) {
			echo 'Error: The output path must be passed in as a string!';
			return; // exit early.
		}

		if ( ! ini_get( 'allow_url_fopen' ) ) {
			echo 'Error: allow_url_fopen is not enabled in your environment!';
			return; // exit early.
		}

		if ( ! empty( $date ) ) {
			$date_time = new DateTime($date); 
			$this->since_timestamp = $date_time->format('U');
		}

		// $this->data_set = json_decode( file_get_contents( $data_path ), true );
		$data_items        = json_decode( file_get_contents( $data_path ), true );
		$this->data_set    = $data_items['items'];
		$this->data_key    = $data_key;
		$this->output_path = $output_path;
		$this->is_gallery  = $is_gallery;

		$this->download_files();
	}

	/**
	 * Download Webflow files from URL.
	 *
	 * @since 2021-10-03
	 */
	public function download_files() : void {
		foreach ( $this->data_set as $key => $value ) {
			if ( ! isset( $this->data_set[ $key ][ $this->data_key ] ) ) {
				echo "The image path for this item is empty.\n";
				continue;
			}

			$item_slug = $this->data_set[ $key ]['slug'];
			$item_date = strtotime( $this->data_set[ $key ]['created-on'] );
			$file_path = $this->is_gallery ? $this->data_set[ $key ][ $this->data_key ] : $this->data_set[ $key ][ $this->data_key ]['url'];

			// Compare with a specific date when needing to only import the latest items.
			if ( $item_date <= $this->since_timestamp ) {
				return;
			}

			mkdir( "$this->output_path/$item_slug", 0777, true );

			if ( $this->is_gallery ) {
				$i = 0;

				foreach ( $file_path as $file_obj ) {
					$file = $file_obj['url'];

					if ( empty( $file ) ) {
						echo "The image gallery for $item_slug is empty.\n";
						continue;
					}

					$file_ext = substr( $file, -4 );

					file_put_contents( "$this->output_path/$item_slug/$item_slug-$i$file_ext", file_get_contents( $file ) );

					echo "Success! $item_slug-$i$file_ext was successfully downloaded.\n";

					$i++;
				}
			} else {
				$file_ext = substr( $file_path, -4 );

				file_put_contents( "$this->output_path/$item_slug/$item_slug$file_ext", file_get_contents( $file_path ) );

				echo "Success! The file for $item_slug has been downloaded.\n";
			}
		}

		echo 'Operation complete: All files have been downloaded to the proper directory. GREAT SUCCESS!';
	}
}
