# Changelog
All notable changes to this project will be documented in this file.

## [1.3.15] / 2022-01-07
### Features
- Support for MariaDB (benjaminjonard)

## [1.3.14] / 2022-01-01
:warning: If after this update you have incoherent collections/items counters, head to the Administration page and click on "Refresh caches" 

### Features
- Add children (sub-collections) list display mode (benjaminjonard)
- Add list display mode for collections, albums and wishlists index pages (benjaminjonard)
- Add option to display or hide actions, visibility, number of items and number of children columns in list display mode (benjaminjonard)
- Add sorting on number of items and number of children for list display mode (benjaminjonard)
- Add Price field type. Price fields with the same label will be added up and displayed on collection page (benjaminjonard)

### Miscellaneous
- Upgrade PHP and JS dependencies, fix CVE-2022-39261 (benjaminjonard)
- Upgrade to API Platform 3 (benjaminjonard)
- Rework cached counters (benjaminjonard)
- Externalize translation process on https://crowdin.com/project/koillection (benjaminjonard)
    
## [1.3.13] / 2022-09-12
### Features
- Add possibility to order columns in list display mode (benjaminjonard)
- Add possibility to hide actions and visibility in list display mode (benjaminjonard)

### Fixes
- Fix sql export in admin panel (benjaminjonard)
- Fix error on collection edit (benjaminjonard)

### Miscellaneous
- Make table headers sticky (benjaminjonard)

## [1.3.12] / 2022-08-29
### Fixes
- Fix empty logs in production environment (benjaminjonard)
- Add missing migration for new locale values (benjaminjonard)

## [1.3.11] / 2022-08-28
### Features
- Add Spanish translation (crishnakh)
- Add new API endpoints for uploads (benjaminjonard)

### Fixes
- Make link properly clickable in list display mode (benjaminjonard)
- Properly display empty date metadata (benjaminjonard)

### Miscellaneous
- Small improvements for search (benjaminjonard)
- Remove phpunit and composer binaries (benjaminjonard)
- Clean up translation files (benjaminjonard)

## [1.3.10] / 2022-08-03
### Features
- Search will now use metadata (benjaminjonard)

### Fixes
- Fix decoding of lists metadata (benjaminjonard)
- Fix error when adding a new tag to an item (benjaminjonard)

## [1.3.9] / 2022-07-29
### Features
- Add possibility to order choices in choice list form (benjaminjonard)

### Fixes
- Fix choice list not loading when using default item template from a collection (benjaminjonard)
- Fix choice list not displaying choices containing special characters (benjaminjonard)
- Fix errors on gif upload (benjaminjonard)

## [1.3.8] / 2022-07-28
### Fixes
- Fix error when submitting forms with "date" metadata (benjaminjonard)

## [1.3.7] / 2022-07-27
### Features
- Add possibility to choose which columns to display in collection list view based on items metadata (benjaminjonard)
- Add new choice list type (benjaminjonard)
- Add possibility to order s collection's items on text, list and country metadata types (benjaminjonard)

### Miscellaneous
- Update JS dependencies, fixes CVE-2022-25858 (benjaminjonard)
- Remove update logs, add create/delete logs for more entities (benjaminjonard)

## [1.3.6] / 2022-07-20
### Features
- Add sorting feature on collections; sorting is available on item name and data of type Date, Number and Rating (benjaminjonard)
- Add textarea (long text) data type (benjaminjonard)
- Add an items default template field in collection form (benjaminjonard)

### Fixes
- Fix missing DateFormat in enum (benjaminjonard)
- Fix wrong column label on wishes list (benjaminjonard)
- Properly handle currency display based on user locale (benjaminjonard)
- Fix broken path to shared items in a tag (benjaminjonard)
- Improve thumbnails generation, now properly takes image EXIF orientation into account (benjaminjonard)

### Miscellaneous
- Api - add possibility to directly link a Datum to an item or a collection (benjaminjonard)
- Improve english translation (benjaminjonard)
- Update JS and PHP dependencies (benjaminjonard)

## [1.3.5] / 2022-07-07
### Features
- Add a list view for items in collections, tags and albums (benjaminjonard)
 
### Fixes
- Prevent unnecessary image re-upload when submitting forms containing base64 images (benjaminjonard)
- Fix swipe gesture on item page (benjaminjonard)

