# Changelog

## 1.1.0-beta.9

- Give every instance setting a stable field ID so osTicket can match checkbox submissions across the repeated form construction used during first-time instance creation.
- Existing saved setting keys remain unchanged. Reload the settings page after updating before submitting an older open form.

## 1.1.0-beta.8

- Pair mobile navigation hover/focus background and text colours across both panels.
- Display native table captions as full-width blocks above mobile records.

## 1.1.0-beta.7

- Extend mobile labelled cards to standard staff/admin lists, plugin instances and dashboard reports, including header rows inside tbody.
- Add compact ticket/task summaries with a Details chevron for secondary columns; preserve native cell nodes, controls, expanded state and desktop visibility.
- Stack core form, account, organisation, customer registration and ticket metadata tables, including nested fixed-width structures.
- Stack section/no-heading lists without guessing labels; retain native hidden/show behaviour and exclude message/editor content.
- Add a source inventory covering 116 upstream screen templates and representative admin/customer table previews.

## 1.1.0-beta.6

- Stack staff and customer profile layout tables and constrain inline field widths through 1024px.
- Handle staff Account, Preferences and Signature tabs, nested name/contact tables, avatar controls and long select options.
- Wrap profile actions while preserving field values, native handlers and desktop columns.
- Profile CSS also works without responsive enhancement JavaScript; signature content tables remain excluded.

## 1.1.0-beta.5

- Convert staff ticket/task and customer ticket lists into labelled record cards up to 1024px; use two metadata columns on phones and three from 640px.
- Show full-width subjects/titles and keep configured status/priority fields immediately below them when recognised.
- Retain every configured field, native links, checkboxes and bulk controls without cloning or replacing records.
- Expose native column sorting through a mobile Sort columns control; restore the existing desktop table layout above 1024px.
- Support new rows/PJAX refreshes and retain horizontal scrolling for unrelated table types.

## 1.1.0-beta.4

- Hide the native Reset button with CSS only on this theme’s instance form. Save Changes, Cancel and Restore default colours remain visible.
- Remove the reference to the hidden button from the settings hint.

## 1.1.0-beta.3

- Render the saved palette on the settings-save response rather than using the colours captured at bootstrap.
- Share successful configuration writes between config objects for the same instance within the request; failed writes do not replace the rendered palette.
- Other open tabs need a normal reload; no hard refresh is needed for colour changes.

## 1.1.0-beta.2

- Add a separate Restore default colours button to the instance settings; Save Changes applies the restored palette.
- Keep native Reset for discarding unsaved edits and synchronize enhanced colour swatches.
- Settings controls work with the theme and responsive enhancements disabled.

## 1.1.0-beta.1

First general-purpose public beta, built on the Soup Digital theme:

- Add thirteen instance colour pickers with validated CSS custom properties.
- Add editable, optional text branding; retain configured osTicket logos by default.
- Keep existing plugin identifiers and options for 1.0.x upgrades.
- Preserve the flat styling, off-canvas navigation and responsive fixes from 1.0.3.
- Add public installation, configuration, contribution and compatibility documentation, PHP/palette tests and a GitHub check workflow.
- Exclude third-party preview assets from the release; support preparing them from a local osTicket source tree.

Developed against osTicket 1.18.4. Live integration testing on other versions remains open.

## 1.0.3

Fix mobile action buttons and nested dropdown carets stretching to full width.

## 1.0.2

Add off-canvas navigation; fix editor widths, reply fields, ticket creation stacking, dropdown carets and modal layering.

## 1.0.1

Remove shadows; improve search, native queue navigation and icons.

## 1.0.0

Initial Soup Digital customer and staff theme plugin.
