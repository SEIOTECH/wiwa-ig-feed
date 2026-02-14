# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-05-23

### Added

- Initial plugin structure with OOP architecture.
- `Instagram_API` class with transient caching.
- Rest API endpoint `/wp-json/wiwa-ig/v1/feed`.
- Settings page for Access Token, Post Limit, Display Mode, and Cache Time.
- Shortcode `[wiwa_ig_feed]` for frontend display.
- CSS Scroll Snap carousel with responsive design.
- GitHub Actions for PHP linting and structure checks.

### Fixed

- **Critical:** Video thumbnails now correctly resolved from Instagram Graph API (using `thumbnail_url` for `VIDEO` media type).