### Miscellaneous
- Update JS and PHP dependencies (benjaminjonard)

## [1.3.4] / 2022-06-27
### Fixes
- Fix zip exports if user uploads directory doesn't exist (benjaminjonard)
- Fix error when displaying item with empty file or country fields (benjaminjonard)
- Remove Administration entry in menu on mobile for non admin users (benjaminjonard)
- Fix completion percent on inventories (benjaminjonard)
- Handle division by zero in inventories when a collection has no items (benjaminjonard)

### Miscellaneous
- Update JS and PHP dependencies (benjaminjonard)

## [1.3.3] / 2022-05-04
### Features
- Add new Link type (benjaminjonard)

### Fixes
- Fix CleanUp and RegenerateThumbnails commands (benjaminjonard)

### Miscellaneous
- Update JS and PHP dependencies, fixing all current CVEs (benjaminjonard)

## [1.3.2] / 2022-03-21
### Fixes
- Fix image upload in additional data (benjaminjonard)

### Miscellaneous
- Add display password icon on login page (benjaminjonard)
- Multi-arch image for docker, now supports ARM (benjaminjonard)

## [1.3.1] / 2022-02-20
### Fixes
- Fix date pickers not displaying (benjaminjonard)
- Fix bug where last common field was incorrectly generated (benjaminjonard)
- Allow empty dates when loading common fields (benjaminjonard)

### Miscellaneous
- Update JS dependencies (benjaminjonard)
- Add pgadmin container to example configuration (Zwordi)

## [1.3.0] / 2022-02-13
### Features
- Add a basic REST API, documentation is accessible on /api (benjaminjonard)
- Enhance visibility mechanism: takes parent visibility into account without erasing own visibility (benjaminjonard)
- Display visibility level icon on each page (benjaminjonard)

### Fixes
- Fix first connection auto-login error (benjaminjonard)
- Fix exception loop (benjaminjonard)

### Miscellaneous
- Display flags as emoji instead of images (benjaminjonard)
- Rework error pages (benjaminjonard)
- Update PHP and JS dependencies, fix current CVEs (benjaminjonard)
- Restore asset preloading (benjaminjonard)

## [1.2.5] / 2021-12-20
### Fixes
- Fix duplicates from tags list, order alphabetically (benjaminjonard)
- Fix tag edition (benjaminjonard)
- Fix sql dump (benjaminjonard)

### Miscellaneous
- Update JS dependencies (benjaminjonard)
- Update PHP dependencies (benjaminjonard)
- Remove built-in assets (benjaminjonard)
- Admin: split backup function into two separated buttons (benjaminjonard)

## [1.2.4] / 2021-09-16
### Features
- Allow user to change username and email in their profile (benjaminjonard)

### Fixes
- Fix divisions by zero when inventory is empty (benjaminjonard)
- Fix delete forms (benjaminjonard)

### Miscellaneous
- Preload assets (benjaminjonard)
- Update JS dependencies, fix CVE-2021-33587 (benjaminjonard)
- Update PHP dependencies (benjaminjonard)

## [1.2.3] / 2021-05-31
### Fixes
- Fix date format (benjaminjonard)

### Miscellaneous
- Update JS, fix CVE-2021-32640 and CVE-2021-23386 (benjaminjonard)
- Update to Symfony 5.3 (benjaminjonard)

## [1.2.2] / 2021-05-22
### Fixes
- Fix select lists on first connection page (benjaminjonard)

## [1.2.1] / 2021-05-17
### Features
- Add description to visibilities (benjaminjonard)

### Fixes
- Fix update date on all pages (benjaminjonard)

### Miscellaneous
- Update JS and PHP dependencies, fix CVE-2021-21424 (benjaminjonard)

## [1.2.0] / 2021-05-03
### Features
- Add "rating" field type (benjaminjonard)
- Add "number" field type (benjaminjonard)
- Add new "Only to authenticated users" visibility (benjaminjonard)
- Add some activity counters on admin dashboard (benjaminjonard)
- Display a link to item's collection when viewing it from a tag page (benjaminjonard)
- Add return to collection button when viewing another user collection (benjaminjonard)

### Fixes
- Fix release checking (benjaminjonard)
- Fix breadcrumb on wish edit (benjaminjonard)
- Fix item name guesser (benjaminjonard)
- Prioritize thumbnails when available (benjaminjonard)

