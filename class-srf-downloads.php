<?php

class SRF_Downloads {
	private $data_set;

	public function __construct( $data_path ) {
		if ( ! is_string( $data_path ) ) {
			echo 'Error: The data path must be passed in as a string!';
			return; // exit early
		}

		if( ! ini_get( 'allow_url_fopen' ) ) {
			echo 'Error: allow_url_fopen is not enabled in your environment!';
			return; // exit early
		}

		$this->data_set = json_decode( file_get_contents( $data_path ), true );
	}
	
	/**
	 * Download warrior image gallery.
	 *
	 * @since 2021-10-03
	 */
	public function download_warrior_gallery() : void {
		$data_set = json_decode( file_get_contents( $this->data_path ), true );
	
		foreach ( $data_set as $key => $value ) {
			$warrior_slug = $data_set[$key]['Slug'];
			$image_array = explode( '; ', $data_set[$key]['Image Gallery'] );
			$i = 0;
		
			mkdir( "images/warriors/$warrior_slug", 0777, true );
	
			foreach ( $image_array as $image ) {
				if ( empty( $image ) ) {
					echo "The image gallery for $warrior_slug is empty.\n";
					continue;
				}
	
				$file_ext = substr( $image, -4 );
	
				file_put_contents( "images/warriors/$warrior_slug/$warrior_slug-$i$file_ext", file_get_contents( $image ) );
	
				echo "Success! $warrior_slug-$i$file_ext was successfully downloaded.\n";
	
				$i++;
			}
	
			echo "All images for $warrior_slug have been downloaded.\n";
		}
	
		echo "Operation complete: All Warrior images have been downloaded to the proper directory. GREAT SUCCESS!";
	}

	/**
	 * Download warrior featured image.
	 *
	 * @since 2021-10-03
	 */
	public function download_warrior_featured_image() : void {
		$data_set = json_decode( file_get_contents( $this->data_path ), true );
	
		foreach ( $data_set as $key => $value ) {
			$warrior_slug = $data_set[$key]['Slug'];
			$featured_image = $data_set[$key]['Image Gallery'];
		
			mkdir( "images/warriors/$warrior_slug/featured-image", 0777, true );
	
			if ( empty( $featured_image ) ) {
				echo "The image gallery for $warrior_slug is empty.\n";
				continue;
			}

			$file_ext = substr( $image, -4 );

			file_put_contents( "images/warriors/$warrior_slug/$warrior_slug-featured$file_ext", file_get_contents( $image ) );

			echo "Featured image for $warrior_slug has been downloaded.\n";
		}
	
		echo "Operation complete: All Warrior images have been downloaded to the proper directory. GREAT SUCCESS!";
	}
}

// $warrior_gallery = new SRF_Downloads( 'data/webflow-json/SRF-Warriors.json' );
// $warrior_gallery->download_warrior_gallery();

$warrior_featured_image = new SRF_Downloads( 'data/webflow-json/SRF-Warriors.json' );
$warrior_featured_image->download_warrior_featured_image();