<?php

require_once './class-srf-content-imports.php';

// $post_categories = new SRF_Content_Imports( './data/webflow-api-data/api-srf-blog-categories.json' );

// $posts = new SRF_Content_Imports( './data/webflow-api-data/api-srf-posts-1.json' );
// $posts = new SRF_Content_Imports( './data/webflow-api-data/api-srf-posts-2.json' );

// $warriors = new SRF_Content_Imports( './data/webflow-api-data/api-srf-warriors-3.json' );
// $warriors = new SRF_Content_Imports( './data/webflow-api-data/api-srf-warriors-4.json' );

// $team = new SRF_Content_Imports( './data/webflow-api-data/api-srf-team.json' );

// $researchers = new SRF_Content_Imports( './data/webflow-api-data/api-srf-researchers.json' );

// $events = new SRF_Content_Imports( './data/webflow-api-data/api-srf-events.json' );

// Test import for Webinars.
$events = new SRF_Content_Imports( 'https://api.webflow.com/collections/5fb23724edd53121e188fe74/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'webinars' );
// $events = new SRF_Content_Imports( './data/webflow-api-data/api-srf-webinars.json', 'webinars' );
