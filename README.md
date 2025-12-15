# WP Rocked

A lightweight WordPress performance plugin that mirrors the core ideas of WP Rocket—page caching, HTML optimization, browser cache headers, and lazy loading—while remaining easy to audit and extend.

## Features
- Page cache stored in `wp-content/cache/wp-rocked` and automatically purged when content changes.
- Lightweight HTML optimization that strips comments and compresses whitespace.
- Optional browser cache headers for static assets.
- Lazy loading for images and iframes without external dependencies.
- Simple settings page under **Settings → WP Rocked** for toggling features and clearing the cache.

## Installation
1. Copy the `wp-rocked` directory into your WordPress `wp-content/plugins` folder.
2. Activate **WP Rocked** from the WordPress Plugins screen.
3. Adjust settings under **Settings → WP Rocked**.

## Development Notes
- The plugin avoids heavy dependencies and keeps logic within a few classes for easier customization.
- Cache files are keyed by the request URL hash. You can safely delete the `wp-content/cache/wp-rocked` directory at any time.
