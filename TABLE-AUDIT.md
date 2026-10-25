# Mobile table audit — osTicket 1.18.4

Reviewed the bundled upstream staff/client screen templates and styles rather than assuming every table uses the ticket-queue structure. This inventory records source structures; it is not a claim that every authenticated workflow was tested.

- Regular list/queue/report tables become labelled cards, including dashboard tables with a header row inside tbody.
- Ticket/task cards keep their title and key configured fields visible; Details expands the remaining native cells. Desktop always shows all columns.
- Forms, nested two-column layouts and read-only account/ticket metadata stack below 1025px. Core controls and stored values are retained.
- Section-based/no-header lists (About and plugin installation) stack without inventing column labels.
- Message/editor content tables and print/export templates are excluded. Irregular record rows with spanning cells retain their content as full-width rows.

The sample previews cover representative structures, including users, organisations, agents, departments, teams, roles, help topics, SLA plans, email accounts, API keys, plugins, schedules, knowledgebase categories, canned responses, pages, custom lists, filters, banlist, system logs, email templates, dashboard, server information, plugin installation, forms and account details. Screens from a real installation, translated/custom headings and server operations still require staging checks.

| Source template | Tables | Classes / IDs |
| --- | --- | --- |
| `staff/apikey.inc.php` | 1 | `form_table` |
| `staff/apikeys.inc.php` | 1 | `list` |
| `staff/banlist.inc.php` | 1 | `list` |
| `staff/banrule.inc.php` | 1 | `form_table` |
| `staff/cannedresponse.inc.php` | 1 | `form_table fixed` |
| `staff/cannedresponses.inc.php` | 1 | `list` |
| `staff/categories.inc.php` | 1 | `list` |
| `staff/dashboard.inc.php` | 1 | `dashboard-stats table` |
| `staff/department.inc.php` | 2 | `form_table`, `two-column table` |
| `staff/departments.inc.php` | 1 | `list` |
| `staff/directory.inc.php` | 1 | `list` |
| `staff/dynamic-form.inc.php` | 2 | `form_table` |
| `staff/dynamic-forms.inc.php` | 1 | `list` |
| `staff/dynamic-list.inc.php` | 2 | `form_table` |
| `staff/dynamic-lists.inc.php` | 1 | `list` |
| `staff/email.inc.php` | 1 | `form_table` |
| `staff/emails.inc.php` | 1 | `list` |
| `staff/filter.inc.php` | 4 | `form_table` |
| `staff/filters.inc.php` | 1 | `list` |
| `staff/helptopic.inc.php` | 3 | `table`, `topic-forms table` |
| `staff/helptopics.inc.php` | 1 | `list` |
| `staff/org-view.inc.php` | 4 | `(unclassed)`, `ticket_info` |
| `staff/orgs.inc.php` | 1 | `list` |
| `staff/page.inc.php` | 1 | `form_table fixed` |
| `staff/pages.inc.php` | 1 | `list` |
| `staff/plugin-add.inc.php` | 1 | `list` |
| `staff/plugin.inc.php` | 1 | `form_table` |
| `staff/plugins.inc.php` | 1 | `list` |
| `staff/profile.inc.php` | 4 | `table two-column` |
| `staff/queue.inc.php` | 2 | `queue-sort table`, `table` |
| `staff/queues-ticket.inc.php` | 1 | `list` |
| `staff/role.inc.php` | 2 | `form_table`, `table` |
| `staff/roles.inc.php` | 1 | `list` |
| `staff/schedule.inc.php` | 1 | `form_table` |
| `staff/schedules.inc.php` | 1 | `list` |
| `staff/settings-agents.inc.php` | 2 | `form_table settings_table` |
| `staff/settings-alerts.inc.php` | 1 | `form_table settings_table` |
| `staff/settings-autoresp.inc.php` | 1 | `form_table settings_table` |
| `staff/settings-emails.inc.php` | 1 | `form_table settings_table` |
| `staff/settings-kb.inc.php` | 1 | `form_table settings_table` |
| `staff/settings-pages.inc.php` | 6 | `(unclassed)`, `form_table settings_table` |
| `staff/settings-system.inc.php` | 1 | `form_table settings_table` |
| `staff/settings-tasks.inc.php` | 2 | `form_table settings_table` |
| `staff/settings-tickets.inc.php` | 1 | `form_table settings_table` |
| `staff/settings-users.inc.php` | 2 | `form_table settings_table` |
| `staff/slaplan.inc.php` | 1 | `form_table` |
| `staff/slaplans.inc.php` | 1 | `list` |
| `staff/staff.inc.php` | 5 | `table`, `table two-column` |
| `staff/staffmembers.inc.php` | 1 | `list` |
| `staff/syslogs.inc.php` | 1 | `list` |
| `staff/system.inc.php` | 1 | `list` |
| `staff/tasks.inc.php` | 1 | `list` |
| `staff/team.inc.php` | 2 | `form_table`, `two-column table` |
| `staff/teams.inc.php` | 1 | `list` |
| `staff/template.inc.php` | 1 | `form_table` |
| `staff/templates/2fas.tmpl.php` | 1 | `table` |
| `staff/templates/assign.tmpl.php` | 1 | `(unclassed)` |
| `staff/templates/collaborators-preview.tmpl.php` | 1 | `(unclassed)` |
| `staff/templates/collaborators.tmpl.php` | 1 | `(unclassed)` |
| `staff/templates/confirm.tmpl.php` | 1 | `(unclassed)` |
| `staff/templates/delete.tmpl.php` | 1 | `(unclassed)` |
| `staff/templates/email-mailbox.tmpl.php` | 1 | `form_table` |
| `staff/templates/email-oauth2auth.tmpl.php` | 2 | `form_table` |
| `staff/templates/email-smtp.tmpl.php` | 1 | `form_table` |
| `staff/templates/field-edit.tmpl.php` | 1 | `(unclassed)` |
| `staff/templates/list-import.tmpl.php` | 1 | `list` |
| `staff/templates/list-item-preview.tmpl.php` | 1 | `custom-info` |
| `staff/templates/list-items.tmpl.php` | 1 | `form_table fixed` |
| `staff/templates/mark-as.tmpl.php` | 1 | `(unclassed)` |
| `staff/templates/merge-preview.tmpl.php` | 1 | `(unclassed)` |
| `staff/templates/org-delete.tmpl.php` | 1 | `(unclassed)` |
| `staff/templates/org-lookup.tmpl.php` | 2 | `(unclassed)`, `fixed` |
| `staff/templates/org-profile.tmpl.php` | 2 | `(unclassed)` |
| `staff/templates/org.tmpl.php` | 2 | `(unclassed)` |
| `staff/templates/plugin-instance.tmpl.php` | 1 | `form_table` |
| `staff/templates/plugin-instances.tmpl.php` | 1 | `form_table fixed` |
| `staff/templates/queue-columns.tmpl.php` | 1 | `table` |
| `staff/templates/queue-fields.tmpl.php` | 1 | `table` |
| `staff/templates/queue-preview.tmpl.php` | 1 | `list queue` |
| `staff/templates/queue-sorting.tmpl.php` | 1 | `table` |
| `staff/templates/queue-tickets.tmpl.php` | 1 | `list queue tickets` |
| `staff/templates/refer.tmpl.php` | 2 | `(unclassed)` |
| `staff/templates/release.tmpl.php` | 1 | `(unclassed)` |
| `staff/templates/schedule-diagnostic.tmpl.php` | 1 | `custom-info` |
| `staff/templates/schedule-entries.tmpl.php` | 1 | `form_table fixed` |
| `staff/templates/schedule-holidays.tmpl.php` | 1 | `form_table fixed` |
| `staff/templates/sequence-manage.tmpl.php` | 2 | `(unclassed)` |
| `staff/templates/task-preview.tmpl.php` | 3 | `(unclassed)`, `ticket_info` |
| `staff/templates/task-status.tmpl.php` | 1 | `(unclassed)` |
| `staff/templates/task-view.tmpl.php` | 7 | `(unclassed)`, `ticket_info`, `ticket_info custom-data` |
| `staff/templates/thread-email-recipients.tmpl.php` | 1 | `(unclassed)` |
| `staff/templates/ticket-preview.tmpl.php` | 4 | `(unclassed)`, `ticket_info` |
| `staff/templates/ticket-status.tmpl.php` | 1 | `(unclassed)` |
| `staff/templates/tickets.tmpl.php` | 1 | `list` |
| `staff/templates/transfer.tmpl.php` | 1 | `(unclassed)` |
| `staff/templates/user-account.tmpl.php` | 2 | `(unclassed)` |
| `staff/templates/user-delete.tmpl.php` | 1 | `(unclassed)` |
| `staff/templates/user-import.tmpl.php` | 1 | `list` |
| `staff/templates/user-lookup.tmpl.php` | 2 | `(unclassed)`, `fixed` |
| `staff/templates/user-register.tmpl.php` | 1 | `(unclassed)` |
| `staff/templates/user.tmpl.php` | 3 | `(unclassed)`, `custom-info` |
| `staff/templates/users.tmpl.php` | 1 | `list` |
| `staff/templates.inc.php` | 1 | `list` |
| `staff/ticket-edit.inc.php` | 3 | `form_table`, `form_table dynamic-forms` |
| `staff/ticket-open.inc.php` | 2 | `(unclassed)`, `form_table fixed` |
| `staff/ticket-relations.inc.php` | 1 | `list` |
| `staff/ticket-tasks.inc.php` | 1 | `list` |
| `staff/ticket-view.inc.php` | 9 | `(unclassed)`, `ticket_info`, `ticket_info custom-data` |
| `staff/user-view.inc.php` | 4 | `(unclassed)`, `ticket_info` |
| `staff/users.inc.php` | 1 | `list` |
| `client/edit.inc.php` | 1 | `(unclassed)` |
| `client/open.inc.php` | 1 | `(unclassed)` |
| `client/profile.inc.php` | 1 | `padded` |
| `client/register.inc.php` | 1 | `padded` |
| `client/tickets.inc.php` | 1 | `ticketTable` |
| `client/view.inc.php` | 4 | `custom-data`, `infoTable`, `ticketInfo` |
