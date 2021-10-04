<?php

class SRF_Downloads {
	private $data_set;
	private $data_key;
	private $output_path;
	private $is_gallery;

	public function __construct( $data_path, $data_key, $output_path, $is_gallery = false ) {
		if ( ! is_string( $data_path ) ) {
			echo 'Error: The data path must be passed in as a string!';
			return; // exit early
		}
		if ( ! is_string( $data_key ) ) {
			echo 'Error: The data key must be passed in as a string!';
			return; // exit early
		}
		if ( ! is_string( $output_path ) ) {
			echo 'Error: The output path must be passed in as a string!';
			return; // exit early
		}

		if( ! ini_get( 'allow_url_fopen' ) ) {
			echo 'Error: allow_url_fopen is not enabled in your environment!';
			return; // exit early
		}

		$this->data_set    = json_decode( file_get_contents( $data_path ), true );
		$this->data_key    = $data_key;
		$this->output_path = $output_path;
		$this->is_gallery  = $is_gallery;
	}
	
	/**
	 * Download Webflow files from URL.
	 *
	 * @since 2021-10-03
	 */
	public function download_files() : void {
		foreach ( $this->data_set as $key => $value ) {
			$item_slug = $this->data_set[$key]['Slug'];
			$file_path = $this->is_gallery ? explode( '; ', $this->data_set[$key][$this->data_key] ) : $this->data_set[$key][$this->data_key];
		
			mkdir( "$this->output_path/$item_slug", 0777, true );
	
			if ( empty( $file_path ) ) {
				echo "The image path for $item_slug is empty.\n";
				continue;
			}

			if ( $this->is_gallery ) {
				$i = 0;

				foreach ( $file_path as $file ) {
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
	
		echo "Operation complete: All files have been downloaded to the proper directory. GREAT SUCCESS!";
	}
}

// $warrior_galleries = new SRF_Downloads( 'data/webflow-json/SRF-Warriors.json' );
// $warrior_galleries->download_warrior_galleries();

// $warrior_featured_images = new SRF_Downloads( 'data/webflow-json/SRF-Warriors.json' );
// $warrior_featured_images->download_warrior_featured_images();

// $team_featured_images = new SRF_Downloads( 'data/webflow-json/SRF-Team-Members.json' );
// $team_featured_images->download_team_featured_images();

// $researcher_featured_images = new SRF_Downloads( 'data/webflow-json/SRF-Researchers.json' );
// $researcher_featured_images->download_researcher_featured_images();

// $blog_featured_images = new SRF_Downloads( 'data/webflow-json/SRF-Blog-Posts.json' );
// $blog_featured_images->download_blog_featured_images();

// $event_featured_images = new SRF_Downloads( 'data/webflow-json/SRF-Events.json', 'Image', 'images/events' );
// $event_featured_images->download_files();

$warrior_galleries = new SRF_Downloads( 'data/webflow-json/SRF-Warriors.json', 'Image Gallery', 'images/warriors/galleries', true );
$warrior_galleries->download_files();