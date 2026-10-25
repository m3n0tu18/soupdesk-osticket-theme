# Contributing

Please report the osTicket, PHP and browser versions; panel and page; viewport width; steps to reproduce; expected and actual behaviour. Redact ticket content, names, email addresses, URLs containing tokens and all credentials from screenshots or markup.

Keep changes scoped to presentation, retain existing form values and native event handlers, and preserve the plugin identity for upgrades. Do not change authentication, ticket permissions or submission behaviour. Maintain nested SCSS and rebuild both committed stylesheets with `npm ci && npm run build`.

Run the PHP tests described in README.md. Check desktop and narrow mobile widths, keyboard navigation, the drawer, modal layering, editing and dynamic fields on an osTicket staging installation. Compatibility claims should include the exact version and workflows tested. Add focused regression coverage for behavioural changes.

Keep third-party preview assets and real helpdesk data out of pull requests. Contributions are licensed under the project's GPL-2.0-or-later licence.
