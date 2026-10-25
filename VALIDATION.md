## 1.1.0-beta.9

84 palette/settings checks (including stable, unique IDs across repeated option construction), 35 renderer checks and 12 save-lifecycle checks passed. Source review confirms CheckboxWidget submits IDs and addInstance rebuilds configuration forms. Actual first-time creation still needs confirmation on an installed helpdesk.

## 1.1.0-beta.8

Browser check at 390px confirmed the customer caption fits horizontally and drawer hover uses yellow with charcoal text. Both Sass outputs compiled. Desktop rules unchanged.

# Broader table audit and compact summaries — 1.1.0-beta.7

Source inventory: 116 non-print/export staff/client templates containing tables in osTicket 1.18.4. TABLE-AUDIT.md lists their structures. This is source coverage, not 116 authenticated live workflow tests.

Local verification completed:

- 98 browser assertions covering 21 common list fixture types, plugin instances, a tbody-header dashboard, section/no-header lists, nested forms/details, staff tickets/tasks and customer records at 320, 390, 768, 1024 and 1280px. Checks include page overflow, record labels, checkbox retention, expansion/collapse, preserved expansion state, native-node preservation, desktop restoration, dynamic rows, unique ARIA targets, message-table exclusion and native hidden/inline-show behaviour.
- Customer-specific check confirms status and ticket number remain in the collapsed summary, while secondary fields expand with Details.
- Existing 53 record-list checks, 38 profile assertions, 108 editor/form/dialog checks and 10 toolbar/drawer checks passed during this revision.
- Both Sass outputs compiled and enhancement JavaScript passed syntax validation.
- Inspected phone previews for admin lists/forms, compact tickets and completed tasks.

Live bulk operations, server-side sorting, custom/translated queue headings, third-party plugins and assistive-technology behaviour still need verification on an installed helpdesk. No server data was fetched or altered, and no customer screenshots or credentials are bundled.

---

# Profile form responsiveness — 1.1.0-beta.6

- 38 browser assertions across source-based staff and customer profile fixtures at 320, 390, 640, 768, 1024 and 1280px: viewport/field fit, all three staff tabs, real Redactor signature initialization, retained edited values, desktop markup and no-JavaScript profile sizing.
- Additional viewport checks at 1025, 1100, 1180 and 1280px showed no page overflow.
- Inspected the staff phone layout, including avatar controls, first/last names, phone extension, authentication controls and footer actions.
- Both Sass panel stylesheets compiled successfully. Profile changes are scoped to the native profile form action and recognised layout-table classes; signature content tables are excluded.

Fixtures preserve upstream fixed dimensions and sample controls. Live profile saving, avatar upload, password/2FA dialogs and custom account fields still need installation verification.

---

# Responsive record lists — 1.1.0-beta.5

- 53 browser checks across staff ticket/queue, native completed-task and customer ticket fixtures at 320, 390, 768, 1024 and 1280px: no page/card horizontal overflow, labelled fields, sort visibility, selection persistence, dynamically inserted rows, native link node/listener preservation and desktop restoration.
- Existing 10 toolbar/drawer and 108 ticket/editor/form/dialog regression assertions passed.
- Inspected phone previews for ticket status/priority, completed-task titles/metadata and customer ticket cards.
- Both Sass stylesheets compiled and enhancement JavaScript passed syntax checking.

These checks use local sample fixtures and upstream 1.18.4 assets. Live bulk actions, server-side sorting, translated/custom queue configurations and assistive-technology behaviour still need verification on an installed helpdesk. The implementation keeps original links, controls and form values in place.

---

# Settings button visibility — 1.1.0-beta.4

The supplied native button markup was checked in a browser with the widget's scoped CSS: only the theme instance Reset control is hidden. Save Changes, Cancel, Restore default colours and another form's Reset remain visible. The PHP widget passed syntax validation. No save, navigation or palette logic changed.

---

# Save response validation — 1.1.0-beta.3

- Twelve save lifecycle checks model separate controller/bootstrap config objects, successful and failed writes, instance isolation, repeated partial writes, updated branding and panel enable switches.
- An HTTP POST fixture with stubbed osTicket persistence returned the newly saved palette in the same response. Fifteen route/content lifecycle checks and emergency disable passed.
- Existing 65 palette/settings and 35 renderer checks passed during implementation.
- PHP syntax checks passed. This release changes configuration timing, with no stylesheet or navigation changes.

The HTTP fixture uses actual plugin code and stubbed osTicket persistence; live authenticated saving needs installation verification. Other open browser tabs retain their loaded styles until a normal reload.

---

# Reset controls validation — 1.1.0-beta.2

- Six browser scenarios at 390px and 1280px using output from the PHP settings widget: restore all thirteen defaults, retain other unsaved settings, avoid automatic submit, native reset to saved values, and respect a canceled reset.
- Reset synchronizes picker displays without emitting changes that would reactivate osTicket's unsaved-changes warning.
- 65 palette/settings checks and 35 renderer checks passed.
- PHP and settings JavaScript syntax checks passed.
- Settings controls are rendered by their widget independently of the panel enhancement script.

