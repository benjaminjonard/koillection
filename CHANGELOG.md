# Changelog
All notable changes to this project will be documented in this file.

## [v1.0.3] / 2019-02-26
### Fixes
- Fix recompute disk usage action
- Fix collection placeholder style
- Fix item images margins
- Fix signs linked to a private item
- Fix date search
- Fix deprecations on transchoices
- Fix twig deprecations in blocks

### Features
- Add action filter on history page
- Use referer for redirection after login

### Miscellaneous
- Upgrade Encore version
- Upgrade to Symfony 4.2
- Update some english translations for proper plurals handling
- Update general style for header
- Rework search input style
- Replace all generateUrl with redirectToRoute
- Replace deprecated TranslatorInterface
- Add debug env for local docker

 
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
