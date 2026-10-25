# SoupDesk — a free responsive theme for osTicket

A free, flat, responsive theme plugin for the customer portal, agent workspace and admin panel. Built by Soup Digital, with editable instance colours and an optional text wordmark. No core template edits, external fonts or server-side build tools required.

**Release: 1.1.0-beta.9. Developed and locally tested against osTicket 1.18.4.** Other osTicket versions are unverified. This is an independent community project, not an official osTicket product.

## Free, open source and community driven

SoupDesk is a completely free, open-source project by [Soup Digital](https://soup.digital). The aim is to make osTicket more comfortable to use on modern desktops, tablets and phones, and to keep improving it with the people who use it.

Everyone is welcome to use, amend, fork and contribute to the theme under its **GPL-2.0-or-later** licence. Keep the licence and applicable copyright notices with redistributed copies. There is no paid edition or donation requirement to use the included features.

## Does this overwrite osTicket?

**No. Install this as a plugin: do not replace osTicket's core files, templates, stylesheets or JavaScript.**

- The plugin lives in its own `include/plugins/soupdesk-osticket-theme/` directory.
- osTicket continues to generate its original pages and run its own authentication, permissions, ticket handling and form submissions.
- The plugin adds its CSS and optional presentation JavaScript to qualifying HTML responses. Its browser enhancements add navigation controls, wrap tables and annotate existing cells for responsive layouts; they do not replace the application with a separate set of page templates.
- Ticket links, form fields, values and existing controls are retained. No ticket/user data migration or database schema change is included. Plugin settings are stored through osTicket's normal configuration system.
- Disable the plugin and reload to return to the underlying osTicket appearance. No core-file restoration is required.

This is a presentation layer, not a replacement osTicket installation. It changes the rendered HTML/DOM for presentation, so it would be inaccurate to say the markup is entirely untouched. Core updates and other plugins can still change markup or styles and require compatibility fixes; test upgrades on staging.

## Features

- Separate customer and staff stylesheets and independent enable switches.
- Mobile off-canvas navigation, keyboard controls, labelled admin/customer records and compact ticket/task summaries with expandable details.
- Responsive ticket creation, full-width reply editors and corrected modal stacking.
- Mobile/tablet staff profile tabs and customer profile forms, including nested fixed-width tables, avatar, preferences and signature editing.
- Flat appearance without box or text shadows.
- Thirteen colour pickers using osTicket's native instance settings, including primary buttons, navigation, text, backgrounds and borders.
- Keeps your configured osTicket logo by default; optional editable text wordmark.
- Nested SCSS source and precompiled CSS. Colour changes require no compilation.

## Install

Use an existing, working osTicket installation. This release was developed against **osTicket 1.18.4**; other versions are unverified. The server needs only osTicket's own supported PHP environment and read access to this plugin's files. Node and Composer are not required for installation.

Try a staging installation first. Download and extract the release ZIP. If using GitHub's source archive, rename the extracted plugin folder to `soupdesk-osticket-theme` before uploading it; avoid an extra nested directory.

1. Place this folder at `include/plugins/soupdesk-osticket-theme/`. `plugin.php` must be directly inside that folder.
2. Open **Admin Panel → Manage → Plugins → Add New Plugin** and install **SoupDesk**.
3. Set the plugin to **Active**.
4. In **Instances**, create an instance, set it to **Enabled**, choose the panel switches and save.
5. Fully reload the customer and staff panels. osTicket needs both an active plugin and an enabled instance.

Only one enabled instance is supported because one helpdesk uses one shared appearance. Instance settings apply to both panels; panel enable switches remain independent.

## Lists, forms and details on mobile/tablet

At viewport widths up to **1024px**, regular osTicket lists become labelled cards: users, organisations, agent directory, departments, teams, roles, help topics, SLA plans, emails, API keys, plugins/instances, schedules, knowledgebase lists, filters, logs and other lists sharing the native column structures. Dashboard reports support headings in their first body row. Section-based tables such as server information and the plugin installer stack without invented labels.

Ticket cards show the existing title/subject, priority and customer/requester fields where present. **Details** and its chevron expand/collapse the other configured cells. Customer lists without priority/customer columns show their existing status and ticket number instead. Task summaries show their title, department/agent and priority when available. Summary identification uses native headings and descriptive links; unknown/custom headings fall back to existing fields rather than hiding the whole record.

All original columns remain available. The plugin does not retrieve additional private fields or add absent queue columns. Expanded state and selected checkboxes survive a presentation refresh. Phone metadata uses two columns; tablets from 640px use three.

**Sort columns** expands original interactive column headings. Native links, checkboxes, bulk-selection controls and values stay in place. New controls are type=button and do not submit forms. Above 1024px all columns are visible in the desktop table layout and the mobile Details controls disappear.

Fixed-width forms, nested account/organisation detail layouts, customer registration/editing, profile tabs and ticket metadata also stack. Rich-text/message tables and print/export templates are excluded. The JavaScript enhancement switch must be enabled for automatic table classification; without it, original record tables remain available. The profile-specific CSS remains independent.

See **TABLE-AUDIT.md** for the source inventory and coverage boundaries. Local previews demonstrate representative native structures; custom plugins, translated headings and real server actions need staging checks.

## Choose your colours and branding

Open **Admin Panel → Manage → Plugins → SoupDesk → Instances → your instance**. Set the colour pickers and click **Save Changes**. The saved settings response uses the new palette immediately. Other already-open tabs need a normal reload; staff PJAX navigation retains the current document styles. A hard refresh is not required for colour changes.

| Setting | Default |
| --- | --- |
| Text / charcoal | `#2D2E2D` |
| Panel background | `#FAF9F6` |
| Muted text | `#808080` |
| Page background | `#F2F2F2` |
| Borders | `#E5E5E5` |
| Secondary text | `#707070` |
| Subtle panel background | `#F9F9F9` |
| Primary buttons / accent | `#FFC107` |
| Primary hover / selection | `#FFE69B` |
| Primary button text | `#2D2E2D` |
| Navigation background | `#2D2E2D` |
| Navigation text | `#FAF9F6` |
| Navigation hover | `#454645` |

Use the pickers, or supported browser hex entry. The server accepts three- or six-digit hex values and rejects other input. Invalid historical values fall back safely to defaults. **Restore default colours** fills all thirteen pickers with the original Soup palette; click **Save Changes** to apply immediately. The native Reset button is hidden on this plugin’s instance form. Use Cancel to leave without saving. Reset buttons on other osTicket forms are unaffected. The palette is designed for light themes; changing colours does not provide a complete dark mode. Check text contrast when choosing your own colours. Error, success and warning treatments retain their semantic colours.

Leave **Show text wordmark** off to retain your osTicket logo. Turn it on and edit **Text wordmark** to use text instead. No Soup Digital logo is forced onto your helpdesk.

## Update an existing installation

**Existing installations:** keep the plugin in its currently installed directory, including `include/plugins/soup-digital-theme/` if you installed an earlier package. Do not rename an installed plugin folder or install a duplicate. The new download/repository folder name is for new installations. Copy the new folder’s contents into your existing plugin directory.

Overwrite only the files inside your existing `include/plugins/soupdesk-osticket-theme/` directory with the new release. Keep the installed plugin and its instance; do not reinstall or create a second instance. Existing settings remain stored in osTicket. Reload open customer/staff pages after uploading the files.

## Upgrade from 1.0.x

Overwrite the files in your **existing** `include/plugins/soupdesk-osticket-theme/` folder; retain the plugin and instance. Do not reinstall or create a second instance. Existing panel switches and wordmark preferences remain stored in osTicket. New colour settings default to the Soup Digital palette. If you previously enabled the fixed Soup Digital wordmark, enter `soup digital.` in the new Text wordmark field. Fully refresh both panels.

The internal plugin ID, PHP class names, folder name and `sd-` CSS hooks are intentionally retained for upgrades. The displayed plugin name is now SoupDesk.

## Build and test

Node is required only to modify SCSS locally:

```bash
npm ci
npm run build
php tests/renderer-test.php
php tests/palette-test.php
php tests/save-test.php
```

Shared styles are in `scss/_shared.scss`; panel styles in `scss/customer.scss` and `scss/staff.scss`. Runtime colours are CSS custom properties with defaults in `scss/_variables.scss`; their settings definitions and validation live in `palette.php`.

Compiled CSS is committed so installations need no Node or Composer. The GitHub workflow checks PHP syntax, renderer/palette tests and that rebuilding Sass leaves compiled CSS unchanged.

## Local previews

The preview HTML contains sample data, not real tickets. Reference assets from osTicket are deliberately **not bundled**. Supply them from your local osTicket 1.18.4 source/install:

```bash
python3 scripts/prepare-previews.py /path/to/osTicket
```

Then open `preview/index.html`. Keep copied third-party assets local; `preview/legacy/` is ignored by Git. These examples are presentation fixtures, not a functioning helpdesk. Their forms do not submit.

## Loading and limitations

In osTicket 1.18.4, plugin bootstrap runs before the global `$ost` is assigned, and the staff login header does not render `getExtraHeaders()`. The plugin uses a scoped output-buffer callback to add classes, a missing viewport meta tag, the matching CSS and optional presentation JavaScript to complete HTML pages.

Assets are injected inline from local files, avoiding public access to `include/plugins/`. A custom Content Security Policy blocking inline styles/scripts needs an alternative loader. API, AJAX/PJAX, downloads, non-HTML, encoded and early-streamed responses are left untouched. Printing retains the original screen-independent appearance. Enhancements do not fetch data, store data or submit forms.

Local checks are not a full authenticated osTicket integration test. Before production, check sign-in, ticket creation/replies, rich-text editing, attachments, custom fields, search/sort, bulk actions, dialogs, mobile navigation and any other plugins. See `VALIDATION.md`.

## Disable

Disable the instance/plugin or untick either panel switch, then refresh. Emergency recovery: create an empty `DISABLED` file beside `plugin.php`. Remove it to restore the configured theme. No database changes are needed.

## Publish on GitHub

Extract this package and upload the **contents** of `soupdesk-osticket-theme/` as the repository root. A suggested repository name is `soupdesk-osticket-theme`. Include dotfiles such as `.github/` and `.gitignore`. Do not commit a helpdesk installation, configuration credentials or customer screenshots. The package contains only plugin source, sample fixtures and project documentation.

Create a GitHub release tagged `v1.1.0-beta.9` and attach this installable ZIP. Users should install the ZIP's `soupdesk-osticket-theme/` folder; a GitHub auto-generated source archive may have a different outer directory name. `CHANGELOG.md` supplies the release notes.

## Help develop the theme

Developers, designers, testers and osTicket users are all welcome. Help with PHP, JavaScript, SCSS, accessibility, browser testing, documentation and compatibility with other osTicket versions is appreciated.

Fork this repository, make a focused change and open a pull request explaining what it fixes and how you checked it. Edit the SCSS sources and rebuild the committed CSS rather than changing compiled CSS alone. Keep changes in the plugin and preserve osTicket's native controls and workflows. See [CONTRIBUTING.md](CONTRIBUTING.md) for development and testing guidance.

## Request features and report problems

**Please actively request the features and improvements you would find useful.** Open an issue using this repository's **Issues** tab. We want reports about awkward layouts, missing responsive screens, accessibility problems and ideas that would make day-to-day support easier. Suggestions help guide future updates; a request is not a promise of a delivery date.

For a bug report, include:

- Your osTicket, PHP and browser versions.
- Whether the problem is in the customer, agent or admin panel, and the affected page.
- Steps to reproduce, expected behaviour and what actually happened.
- Screen/device width and a redacted screenshot where useful.
- Theme version and any other plugins that might affect the page.

For a feature request, explain the problem, who it affects and your preferred outcome. Please search existing issues first and keep separate problems in separate issues. Never post passwords, API keys, session/CSRF tokens or identifiable customer/ticket content in a public report.

## Support the project

**Completely free to use, modify and share under the GPL licence. No payment is required.**

If you enjoy using the theme, please feel free to [buy me a coffee](https://buymeacoffee.com/soupdigital). It is entirely optional, and everyone is welcome to use and contribute to the project without donating.

Donations are optional and do not purchase support, feature priority or a guaranteed response. Testing, useful bug reports, feature requests, documentation and pull requests are equally welcome ways to help.

## Licence and acknowledgements

Original plugin code and theme styles are **GPL-2.0-or-later**; see [LICENSE.txt](LICENSE.txt). Supplied without warranty. Third-party reference assets retain their own licences and notices; see [THIRD-PARTY-NOTICES.md](THIRD-PARTY-NOTICES.md).

osTicket remains the underlying helpdesk application. This is an independent community theme by Soup Digital, not an official osTicket product.
