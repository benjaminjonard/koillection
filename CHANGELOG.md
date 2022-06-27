# Changelog
All notable changes to this project will be documented in this file.

## [1.3.4] / 2022-06-27
### Fixes
- Fix zip exports if user uploads directory doesn't exist (Benjamin Jonard)
- Fix error when displaying item with empty file or country fields (Benjamin Jonard)
- Remove Administration entry in menu on mobile for non admin users (Benjamin Jonard)
- Fix completion percent on inventories (Benjamin Jonard)
- Handle division by zero in inventories when a collection has no items (Benjamin Jonard)

### Miscellaneous
- Update JS and PHP dependencies (Benjamin Jonard)

## [1.3.3] / 2022-05-04
### Features
- Add new Link type (Benjamin Jonard)

### Fixes
- Fix CleanUp and RegenerateThumbnails commands (Benjamin Jonard)

### Miscellaneous
- Update JS and PHP dependencies, fixing all current CVEs (Benjamin Jonard)

## [1.3.2] / 2022-03-21
### Fixes
- Fix image upload in additional data (Benjamin Jonard)

### Miscellaneous
- Add display password icon on login page (Benjamin Jonard)
- Multi-arch image for docker, now supports ARM (Benjamin Jonard)

## [1.3.1] / 2022-02-20
### Fixes
- Fix date pickers not displaying (Benjamin Jonard)
- Fix bug where last common field was incorrectly generated (Benjamin Jonard)
- Allow empty dates when loading common fields (Benjamin Jonard)

### Miscellaneous
- Update JS dependencies (Benjamin Jonard)
- Add pgadmin container to example configuration (Zwordi)

## [1.3.0] / 2022-02-13
### Features
- Add a basic REST API, documentation is accessible on /api (Benjamin Jonard)
- Enhance visibility mechanism: takes parent visibility into account without erasing own visibility (Benjamin Jonard)
- Display visibility level icon on each page (Benjamin Jonard)

### Fixes
- Fix first connection auto-login error (Benjamin Jonard)
- Fix exception loop (Benjamin Jonard)

### Miscellaneous
- Display flags as emoji instead of images (Benjamin Jonard)
- Rework error pages (Benjamin Jonard)
- Update PHP and JS dependencies, fix current CVEs (Benjamin Jonard)
- Restore asset preloading (Benjamin Jonard)

## [1.2.5] / 2021-12-20
### Fixes
- Fix duplicates from tags list, order alphabetically (Benjamin Jonard)
- Fix tag edition (Benjamin Jonard)
- Fix sql dump (Benjamin Jonard)

### Miscellaneous
- Update JS dependencies (Benjamin Jonard)
- Update PHP dependencies (Benjamin Jonard)
- Remove built-in assets (Benjamin Jonard)
- Admin: split backup function into two separated buttons (Benjamin Jonard)

## [1.2.4] / 2021-09-16
### Features
- Allow user to change username and email in their profile (Benjamin Jonard)

### Fixes
- Fix divisions by zero when inventory is empty (Benjamin Jonard)
- Fix delete forms (Benjamin Jonard)

### Miscellaneous
- Preload assets (Benjamin Jonard)
- Update JS dependencies, fix CVE-2021-33587 (Benjamin Jonard)
- Update PHP dependencies (Benjamin Jonard)

## [1.2.3] / 2021-05-31
### Fixes
- Fix date format (Benjamin Jonard)

### Miscellaneous
- Update JS, fix CVE-2021-32640 and CVE-2021-23386 (Benjamin Jonard)
- Update to Symfony 5.3 (Benjamin Jonard)

## [1.2.2] / 2021-05-22
### Fixes
- Fix select lists on first connection page (Benjamin Jonard)

## [1.2.1] / 2021-05-17
### Features
- Add description to visibilities (Benjamin Jonard)

### Fixes
- Fix update date on all pages (Benjamin Jonard)

### Miscellaneous
- Update JS and PHP dependencies, fix CVE-2021-21424 (Benjamin Jonard)

## [1.2.0] / 2021-05-03
### Features
- Add "rating" field type (Benjamin Jonard)
- Add "number" field type (Benjamin Jonard)
- Add new "Only to authenticated users" visibility (Benjamin Jonard)
- Add some activity counters on admin dashboard (Benjamin Jonard)
- Display a link to item's collection when viewing it from a tag page (Benjamin Jonard)
- Add return to collection button when viewing another user collection (Benjamin Jonard)

### Fixes
- Fix release checking (Benjamin Jonard)
- Fix breadcrumb on wish edit (Benjamin Jonard)
- Fix item name guesser (Benjamin Jonard)
- Prioritize thumbnails when available (Benjamin Jonard)