### Miscellaneous
- Add production ready docker setup example with Traefik and SSL (Zwordi)
- Move all Javascript to Stimulus, remove jQuery (benjaminjonard)
- Update PHP version requirement to 8.0 (benjaminjonard)
- Update JS and PHP dependencies (benjaminjonard)

## [1.1.7] / 2021-01-27
### Features
- Add a message in admin interface if a new release of Koillection is available  (benjaminjonard)
- Add a new field type for dates on item page (benjaminjonard)
- Add .gif support for uploads (benjaminjonard)
- Add the possibility to add related items to an item (benjaminjonard)

### Miscellaneous
- Update JS and PHP dependencies (benjaminjonard)
- Trim white spaces on tag autocomplete (benjaminjonard)
- Change text color and font weight on autocompletes (benjaminjonard)
- Multiple small dark mode improvements (benjaminjonard)
- Add a CONTRIBUTING.md file (benjaminjonard)
- Add some new counters in admin dashboard (benjaminjonard)
- Remove preload support as it was dropped by Chrome (benjaminjonard)

### Fixes
- Fix social media metas (benjaminjonard)

## [1.1.6] / 2020-10-17
### Features
- Functionalities can now be activated/deactivated in settings page (benjaminjonard)
- Dark mode can be automatically activated between two hours (benjaminjonard)
- On mobile devices, user can swipe left or right to navigate between items (benjaminjonard)
- Add a "remember me" option on login page (benjaminjonard)
- Keep tag context when navigating tag's items instead of switching to the item's collection (benjaminjonard)

### Miscellaneous
- Rework profile and settings pages (benjaminjonard)
- Move search bar at the top of the left menu (benjaminjonard)
- Remove themes for now (benjaminjonard)
- Improve dark mode (benjaminjonard)
- Update JS and PHP dependencies (benjaminjonard)

### Fixes
- Fix album breadcrumb (benjaminjonard)
- Fix preload for assets (benjaminjonard)

## [1.1.5] / 2020-09-21
### Fixes
- Update migration configuration to comply with the new syntax introduced in doctrine/doctrine-migrations-bundle v3.0 (benjaminjonard)

### Miscellaneous
- Update JS and PHP dependencies (benjaminjonard)

## [1.1.4] / 2020-09-11
### Fixes
- Fix clean up function (benjaminjonard)
- Fix all deprecations (benjaminjonard)

### Miscellaneous
- Update PHP dependencies to fix high severity alert on symfony/http-kernel (benjaminjonard)
- Update JS dependencies (benjaminjonard)

## [1.1.3] / 2020-08-14
### Features
- New file field for items and collections (benjaminjonard)

### Fixes
- Fix multiple data display on item page (benjaminjonard)
- Fix links in underlined tags (benjaminjonard)
- Properly handle context in tag links (benjaminjonard)

### Miscellaneous
- Update PHP and JS dependencies (benjaminjonard)

## [1.1.2] / 2020-07-30
### Features
- Collections can have additional fields like items (benjaminjonard)
- Add an autocomplete on search in the header (benjaminjonard)
- Rework additional fields creation (benjaminjonard)

### Fixes
- Fix upload on item data (benjaminjonard)
- Fix detection of common fields on item data (benjaminjonard)
- Fix tag search (benjaminjonard)
- Fix locale dropdown on login page (benjaminjonard)
- Prevent default selection on country fields (benjaminjonard) 

### Miscellaneous
- Handle echarts properly through yarn (benjaminjonard)
- Update JS dependencies (benjaminjonard)
- Update PHP dependencies (benjaminjonard)

## [1.1.1] / 2020-06-25
### Features
- Add a button in admin panel to regenerate thumbnails (benjaminjonard)

### Fixes
- Fix locale updates (benjaminjonard)
- Fix wish transfer to collection form (benjaminjonard)

### Miscellaneous
- Upgrade to Symfony 5.1  (benjaminjonard)
- Improve dark mode (benjaminjonard)
- Fix vulnerabilities in JS dependencies (benjaminjonard)

