# ND Google Reviews

WordPress plugin to add Google Reviews to any post type. Also adds the total number of reviews and average rating.

This plugin is designed for developers who want to use as a basis for their own projects and does not include any default view or styling. An example view will be added later.

**No support will be provided by the developer.**

## Installation

1. Install and activate the Advanced Custom Fields plugin, if not already.
2. Install and activate this plugin (ND Google Reviews).
3. Go to Site Options > Google Reviews and follow the 'How To' instructions.

## Limitations

- Pulls the last five Google Reviews per post only.
- The import process may time out (fail) if you have a large number of posts to import reviews for.

## TODO
- [ ] Add a default(?) / example view as a starting point for developers
- [ ] Set ability to hide reviews with a score of less than four stars (currently on by default)
- [ ] Allow more fine-tuning of the ACF location settings for the post fields, e.g. to show for some pages, but not all

## Changelog

### 2.2.0 (2023-12-13)
- Add `nd_google_reviews_disable_reviews_schema` filter to allow reviews schema to be disabled and only show aggregate rating

### 2.0.0 (2023-07-04)
- Fix only one review showing in schema + (breaking) refactor

### 1.0.3 (2023-06-30)
- Fix critical error if array not returned
- Skip abandoning import when Place ID is not valid
- Update code to PHP 8 version
- Drop import batches to 250 at a time

### 1.0.2 (2023-06-29)
- Batch reviews imports to 500 at a time
- Add .png versions of .svg stars

### 1.0.1 (2023-06-28)
- fix: wrong function ref. breaking meta box
- fix: reviews not showing on front end
- fix: set as wordpress-plugin, not mu-plugin

### 1.0.0 (2023-06-28)
First version of the plugin without a default / example view.