### Miscellaneous
- Add production ready docker setup example with Traefik and SSL (Zwordi)
- Move all Javascript to Stimulus, remove jQuery (Benjamin Jonard)
- Update PHP version requirement to 8.0 (Benjamin Jonard)
- Update JS and PHP dependencies (Benjamin Jonard)

## [1.1.7] / 2021-01-27
### Features
- Add a message in admin interface if a new release of Koillection is available  (Benjamin Jonard)
- Add a new field type for dates on item page (Benjamin Jonard)
- Add .gif support for uploads (Benjamin Jonard)
- Add the possibility to add related items to an item (Benjamin Jonard)

### Miscellaneous
- Update JS and PHP dependencies (Benjamin Jonard)
- Trim white spaces on tag autocomplete (Benjamin Jonard)
- Change text color and font weight on autocompletes (Benjamin Jonard)
- Multiple small dark mode improvements (Benjamin Jonard)
- Add a CONTRIBUTING.md file (Benjamin Jonard)
- Add some new counters in admin dashboard (Benjamin Jonard)
- Remove preload support as it was dropped by Chrome (Benjamin Jonard)

### Fixes
- Fix social media metas (Benjamin Jonard)

## [1.1.6] / 2020-10-17
### Features
- Functionalities can now be activated/deactivated in settings page (Benjamin Jonard)
- Dark mode can be automatically activated between two hours (Benjamin Jonard)
- On mobile devices, user can swipe left or right to navigate between items (Benjamin Jonard)
- Add a "remember me" option on login page (Benjamin Jonard)
- Keep tag context when navigating tag's items instead of switching to the item's collection (Benjamin Jonard)

### Miscellaneous
- Rework profile and settings pages (Benjamin Jonard)
- Move search bar at the top of the left menu (Benjamin Jonard)
- Remove themes for now (Benjamin Jonard)
- Improve dark mode (Benjamin Jonard)
- Update JS and PHP dependencies (Benjamin Jonard)

### Fixes
- Fix album breadcrumb (Benjamin Jonard)
- Fix preload for assets (Benjamin Jonard)

## [1.1.5] / 2020-09-21
### Fixes
- Update migration configuration to comply with the new syntax introduced in doctrine/doctrine-migrations-bundle v3.0 (Benjamin Jonard)

### Miscellaneous
- Update JS and PHP dependencies (Benjamin Jonard)

## [1.1.4] / 2020-09-11
### Fixes
- Fix clean up function (Benjamin Jonard)
- Fix all deprecations (Benjamin Jonard)

### Miscellaneous
- Update PHP dependencies to fix high severity alert on symfony/http-kernel (Benjamin Jonard)
- Update JS dependencies (Benjamin Jonard)

## [1.1.3] / 2020-08-14
### Features
- New file field for items and collections (Benjamin Jonard)

### Fixes
- Fix multiple data display on item page (Benjamin Jonard)
- Fix links in underlined tags (Benjamin Jonard)
- Properly handle context in tag links (Benjamin Jonard)

### Miscellaneous
- Update PHP and JS dependencies (Benjamin Jonard)

## [1.1.2] / 2020-07-30
### Features
- Collections can have additional fields like items (Benjamin Jonard)
- Add an autocomplete on search in the header (Benjamin Jonard)
- Rework additional fields creation (Benjamin Jonard)

### Fixes
- Fix upload on item data (Benjamin Jonard)
- Fix detection of common fields on item data (Benjamin Jonard)
- Fix tag search (Benjamin Jonard)
- Fix locale dropdown on login page (Benjamin Jonard)
- Prevent default selection on country fields (Benjamin Jonard) 

### Miscellaneous
- Handle echarts properly through yarn (Benjamin Jonard)
- Update JS dependencies (Benjamin Jonard)
- Update PHP dependencies (Benjamin Jonard)

## [1.1.1] / 2020-06-25
### Features
- Add a button in admin panel to regenerate thumbnails (Benjamin Jonard)

### Fixes
- Fix locale updates (Benjamin Jonard)
- Fix wish transfer to collection form (Benjamin Jonard)

### Miscellaneous
- Upgrade to Symfony 5.1  (Benjamin Jonard)
- Improve dark mode (Benjamin Jonard)
- Fix vulnerabilities in JS dependencies (Benjamin Jonard)

## [1.1.0] / 2020-04-30
### Important
- Update PHP minimum version to 7.4 (Benjamin Jonard)
- Update to Symfony 5 (Benjamin Jonard)
- Update to Twig 3 (Benjamin Jonard)
- Add support of Mysql for version 8 and superior (Benjamin Jonard)