## [1.1.0] / 2020-04-30
### Important
- Update PHP minimum version to 7.4 (benjaminjonard)
- Update to Symfony 5 (benjaminjonard)
- Update to Twig 3 (benjaminjonard)
- Add support of Mysql for version 8 and superior (benjaminjonard)

### Features
- Dark mode (benjaminjonard)
- Suggest new item name based on other items in the collection (benjaminjonard)
- Webp format support for images (benjaminjonard)
- Support for all currencies (benjaminjonard)
- Add a field `Country` for items (benjaminjonard)
- Users can choose how dates should be displayed (benjaminjonard)
- Add Inventory (benjaminjonard)
- Tags can now be assigned to a category (benjaminjonard)
- Admins can do a full backup : database + images (benjaminjonard)
- Admins can clean obsoletes images (benjaminjonard)
- Tag autocompletion will now priotarize words starting the same way as the word searched (benjaminjonard)
- Add metadata for content sharing on social networks (benjaminjonard)
- Use select2 for all dropdown lists (benjaminjonard)
- Add 'See more' button on related tags (benjaminjonard)
- Add history for Albums and Wishlists (benjaminjonard)
- Add possibility to create child albums (benjaminjonard)
- Search now search in Albums and Wishlists (benjaminjonard)
- Urls now depends on user's locale (benjaminjonard)
- Merge Profile and Settings menus (benjaminjonard)

### Miscellaneous
- Rework responsive on forms (benjaminjonard)
- Rework modals (benjaminjonard)
- Rework counters (benjaminjonard)
- Rework file upload system (benjaminjonard)
- Add indexes on `visibility` property (benjaminjonard)
- Lazy load Doctrine listeners (benjaminjonard)

### Fixes
- Avatars are now correctly deleted (benjaminjonard)
- Fix floating label on croppers when using Firefox (benjaminjonard)
- Fix streamed responses when downloading backups (benjaminjonard)
- Fix infinite loading bar (benjaminjonard)

## [1.0.4] / 2019-03-06
### Fixes
 - Improve header responsiveness (benjaminjonard)
 
### Features
 - Add a scrollbar on navbar for mobile devices (benjaminjonard)
 - Add hidden menu for mobile devices (benjaminjonard)
 
### Miscellaneous
- Update dependencies and config files (benjaminjonard)
- Switch to the new .env system (with .env.local) (benjaminjonard)
- Update phpUnit version (benjaminjonard)

## [1.0.3] / 2019-02-26
### Fixes
- Fix recompute disk usage action (benjaminjonard)
- Fix collection placeholder style (benjaminjonard)
- Fix item images margins (benjaminjonard)
- Fix signs linked to a private item (benjaminjonard)
- Fix date search (benjaminjonard)
- Fix deprecations on transchoices (benjaminjonard)
- Fix twig deprecations in blocks (benjaminjonard)

### Features
- Add action filter on history page (benjaminjonard)
- Use referer for redirection after user login (benjaminjonard)

### Miscellaneous
- Upgrade Encore version (benjaminjonard)
- Upgrade to Symfony 4.2 (benjaminjonard)
- Update some english translations for proper plurals handling (benjaminjonard)
- Update general style for the header (benjaminjonard)
- Rework search input style (benjaminjonard)
- Replace all generateUrl with redirectToRoute (benjaminjonard)
- Replace deprecated TranslatorInterface (benjaminjonard)
- Add debug env for local docker (benjaminjonard)

 
## [1.0.2] / 2018-09-28
### Fixes
- Fix error pages style (benjaminjonard)
- Fix sortable fields on item edition page (benjaminjonard)
- Fix custom field selection on item edition page (benjaminjonard)

## [1.0.1] / 2018-09-18
### Features
- Add SQL export (benjaminjonard)
- Add images export (benjaminjonard)
- Add tooltips on some images (benjaminjonard)

### Miscellaneous
- Update php dependencies (benjaminjonard)
- Fix deprecations (benjaminjonard)
- Switch from Gulp to Symfony Encore for managing assets (benjaminjonard)
- Update materializecss to v1.0.0 (benjaminjonard)
- Remove FOSJsRoutingBundle (benjaminjonard)
- Remove unused FontAwesome icons and inline the others (benjaminjonard)

### Refactoring
- Move javascript from Twig template to a JS file on statistics page (benjaminjonard)

## [1.0.0] / 2018-07-31
- Initial release (benjaminjonard)
