<?php

require_once './class-srf-content-imports.php';

// Import for Post Categories.
// $post_categories = new SRF_Content_Imports( './data/webflow-api-data/api-srf-blog-categories.json' );

// Import for Posts.
// $posts = new SRF_Content_Imports( './data/webflow-api-data/api-srf-posts-1.json' );

// Import for Warriors.
// $warriors = new SRF_Content_Imports( './data/webflow-api-data/api-srf-warriors-3.json' );
$warriors = new SRF_Content_Imports( 'https://api.webflow.com/collections/5fb39d7a23e32a4a96b1aab2/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'warriors' );

// Import for Team.
// $team = new SRF_Content_Imports( 'https://api.webflow.com/collections/5fb2315e1e40ec6dda1d4434/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'team' );

// Import for Researchers.
// $researchers = new SRF_Content_Imports( './data/webflow-api-data/api-srf-researchers.json' );

// Import for Events.
// $events = new SRF_Content_Imports( './data/webflow-api-data/api-srf-events.json' );

// Import for Webinars.
// $webinars = new SRF_Content_Imports( 'https://api.webflow.com/collections/5fb23724edd53121e188fe74/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'webinars' );
