# Changelog
All notable changes to this project will be documented in this file.

## [v1.1.0] / 2020-04-29
### Important
- Update PHP minimum version to 7.4  (Benjamin Jonard)
- Update to Symfony 5  (Benjamin Jonard)
- Update to Twig 3  (Benjamin Jonard)
- Add support of Mysql for version 8 and superior  (Benjamin Jonard)

### Features
- Dark mode  (Benjamin Jonard)
- Suggest new item name based on other items in the collection  (Benjamin Jonard)
- Webp format support for images  (Benjamin Jonard)
- Add a medium thumbnail on images to reduce mobile data consumption  (Benjamin Jonard)
- Support for all currencies  (Benjamin Jonard)
- Add a field `Country` for items  (Benjamin Jonard)
- Users can choose how dates should be displayed  (Benjamin Jonard)
- Add Inventory  (Benjamin Jonard)
- Tags can now be assigned to a category  (Benjamin Jonard)
- Admins can do a full backup : database + images  (Benjamin Jonard)
- Admins can clean obsoletes images (Benjamin Jonard)
- Tag autocompletion will now priotarize words starting the same way as the word searched  (Benjamin Jonard)
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
- Fix floating label on cropper when using Firefox (Benjamin Jonard)
- Fix streamed responses when downloading backups (Benjamin Jonard)
- Fix infinite loading bar (Benjamin Jonard)

## [v1.0.4] / 2019-03-06
### Fixes
 - Improve header responsiveness (Benjamin Jonard)
 
### Features
 - Add a scrollbar on navbar for mobile devices (Benjamin Jonard)
 - Add hidden menu for mobile devices (Benjamin Jonard)
 
### Miscellaneous
- Update dependencies and config files (Benjamin Jonard)
- Switch to the new .env system (with .env.local) (Benjamin Jonard)
- Update phpUnit version (Benjamin Jonard)

## [v1.0.3] / 2019-02-26
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
- Use referer for redirection after login (Benjamin Jonard)

### Miscellaneous
- Upgrade Encore version (Benjamin Jonard)
- Upgrade to Symfony 4.2 (Benjamin Jonard)
- Update some english translations for proper plurals handling (Benjamin Jonard)
- Update general style for header (Benjamin Jonard)
- Rework search input style (Benjamin Jonard)
- Replace all generateUrl with redirectToRoute (Benjamin Jonard)
- Replace deprecated TranslatorInterface (Benjamin Jonard)
- Add debug env for local docker (Benjamin Jonard)

 
## [v1.0.2] / 2018-09-28
### Fixes
- Fix error pages style (Benjamin Jonard)
- Fix sortable fields on item edition page (Benjamin Jonard)
- Fix custom field selection on item edition page (Benjamin Jonard)

## [v1.0.1] / 2018-09-18
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

## [v1.0.0] / 2018-07-31
- Initial release (Benjamin Jonard)
