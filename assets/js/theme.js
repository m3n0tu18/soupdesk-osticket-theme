/* Copyright (C) 2026 Soup Digital and contributors. SPDX-License-Identifier: GPL-2.0-or-later */
/* Soup Digital presentation enhancements. No fetches, storage or form submissions. */
(function () {
    'use strict';

    // PJAX or an accidental duplicate script must not register handlers twice.
    if (window.SoupDigitalTheme) {
        window.SoupDigitalTheme.refresh();
        return;
    }

    var scheduled = false;
    var queueWidths = new WeakMap();
    var recordTableId = 0;
    var drawerInert = new Map();

    function setDrawer(open, returnFocus) {
        var nav = document.getElementById('nav');
        var trigger = document.getElementById('sd-nav-toggle');
        if (!nav || !trigger) { return; }
        open = Boolean(open && window.matchMedia('(max-width: 760px)').matches);
        document.body.classList.toggle('sd-nav-open', open);
        trigger.setAttribute('aria-expanded', String(open));
        // Keep the header control's width stable; the drawer has its own close button.
        menuLabel(trigger, false);
        drawerInert.forEach(function (value, element) { element.inert = value; });
        drawerInert.clear();
        if (open) {
            // Inert only sibling branches; keep the original navigation in its PJAX tree.
            var branch = nav;
            while (branch.parentElement) {
                Array.from(branch.parentElement.children).forEach(function (sibling) {
                    if (sibling !== branch && sibling.id !== 'sd-menu-backdrop'
                        && !sibling.matches('script, style, link')) {
                        drawerInert.set(sibling, sibling.inert);
                        sibling.inert = true;
                    }
                });
                branch = branch.parentElement;
                if (branch === document.body) { break; }
            }
            nav.setAttribute('role', 'dialog');
            nav.setAttribute('aria-modal', 'true');
            nav.querySelector('.sd-drawer-close').focus();
        } else {
            nav.removeAttribute('role');
            nav.removeAttribute('aria-modal');
            if (returnFocus) { trigger.focus(); }
        }
    }

    function enhanceDrawer(nav) {
        if (!nav.querySelector('.sd-drawer-heading')) {
            var heading = document.createElement('li');
            heading.className = 'sd-drawer-heading';
            var title = document.createElement('span');
            title.textContent = 'Navigation';
            var close = document.createElement('button');
            close.type = 'button';
            close.className = 'sd-drawer-close';
            close.setAttribute('aria-label', 'Close navigation');
            close.appendChild(icon('M6 6l12 12M18 6L6 18'));
            close.addEventListener('click', function () { setDrawer(false, true); });
            heading.append(title, close);
            nav.insertBefore(heading, nav.firstChild);
            nav.addEventListener('click', function (event) {
                if (event.target.closest('a') && document.body.classList.contains('sd-nav-open')) {
                    setDrawer(false, false);
                }
            });
        }
        if (!document.getElementById('sd-menu-backdrop')) {
            var backdrop = document.createElement('button');
            backdrop.id = 'sd-menu-backdrop';
            backdrop.className = 'sd-menu-backdrop';
            backdrop.type = 'button';
            backdrop.tabIndex = -1;
            backdrop.setAttribute('aria-label', 'Close navigation');
            backdrop.addEventListener('click', function () { setDrawer(false, true); });
            document.body.appendChild(backdrop);
        }
    }
    var resizeObserver = typeof ResizeObserver === 'function'
        ? new ResizeObserver(updateScrollRegions) : null;

    function updateScrollRegions() {
        document.querySelectorAll('.sd-table-scroll').forEach(function (region) {
            var scrolls = region.scrollWidth > region.clientWidth + 2;
            region.tabIndex = scrolls ? 0 : -1;
            region.classList.toggle('sd-can-scroll', scrolls);
            var before = region.previousElementSibling;
            var hint = before && before.classList.contains('sd-list-sort-toggle') ? before.previousElementSibling : before;
            if (!hint || !hint.classList.contains('sd-table-hint')) {
                hint = document.createElement('p');
                hint.className = 'sd-table-hint';
                hint.textContent = 'Scroll horizontally to see all columns.';
                region.parentNode.insertBefore(hint, before && before.classList.contains('sd-list-sort-toggle') ? before : region);
            }
            hint.hidden = !scrolls;
        });
    }

    // Our own UI icons are SVG, so they do not depend on a font download.
    function icon(path) {
        var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('viewBox', '0 0 24 24');
        svg.setAttribute('class', 'sd-ui-icon');
        svg.setAttribute('aria-hidden', 'true');
        svg.setAttribute('focusable', 'false');
        var shape = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        shape.setAttribute('d', path);
        svg.appendChild(shape);
        return svg;
    }

    function menuLabel(button, open) {
        button.replaceChildren(
            icon(open ? 'M6 6l12 12M18 6L6 18' : 'M4 6h16M4 12h16M4 18h16'),
            document.createTextNode(open ? 'Close menu' : 'Menu')
        );
    }

    function enhanceQueues() {
        var menu = document.getElementById('customQ_nav');
        if (!menu || !window.jQuery) {
            return;
        }
        var instance = window.jQuery(menu).data('jb-overflowmenu');
        var width = Math.round(menu.clientWidth);
        // The stock widget caches its initial width; refresh it only when that changes.
        // Cache before refresh, because moving queue items triggers our DOM observer.
        if (instance && width > 0 && queueWidths.get(menu) !== width) {
            queueWidths.set(menu, width);
            instance.primaryMenuWidth = width;
            instance.refresh();
        }
        var more = menu.querySelector('.jb-overflowmenu-menu-secondary-handle');
        if (more) {
            more.setAttribute('aria-label', 'More ticket queues');
        }
    }

    var recordRowId = 0;
    var boundExpanders = new WeakSet();
    var recordRowKeys = new WeakMap();
    var excludedTables = '.thread-body, .redactor-in, .redactor_editor, .redactor-editor, .redactor-toolbar, .action-dropdown';

    function recordHeading(table) {
        if (table.tHead && table.tHead.rows.length === 1) { return table.tHead.rows[0]; }
        var first = table.tBodies.length && table.tBodies[0].rows[0];
        return first && Array.from(first.cells).every(function (cell) { return cell.tagName === 'TH'; }) ? first : null;
    }

    function textWithoutExpander(cell) {
        return Array.from(cell.childNodes).filter(function (node) {
            return node.nodeType !== 1 || !node.classList.contains('sd-record-expander');
        }).map(function (node) { return node.textContent; }).join('').trim();
    }

    function enhanceRecordDetails(row, cells, headings, titleIndex, kind) {
        // Summary fields use native column headings. Custom/translated queues fall
        // back to two existing fields, so records never become blank collapsed cards.
        var summary = new Set([titleIndex]);
        var pattern = kind === 'ticket' ? /^(priority|customer|requester|from|client)$/i
            : /^(priority|department|agent|assigned to|assignee)$/i;
        headings.forEach(function (heading, index) {
            if (pattern.test(heading.textContent.trim())) { summary.add(index); }
        });
        if (kind === 'ticket' && summary.size < 2) {
            headings.forEach(function (heading, index) {
                if (/^(status|ticket(?:\s*(?:#|number))?)$/i.test(heading.textContent.trim())) { summary.add(index); }
            });
        }
        if (summary.size < 2) {
            cells.forEach(function (cell, index) {
                if (summary.size < 3 && index !== titleIndex && !cell.classList.contains('sd-record-select')) { summary.add(index); }
            });
        }
        var detailIds = [];
        if (!recordRowKeys.has(row)) { recordRowKeys.set(row, ++recordRowId); }
        var rowKey = recordRowKeys.get(row);
        cells.forEach(function (cell, index) {
            var detail = !summary.has(index) && !cell.classList.contains('sd-record-select');
            cell.classList.toggle('sd-record-details', detail);
            cell.classList.toggle('sd-record-summary', !detail);
            if (detail) {
                if (!cell.id || /^sd-record-detail-/.test(cell.id)) { cell.id = 'sd-record-detail-' + rowKey + '-' + index; }
                detailIds.push(cell.id);
            }
        });
        if (!detailIds.length || titleIndex < 0) { return; }
        row.classList.add('sd-record-compact');
        var button = row.querySelector('.sd-record-expander');
        if (!button) {
            button = document.createElement('button');
            button.type = 'button';
            button.className = 'button sd-record-expander';
            button.append(document.createTextNode('Details'), icon('M6 9l6 6 6-6'));
            cells[titleIndex].appendChild(button);
        }
        button.setAttribute('aria-controls', detailIds.join(' '));
        function updateLabel() {
            var titleCell = row.querySelector('.sd-record-title');
            var title = titleCell ? textWithoutExpander(titleCell) : '';
            var open = row.classList.contains('sd-record-expanded');
            button.setAttribute('aria-expanded', String(open));
            button.setAttribute('aria-label', (open ? 'Hide details: ' : 'Show details: ') + title);
        }
        updateLabel();
        if (!boundExpanders.has(button)) {
            boundExpanders.add(button);
            button.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                row.classList.toggle('sd-record-expanded');
                updateLabel();
            });
        }
    }

    /** Label native list/report cells without cloning or moving their controls. */
    function enhanceRecordList(table, wrapper) {
        var headerRow = recordHeading(table);
        if (!headerRow || Array.from(headerRow.cells).some(function (cell) { return cell.colSpan !== 1 || cell.rowSpan !== 1; })) {
            // About/system and plugin-install lists use section headings or no
            // column headings. Keep those rows readable without guessing labels.
            if (table.matches('.list')) {
                table.classList.add('sd-stack-table');
                wrapper.classList.add('sd-layout-scroll');
            }
            return;
        }
        var headings = Array.from(headerRow.cells);
        var head = table.tHead || headerRow;
        var kind = table.matches('.tickets, #ticketTable') || table.closest('form#tickets') ? 'ticket'
            : table.closest('form#tasks, form[name="tasks"]') || table.querySelector('a[href*="tasks.php?id="]') ? 'task' : 'record';
        table.classList.add('sd-record-list');
        headerRow.classList.add('sd-record-head');
        wrapper.classList.add('sd-records-scroll');
        wrapper.setAttribute('aria-label', 'Records');
        if (!head.id) { head.id = 'sd-record-head-' + (++recordTableId); }
        headings.forEach(function (heading, index) {
            if (!heading.id) { heading.id = head.id + '-column-' + index; }
            if (!heading.hasAttribute('scope')) { heading.scope = 'col'; }
            heading.classList.toggle('sd-record-empty-heading', !heading.textContent.trim() && !heading.querySelector('input, button, select'));
        });

        Array.from(table.tBodies).forEach(function (body) {
            Array.from(body.rows).forEach(function (row) {
                if (row === headerRow) { return; }
                var cells = Array.from(row.cells);
                if (cells.length !== headings.length || cells.some(function (cell) { return cell.colSpan !== 1 || cell.rowSpan !== 1; })) {
                    row.classList.add('sd-record-message');
                    return;
                }
                row.classList.add('sd-record');
                var titleIndex = headings.findIndex(function (heading) {
                    return /^(subject|title|name|plugin name(?: \(instances\))?|email address|schedule|department|team|role|help topic|list|template|api key)$/i.test(heading.textContent.trim());
                });
                if (titleIndex < 0) {
                    var longest = 0;
                    cells.forEach(function (cell, index) {
                        cell.querySelectorAll('a[href]').forEach(function (link) {
                            var text = link.textContent.trim();
                            if (!link.hasAttribute('data-preview') && !/^#?\d+$/.test(text) && text.length > longest && !link.closest('.sd-record-expander')) {
                                longest = text.length;
                                titleIndex = index;
                            }
                        });
                    });
                }
                if (titleIndex < 0) {
                    titleIndex = cells.findIndex(function (cell) { return cell.textContent.trim() && !cell.querySelector('input, select, button'); });
                }
                cells.forEach(function (cell, index) {
                    var label = headings[index].textContent.trim();
                    cell.setAttribute('data-sd-label', label);
                    if (!cell.hasAttribute('headers')) { cell.setAttribute('headers', headings[index].id); }
                    cell.classList.toggle('sd-record-title', index === titleIndex);
                    cell.classList.toggle('sd-record-key', /^(priority|status)$/i.test(label));
                    // Only an otherwise blank selection column is a bulk checkbox.
                    // Permission/feature checkboxes remain labelled data cells.
                    var checkbox = !label && cell.querySelector('input[type="checkbox"]');
                    cell.classList.toggle('sd-record-select', Boolean(checkbox));
                    if (checkbox && !checkbox.hasAttribute('aria-label') && !checkbox.labels.length) {
                        var title = titleIndex >= 0 ? textWithoutExpander(cells[titleIndex]) : '';
                        checkbox.setAttribute('aria-label', title ? 'Select: ' + title : 'Select record');
                    }
                });
                row.classList.toggle('sd-record-has-selection', Boolean(row.querySelector('.sd-record-select')));
                if (kind === 'ticket' || kind === 'task') { enhanceRecordDetails(row, cells, headings, titleIndex, kind); }
            });
        });

        if (!head.querySelector('a, button, input, select')) { return; }
        var button = wrapper.previousElementSibling;
        if (!button || !button.classList.contains('sd-list-sort-toggle')) {
            button = document.createElement('button');
            button.type = 'button';
            button.className = 'button sd-list-sort-toggle';
            button.textContent = 'Sort columns';
            button.setAttribute('aria-controls', head.id);
            button.setAttribute('aria-expanded', 'false');
            wrapper.parentNode.insertBefore(button, wrapper);
            button.addEventListener('click', function () {
                var open = table.classList.toggle('sd-list-sort-open');
                button.setAttribute('aria-expanded', String(open));
            });
        }
    }

    function enhanceLayoutTables() {
        document.querySelectorAll('#content table, .dialog table').forEach(function (table) {
            if (table.closest(excludedTables) || table.matches('.list, .queue, .dashboard-stats, #ticketTable, .sd-record-list') || table.closest('.sd-record-list')) { return; }
            var known = Boolean(table.closest('table.form_table, table.two-column, table.settings_table, table.padded, table.ticket_info, table.infoTable, table.custom-data, table.custom_data, table.custom-info, table#ticketInfo, table.sd-form-table'));
            var layout = !recordHeading(table) && (table.querySelector('input, select, textarea, table') || table.matches('#content > table[width]'));
            if (known || layout) { table.classList.add('sd-layout-table'); }
        });
    }

    function enhanceTables() {
        if (resizeObserver) {
            resizeObserver.disconnect();
        }
        document.querySelectorAll('table.list, table.queue, table.dashboard-stats, #ticketTable, #content table.table, .dialog table.table, #content table.form_table, .dialog table.form_table')
            .forEach(function (table) {
                // Do not change tables in messages, rich text, dropdowns or hidden editors.
                if (table.closest(excludedTables)) {
                    return;
                }
                if (!table.matches('.list, .queue, .dashboard-stats, #ticketTable')) {
                    var heading = recordHeading(table);
                    if (!heading || heading.cells.length < 2 || !heading.textContent.trim()
                        || Array.from(heading.cells).some(function (cell) { return cell.colSpan !== 1; })) { return; }
                }
                if (table.parentElement.classList.contains('sd-table-scroll')) {
                    enhanceRecordList(table, table.parentElement);
                    return;
                }
                var wrapper = document.createElement('div');
                wrapper.className = 'sd-table-scroll';
                if (!table.matches('.list, .queue, .dashboard-stats, #ticketTable')) {
                    wrapper.classList.add('sd-added-table-wrapper');
                }
                wrapper.setAttribute('role', 'region');
                wrapper.setAttribute('aria-label', 'Table — scroll horizontally for more columns');
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
                enhanceRecordList(table, wrapper);
            });
        if (resizeObserver) {
            document.querySelectorAll('.sd-table-scroll').forEach(function (wrapper) {
                resizeObserver.observe(wrapper);
            });
        }

        document.querySelectorAll('#ticketForm > table, #content form > table, table.form_table, .dialog form > table, .dialog table.grid.form')
            .forEach(function (table) {
                if (!table.matches('.list, .queue, .dashboard-stats, #ticketTable, .ticket_info, .sd-record-list')
                    && !table.closest('.thread-body, .redactor-in, .redactor_editor, .redactor-editor, .sd-table-scroll')
                    && table.querySelector('input:not([type="hidden"]), select, textarea')) {
                    table.classList.add('sd-form-table');
                    table.querySelectorAll(':scope > thead > tr').forEach(function (row) {
                        if (!row.textContent.trim() && !row.querySelector('input, select, button, textarea')) {
                            row.classList.add('sd-sizing-row');
                        }
                    });
                }
            });

        var loginColumns = document.querySelector('#clientLogin > div');
        if (loginColumns) {
            loginColumns.classList.add('sd-login-columns');
        }
        enhanceLayoutTables();
        updateScrollRegions();
    }

    function enhanceNavigation() {
        var header = document.getElementById('header');
        var nav = document.getElementById('nav');
        if (!header || !nav) {
            return;
        }
        var button = document.getElementById('sd-nav-toggle');
        if (!button) {
            button = document.createElement('button');
            button.type = 'button';
            button.id = 'sd-nav-toggle';
            button.className = 'sd-nav-toggle';
            button.setAttribute('aria-controls', 'nav');
            button.setAttribute('aria-expanded', 'false');
            menuLabel(button, false);
            header.appendChild(button);
            button.addEventListener('click', function () {
                setDrawer(!document.body.classList.contains('sd-nav-open'), false);
            });
        }
        document.body.classList.add('sd-nav-ready');
        nav.setAttribute('aria-label', 'Main navigation');
        nav.querySelectorAll('a.active, li.active > a').forEach(function (link) {
            link.setAttribute('aria-current', 'page');
        });
        nav.querySelectorAll(':scope > li').forEach(function (item, index) {
            var submenu = item.querySelector(':scope > ul');
            var link = item.querySelector(':scope > a');
            if (!submenu || !link || item.querySelector(':scope > .sd-subnav-toggle')) {
                return;
            }
            if (!submenu.id) {
                submenu.id = 'sd-submenu-' + index;
            }
            var toggle = document.createElement('button');
            toggle.type = 'button';
            toggle.className = 'sd-subnav-toggle';
            toggle.appendChild(icon('M6 9l6 6 6-6'));
            toggle.setAttribute('aria-label', 'Toggle ' + link.textContent.trim() + ' submenu');
            toggle.setAttribute('aria-controls', submenu.id);
            toggle.setAttribute('aria-expanded', 'false');
            item.insertBefore(toggle, submenu);
            toggle.addEventListener('click', function () {
                var open = item.classList.toggle('sd-subnav-open');
                toggle.setAttribute('aria-expanded', String(open));
            });
        });
        enhanceDrawer(nav);
    }

    function refresh() {
        scheduled = false;
        enhanceNavigation();
        enhanceQueues();
        enhanceTables();
        if (document.documentElement.classList.contains('sd-wordmark')) {
            document.querySelectorAll('#header #logo, #loginBox #logo a').forEach(function (logo) {
                logo.setAttribute('aria-label', (document.documentElement.getAttribute('data-sd-brand') || 'Helpdesk') + ' support home');
            });
        }
        var content = document.getElementById('content');
        if (content) {
            content.tabIndex = -1;
        }
        if (content && !document.getElementById('sd-skip-link')) {
            var skip = document.createElement('a');
            skip.id = 'sd-skip-link';
            skip.className = 'sd-skip-link';
            skip.href = '#content';
            skip.textContent = 'Skip to content';
            document.body.insertBefore(skip, document.body.firstChild);
        }
    }

    function scheduleRefresh() {
        if (!scheduled) {
            scheduled = true;
            window.requestAnimationFrame(refresh);
        }
    }

    function start() {
        refresh();
        // Only inserted/removed elements are observed. Style and value changes are ignored.
        var observer = new MutationObserver(function (mutations) {
            if (mutations.some(function (mutation) {
                return Array.prototype.some.call(mutation.addedNodes, function (node) {
                    return node.nodeType === 1;
                });
            })) {
                scheduleRefresh();
            }
        });
        observer.observe(document.body, {childList: true, subtree: true});
        window.addEventListener('resize', function () {
            if (!window.matchMedia('(max-width: 760px)').matches
                && document.body.classList.contains('sd-nav-open')) { setDrawer(false, false); }
            scheduleRefresh();
        }, {passive: true});
        if (window.jQuery) {
            window.jQuery(document).on('pjax:end.sdTheme', scheduleRefresh);
        }
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && document.body.classList.contains('sd-nav-open')) {
                setDrawer(false, true);
            }
            if (event.key === 'Tab' && document.body.classList.contains('sd-nav-open')) {
                var links = Array.from(document.querySelectorAll('#nav a, #nav button'))
                    .filter(function (element) { return element.getClientRects().length && !element.disabled; });
                var first = links[0];
                var last = links[links.length - 1];
                if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
                if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
            }
        });
    }

    window.SoupDigitalTheme = {refresh: scheduleRefresh};
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start, {once: true});
    } else {
        start();
    }
}());
