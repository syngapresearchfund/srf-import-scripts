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
	 * Download warrior image galleries.
	 *
	 * @since 2021-10-03
	 */
	public function download_warrior_galleries() : void {
		foreach ( $this->data_set as $key => $value ) {
			$warrior_slug = $this->data_set[$key]['Slug'];
			$image_array = explode( '; ', $this->data_set[$key]['Image Gallery'] );
			$i = 0;
		
			mkdir( "image-galleries/warriors/$warrior_slug", 0777, true );
	
			foreach ( $image_array as $image ) {
				if ( empty( $image ) ) {
					echo "The image gallery for $warrior_slug is empty.\n";
					continue;
				}
	
				$file_ext = substr( $image, -4 );
	
				file_put_contents( "image-galleries/warriors/$warrior_slug/$warrior_slug-$i$file_ext", file_get_contents( $image ) );
	
				echo "Success! $warrior_slug-$i$file_ext was successfully downloaded.\n";
	
				$i++;
			}
	
			echo "All images for $warrior_slug have been downloaded.\n";
		}
	
		echo "Operation complete: All warrior image galleries have been downloaded to the proper directory. GREAT SUCCESS!";
	}

	/**
	 * Download warrior featured images.
	 *
	 * @since 2021-10-03
	 */
	public function download_warrior_featured_images() : void {
		foreach ( $this->data_set as $key => $value ) {
			$warrior_slug = $this->data_set[$key]['Slug'];
			$featured_image = $this->data_set[$key]['Primary Photo'];
		
			mkdir( "featured-images/warriors/$warrior_slug/featured-image", 0777, true );
	
			if ( empty( $featured_image ) ) {
				echo "The image path for $warrior_slug is empty.\n";
				continue;
			}

			$file_ext = substr( $featured_image, -4 );

			file_put_contents( "featured-images/warriors/$warrior_slug/$warrior_slug-featured$file_ext", file_get_contents( $featured_image ) );

			echo "Featured image for $warrior_slug has been downloaded.\n";
		}
	
		echo "Operation complete: All warrior featured images have been downloaded to the proper directory. GREAT SUCCESS!";
	}

	/**
	 * Download team member featured images.
	 *
	 * @since 2021-10-03
	 */
	public function download_team_featured_images() : void {
		foreach ( $this->data_set as $key => $value ) {
			$team_member_slug = $this->data_set[$key]['Slug'];
			$featured_image = $this->data_set[$key]['Profile Picture'];
		
			mkdir( "featured-images/team-members/$team_member_slug", 0777, true );
	
			if ( empty( $featured_image ) ) {
				echo "The image path for $team_member_slug is empty.\n";
				continue;
			}

			$file_ext = substr( $featured_image, -4 );

			file_put_contents( "featured-images/team-members/$team_member_slug/$team_member_slug-featured$file_ext", file_get_contents( $featured_image ) );

			echo "Featured image for $team_member_slug has been downloaded.\n";
		}
	
		echo "Operation complete: All team member featured images have been downloaded to the proper directory. GREAT SUCCESS!";
	}

	/**
	 * Download researcher featured images.
	 *
	 * @since 2021-10-03
	 */
	public function download_researcher_featured_images() : void {
		foreach ( $this->data_set as $key => $value ) {
			$researcher_slug = $this->data_set[$key]['Slug'];
			$featured_image = $this->data_set[$key]['Picture'];
		
			mkdir( "featured-images/researchers/$researcher_slug", 0777, true );
	
			if ( empty( $featured_image ) ) {
				echo "The image path for $researcher_slug is empty.\n";
				continue;
			}

			$file_ext = substr( $featured_image, -4 );

			file_put_contents( "featured-images/researchers/$researcher_slug/$researcher_slug-featured$file_ext", file_get_contents( $featured_image ) );

			echo "Featured image for $researcher_slug has been downloaded.\n";
		}
	
		echo "Operation complete: All researcher featured images have been downloaded to the proper directory. GREAT SUCCESS!";
	}

	/**
	 * Download blog featured images.
	 *
	 * @since 2021-10-03
	 */
	public function download_blog_featured_images() : void {
		foreach ( $this->data_set as $key => $value ) {
			$post_slug = $this->data_set[$key]['Slug'];
			$featured_image = $this->data_set[$key]['Main Image'];
		
			mkdir( "featured-images/blog/$post_slug", 0777, true );
	
			if ( empty( $featured_image ) ) {
				echo "The image path for $post_slug is empty.\n";
				continue;
			}

			$file_ext = substr( $featured_image, -4 );

			file_put_contents( "featured-images/blog/$post_slug/$post_slug-featured$file_ext", file_get_contents( $featured_image ) );

			echo "Featured image for $post_slug has been downloaded.\n";
		}
	
		echo "Operation complete: All blog post featured images have been downloaded to the proper directory. GREAT SUCCESS!";
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

$blog_featured_images = new SRF_Downloads( 'data/webflow-json/SRF-Blog-Posts.json' );
$blog_featured_images->download_blog_featured_images();