<?php

require_once './class-srf-media-downloads.php';

// ! NOTE: Once images are downloaded, use the `rename` CLI tool to fix jpeg extensions. For example, move into the content type dir and run `rename 's/jpeg/.jpg/' ./**/**/*jpeg`. This will fix broken image extensions.

// $warrior_galleries = new SRF_Media_Downloads( 'https://api.webflow.com/collections/5fb39d7a23e32a4a96b1aab2/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'image-gallery', 'images/warriors/2023-01-17/galleries', '2022-01-15', true );
$warrior_featured_images = new SRF_Media_Downloads( 'https://api.webflow.com/collections/5fb39d7a23e32a4a96b1aab2/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'photo', 'images/warriors/2023-01-17/featured-images', '2022-01-15' );

// $event_featured_images = new SRF_Media_Downloads( 'data/webflow-api-data/api-srf-events.json', 'image', 'images/events' );

// $grant_images = new SRF_Media_Downloads( 'data/webflow-api-data/api-srf-grants.json', 'grant-pdf', 'files/grants/pdf' );

// $team_images = new SRF_Media_Downloads( 'data/webflow-api-data/api-srf-team.json', 'profile-picture', 'images/team' );

// $researchers_images = new SRF_Media_Downloads( 'data/webflow-api-data/api-srf-researchers.json', 'picture', 'images/researchers' );

// $blog_feat_images = new SRF_Media_Downloads( 'data/webflow-api-data/api-srf-posts-2.json', 'main-image', 'images/blog/set-2', 1638921600 );

// $grant_images = new SRF_Media_Downloads( 'data/webflow-api-data/api-srf-grants.json', 'image', 'images/grants' );
