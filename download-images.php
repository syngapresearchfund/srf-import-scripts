<?php

function download_warrior_images() {
	$warriors = json_decode( file_get_contents( 'webflow-json/SRF-Warriors.json' ), true );

	if( ! ini_get( 'allow_url_fopen' ) ) {
		// exit early
		echo 'Error: allow_url_fopen is not enabled in your environment!';
		return;
	}

	foreach ( $warriors as $key => $value ) {
		$warrior_slug = $warriors[$key]['Slug'];
		$image_array = explode( '; ', $warriors[$key]['Image Gallery'] );
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
download_warrior_images();