### Features
- Dark mode (Benjamin Jonard)
- Suggest new item name based on other items in the collection (Benjamin Jonard)
- Webp format support for images (Benjamin Jonard)
- Support for all currencies (Benjamin Jonard)
- Add a field `Country` for items (Benjamin Jonard)
- Users can choose how dates should be displayed (Benjamin Jonard)
- Add Inventory (Benjamin Jonard)
- Tags can now be assigned to a category (Benjamin Jonard)
- Admins can do a full backup : database + images (Benjamin Jonard)
- Admins can clean obsoletes images (Benjamin Jonard)
- Tag autocompletion will now priotarize words starting the same way as the word searched (Benjamin Jonard)
- Add metadata for content sharing on social networks (Benjamin Jonard)
- Use select2 for all dropdown lists (Benjamin Jonard)
- Add 'See more' button on related tags (Benjamin Jonard)
- Add history for Albums and Wishlists (Benjamin Jonard)
- Add possibility to create child albums (Benjamin Jonard)
- Search now search in Albums and Wishlists (Benjamin Jonard)
- Urls now depends on user's locale (Benjamin Jonard)
- Merge Profile and Settings menus (Benjamin Jonard)

### Miscellaneous
- Rework responsive on forms (Benjamin Jonard)
- Rework modals (Benjamin Jonard)
- Rework counters (Benjamin Jonard)
- Rework file upload system (Benjamin Jonard)
- Add indexes on `visibility` property (Benjamin Jonard)
- Lazy load Doctrine listeners (Benjamin Jonard)

### Fixes
- Avatars are now correctly deleted (Benjamin Jonard)
- Fix floating label on croppers when using Firefox (Benjamin Jonard)
- Fix streamed responses when downloading backups (Benjamin Jonard)
- Fix infinite loading bar (Benjamin Jonard)

## [1.0.4] / 2019-03-06
### Fixes
 - Improve header responsiveness (Benjamin Jonard)
 
### Features
 - Add a scrollbar on navbar for mobile devices (Benjamin Jonard)
 - Add hidden menu for mobile devices (Benjamin Jonard)
 
### Miscellaneous
- Update dependencies and config files (Benjamin Jonard)
- Switch to the new .env system (with .env.local) (Benjamin Jonard)
- Update phpUnit version (Benjamin Jonard)

## [1.0.3] / 2019-02-26
### Fixes
- Fix recompute disk usage action (Benjamin Jonard)
- Fix collection placeholder style (Benjamin Jonard)
- Fix item images margins (Benjamin Jonard)
- Fix signs linked to a private item (Benjamin Jonard)
- Fix date search (Benjamin Jonard)
- Fix deprecations on transchoices (Benjamin Jonard)
- Fix twig deprecations in blocks (Benjamin Jonard)

### Features
- Add action filter on history page (Benjamin Jonard)
- Use referer for redirection after user login (Benjamin Jonard)

### Miscellaneous
- Upgrade Encore version (Benjamin Jonard)
- Upgrade to Symfony 4.2 (Benjamin Jonard)
- Update some english translations for proper plurals handling (Benjamin Jonard)
- Update general style for the header (Benjamin Jonard)
- Rework search input style (Benjamin Jonard)
- Replace all generateUrl with redirectToRoute (Benjamin Jonard)
- Replace deprecated TranslatorInterface (Benjamin Jonard)
- Add debug env for local docker (Benjamin Jonard)

 
## [1.0.2] / 2018-09-28
### Fixes
- Fix error pages style (Benjamin Jonard)
- Fix sortable fields on item edition page (Benjamin Jonard)
- Fix custom field selection on item edition page (Benjamin Jonard)

## [1.0.1] / 2018-09-18
### Features
- Add SQL export (Benjamin Jonard)
- Add images export (Benjamin Jonard)
- Add tooltips on some images (Benjamin Jonard)

### Miscellaneous
- Update php dependencies (Benjamin Jonard)
- Fix deprecations (Benjamin Jonard)
- Switch from Gulp to Symfony Encore for managing assets (Benjamin Jonard)
- Update materializecss to v1.0.0 (Benjamin Jonard)
- Remove FOSJsRoutingBundle (Benjamin Jonard)
- Remove unused FontAwesome icons and inline the others (Benjamin Jonard)

### Refactoring
- Move javascript from Twig template to a JS file on statistics page (Benjamin Jonard)

## [1.0.0] / 2018-07-31
- Initial release (Benjamin Jonard)
