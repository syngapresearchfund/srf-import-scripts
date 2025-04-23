# SRF Import Scripts

Import scripts and helpers for migrating our data over to WordPress.

## Content Imports

### Arguments

- `$url`: The URL of the API endpoint to fetch the content from.
- `$type`: The type of content to import.
- `$date`: The date to import the content from.

Example:

```php
$books = new SRF_Content_Imports( 'https://api.webflow.com/collections/.../items?access_token=...&api_version=1.0.0', 'books', '1970-01-01' );
```

## Media Imports

### Arguments

- `$data_path`: The path to the data to import.
- `$data_key`: The content type of the media to import.
- `$output_path`: The path to the output directory.
- `$date`: The date to import the media from.
- `$is_gallery`: Whether the media is a part of an image gallery or not.

Example:

```php
$movies = new SRF_Media_Downloads( 'https://api.webflow.com/collections/.../items?access_token=...&api_version=1.0.0', 'movies', '1970-01-01' );
```

## RSS Imports

### Arguments

- `$url`: The URL of the API endpoint to fetch the content from.
- `$type`: The type of content to import.
- `$date`: The date to import the content from.

Example:

```php
$podcasts = new SRF_RSS_Imports( 'https://feed.podbean.com/syngap10/feed.xml', 'srf-podcasts', 'srf-podcasts-category', 'syngap10' );
```

## Usage

This applies to all of the imports listed above.

**Option 1:**

Create a script (e.g., run.php) to include the class and execute the method:

```php
require_once 'class-srf-content-imports.php';

$books = new SRF_Content_Imports( 'https://api.webflow.com/collections/.../items?access_token=...&api_version=1.0.0', 'books', '1970-01-01' );
```

Then run the script by executing the file:

```bash
php run.php
```

**Option 2:**

Run the script directly from the interpreter:

```bash
php -r 'include "class-srf-content-imports.php"; $books = new SRF_Content_Imports( "https://api.webflow.com/collections/.../items?access_token=...&api_version=1.0.0", "books", "1970-01-01" );'
```
