<?php

require_once './class-srf-media-downloads.php';

// $warrior_galleries = new SRF_Media_Downloads( 'data/webflow-api-data/api-srf-warriors-3.json', 'image-gallery', 'images/warriors/galleries', true );
// $warrior_galleries->download_files();
// $warrior_featured_images = new SRF_Media_Downloads( 'data/webflow-api-data/api-srf-warriors-3.json', 'photo', 'images/warriors/featured-images' );
// $warrior_featured_images->download_files();

// $event_featured_images = new SRF_Media_Downloads( 'data/webflow-api-data/api-srf-events.json', 'image', 'images/events' );
// $event_featured_images->download_files();

// $grant_images = new SRF_Media_Downloads( 'data/webflow-api-data/api-srf-grants.json', 'grant-pdf', 'files/grants/pdf' );
// $grant_images->download_files();

// $team_images = new SRF_Media_Downloads( 'data/webflow-api-data/api-srf-team.json', 'profile-picture', 'images/team' );
// $team_images->download_files();

// $researchers_images = new SRF_Media_Downloads( 'data/webflow-api-data/api-srf-researchers.json', 'picture', 'images/researchers' );
// $researchers_images->download_files();

$blog_feat_images = new SRF_Media_Downloads( 'data/webflow-api-data/api-srf-posts-2.json', 'main-image', 'images/blog/set-2', 1638921600 );
// $blog_feat_images->download_files();

// $grant_images = new SRF_Media_Downloads( 'data/webflow-api-data/api-srf-grants.json', 'image', 'images/grants' );
// $grant_images->download_files();