Live instance save/reload still requires a staging check. Previous validation follows.

---

# Public beta validation — 1.1.0-beta.1

This version adds runtime colours and configurable branding to the 1.0.3 theme. Local checks completed:

- 35 renderer checks: routes, document guards, preserved form content and injection switches.
- 65 palette/settings checks: native colour field definitions and validators, accepted/invalid hex input, defaults, branding escaping, both panels and logo defaults.
- Four browser scenarios: custom blue/white palette and quoted/Unicode branding at 390px and 1280px in customer and staff panels; computed colours, wordmark rendering and page overflow checked.
- Ten mobile toolbar/drawer checks and 108 reply/editor/form/caret/dialog/drawer regression assertions using source-based fixtures.
- Fifteen HTTP lifecycle checks with osTicket parent classes stubbed, plus emergency disable.
- PHP syntax, enhancement JavaScript syntax and both Sass builds passed.
- Preview asset preparation from the local osTicket 1.18.4 source succeeded.

The added GitHub workflow has not run on a hosted repository yet. Tests are local fixtures and stubs, not a connected authenticated helpdesk. Live instance save/reload, ticket operations, uploads and third-party plugin interactions need staging verification. Compatibility beyond osTicket 1.18.4 is unverified.

Previous detailed validation follows for context; older counts describe those releases.

---

# Validation — 25 October 2026

Target source: official osTicket **v1.18.4**, plus the supplied dashboard HTML and the linked customer/staff CSS. This package has not been installed on support.soup.digital.

## Completed

- Compiled both nested SCSS entrypoints with Dart Sass 1.106.1.
- PHP 8.3 syntax checks on the plugin PHP files.
- JavaScript syntax check on the enhancement script.
- 35 automated renderer assertions: panel classes, existing HTML attributes, viewport handling, no duplicate injection, CSRF/message/script preservation, non-HTML responses, downloads, fragments, configuration switches, missing-asset fallback and root/subdirectory route detection.
- 15 HTTP checks using PHP 8.3's development server and minimal stubs for the osTicket parent classes. Checked injection on customer/staff/login/KB/pages controllers, exclusions for API/AJAX/files/fragments/JSON/CSV, PJAX requests and independent panel switches.
- Filesystem emergency-disable check.
- Browser layout checks on ten representative static pages at 390, 768 and 1440 CSS pixels wide. No document-level horizontal overflow or JavaScript exceptions detected; wide tables scroll within their containers.
- Additional browser interaction checks for mobile main/submenu controls, Escape behaviour, mobile dialog width, dynamic table insertion without duplicate wrapping, retained checkbox state and draft/CSRF values, and navigation without JavaScript.
- Layout checks on the supplied dashboard HTML at 320, 390, 768 and 1440 CSS pixels wide.
- Visual inspection and refinement of desktop/mobile customer home, ticket form, staff queue, ticket detail, admin settings and staff login layouts.

## Still requires staging verification

The HTTP harness does not include an installed osTicket database or exercise its real plugin discovery/configuration UI. The static browser fixtures reproduce relevant v1.18.4 markup but do not execute the complete application's JavaScript or server-side ticket flows.

Verify real plugin installation and instance configuration, logins/2FA, permission-specific menus, queue behaviours, replies/notes, dynamic forms, Redactor, uploads, datepickers, third-party plugins and all live workflows on staging. Other osTicket versions, custom themes and restrictive custom CSP configurations have not been certified.


## Version 1.0.1 refinements

Added source-based preview fixtures for the native queue, the native overflow widget, task search and simple plugin configuration forms. Checked queue/search geometry, Font Awesome font selection and glyph mappings, fluid queue width, and native-widget resizing/overflow access at 320, 390, 500, 768, 1280 and 1440 pixels. Checked flat styling, including focused and hovered states. New navigation icons use inline SVG. The source-based previews include only illustrative data.


## Version 1.0.2 ticket/detail refinements

Checked source-based reply and staff ticket-creation fixtures using the actual bundled osTicket v1.18.4 Redactor JavaScript. Tests cover editor count/width/inset, typing and draft preservation through theme refresh, dropdown caret alignment, modal stacking and geometry, and off-canvas position/background restoration at 320, 390, 500, 768, 1280 and 1440 pixels. Popup fixture centering follows the application's calculated centering rather than a fixed left percentage. Mobile rows no longer keep their legacy fixed height. Existing browser, flat-style, native queue and PHP checks are retained. These are local tests; authenticated live osTicket flows still require verification on your installation.


## Version 1.0.3 mobile toolbar fix

Added a native-class toolbar fixture with nested `.action-button.pull-right` controls and `.icon-caret-down.pull-right` carets. Ten targeted checks passed across 320, 390, 500, 760, 768 and 1280 pixel viewports: no document overflow, compact button widths, compact/centered carets and working mobile drawer visibility. No off-canvas navigation code was changed. The user's very long screenshot was received at only 37 pixels wide, so detailed thread content in that attachment could not be assessed.
