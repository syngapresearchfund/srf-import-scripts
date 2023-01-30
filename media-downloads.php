<?php

require_once './class-srf-media-downloads.php';

// ! NOTE: Once images are downloaded, use the `rename` CLI tool to fix jpeg extensions.
// ! For example, move into the content type dir and run `rename 's/jpeg/.jpg/' ./**/**/*jpeg`.
// ! This will fix broken image extensions.

// Warrior Downloads
// $warrior_galleries = new SRF_Media_Downloads( 'https://api.webflow.com/collections/5fb39d7a23e32a4a96b1aab2/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'image-gallery', 'images/2023-01-22/warriors/galleries', '2023-01-17', true );
// $warrior_featured_images = new SRF_Media_Downloads( 'https://api.webflow.com/collections/5fb39d7a23e32a4a96b1aab2/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'photo', 'images/2023-01-22/warriors/featured-images', '2023-01-17' );

// Event Downloads
// $event_images = new SRF_Media_Downloads( 'https://api.webflow.com/collections/5fff5614a4b6fde69ab9fcb1/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'image', 'images/2023-01-22/events' );

// Team Downloads
// $team_images = new SRF_Media_Downloads( 'https://api.webflow.com/collections/5fb2315e1e40ec6dda1d4434/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'profile-picture', 'images/2023-01-22/team' );

// Researchers Downloads
// $researchers_images = new SRF_Media_Downloads( 'https://api.webflow.com/collections/5fb232f0d183e48eeb8dc3b5/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'picture', 'images/2023-01-22/researchers' );

// Blog Featured Image Downloads
// $blog_featured_images   = new SRF_Media_Downloads( 'https://api.webflow.com/collections/5fa7dd058c8d627498b9cd07/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'main-image', 'images/2023-01-22/blog/set1' );
// $blog_featured_images_2 = new SRF_Media_Downloads( 'https://api.webflow.com/collections/5fa7dd058c8d627498b9cd07/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0&offset=100', 'main-image', 'images/2023-01-22/blog/set2' );

// Grant Downloads
// $grant_images = new SRF_Media_Downloads( 'https://api.webflow.com/collections/5fb28e7a6744e975d9c5f59d/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'image', 'images/2023-01-22/grants' );
// $grant_images = new SRF_Media_Downloads( 'https://api.webflow.com/collections/5fb28e7a6744e975d9c5f59d/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'grant-pdf', 'documents/2023-01-26/grants' );

// Movie Downloads
$movies = new SRF_Media_Downloads( 'https://api.webflow.com/collections/5fff2adfcd939f5bdc4e73de/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'list-video-thumbnail', 'images/2023-01-22/movies' );
