<?php

require_once './class-srf-content-imports.php';

// Import for Post Categories. ✅
// $post_categories = new SRF_Content_Imports( 'https://api.webflow.com/collections/5fa7dd058c8d6276adb9cd06/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'post categories' );

// Import for Posts. ✅
// $posts = new SRF_Content_Imports( 'https://api.webflow.com/collections/5fa7dd058c8d627498b9cd07/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'posts', '2022-01-01' );
// $posts_2 = new SRF_Content_Imports( 'https://api.webflow.com/collections/5fa7dd058c8d627498b9cd07/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0&offset=100', 'posts' );

// Import for Warriors. ✅
// $warriors  = new SRF_Content_Imports( 'https://api.webflow.com/collections/5fb39d7a23e32a4a96b1aab2/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'warriors' );
// $warriors2 = new SRF_Content_Imports( 'https://api.webflow.com/collections/5fb39d7a23e32a4a96b1aab2/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0&offset=100', 'warriors' );

// Import for Team. ✅
// $team = new SRF_Content_Imports( 'https://api.webflow.com/collections/5fb2315e1e40ec6dda1d4434/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'team' );

// Import for Researchers.✅
// $researchers = new SRF_Content_Imports( 'https://api.webflow.com/collections/5fb232f0d183e48eeb8dc3b5/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'researchers' );

// Import for Events. ✅
// $events = new SRF_Content_Imports( 'https://api.webflow.com/collections/5fff5614a4b6fde69ab9fcb1/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'events' );

// Import for Webinars. ✅
// $webinars = new SRF_Content_Imports( 'https://api.webflow.com/collections/5fb23724edd53121e188fe74/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'webinars' );

// Import for Grants. ✅
// $grants = new SRF_Content_Imports( 'https://api.webflow.com/collections/5fb28e7a6744e975d9c5f59d/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'grants' );

// TODO: Need new method for this - set a category in as Resources CPT
// Import for Movies (Patient Stories from Webflow API).
$movies = new SRF_Content_Imports( 'https://api.webflow.com/collections/5fff2adfcd939f5bdc4e73de/items?access_token=2ea8f0e8e54e14d9f0a362a021a54eb2552117c28ccf9e278b1dc6fdda444d1a&api_version=1.0.0', 'movies' );

// TODO: Also need to import Studies, Sponsors, Press Releases, Org Partners, Knowledge Center - possibly set all as a category in Resources CPT
// It may make sense for

// TODO: Lastly, need to import the Shop, which appears to be possible via CSV
