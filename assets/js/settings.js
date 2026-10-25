/* Copyright (C) 2026 Soup Digital and contributors. SPDX-License-Identifier: GPL-2.0-or-later */
(function () {
    'use strict';

    function updatePicker(input, value, notify) {
        input.value = value;
        // Support osTicket installations that enhance native colour fields with Spectrum.
        var jq = window.jQuery;
        if (jq && jq.fn.spectrum && jq(input).data('spectrum.id') !== undefined) {
            jq(input).spectrum('set', value);
        }
        if (notify) {
            input.dispatchEvent(new Event('input', {bubbles: true}));
            input.dispatchEvent(new Event('change', {bubbles: true}));
        }
    }

    function bindSettings() {
        document.querySelectorAll('.sd-restore-colours').forEach(function (button) {
            if (button.dataset.sdBound) { return; }
            var form = button.closest('form');
            if (!form) { return; }
            button.dataset.sdBound = 'true';
            var status = button.parentElement.querySelector('.sd-palette-status');

            button.addEventListener('click', function () {
                form.querySelectorAll('input[data-sd-default-color]').forEach(function (input) {
                    updatePicker(input, input.dataset.sdDefaultColor, true);
                });
                status.textContent = ' Default colours selected. Click Save Changes to apply.';
            });

            form.addEventListener('reset', function (event) {
                // Native reset runs after this event. Keep any enhanced swatches in sync.
                window.setTimeout(function () {
                    if (event.defaultPrevented) { return; }
                    form.querySelectorAll('input[data-sd-default-color]').forEach(function (input) {
                        updatePicker(input, input.defaultValue, false);
                    });
                    status.textContent = '';
                }, 0);
            });
        });
    }

    // Also works when osTicket inserts the form through PJAX after DOMContentLoaded.
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindSettings, {once: true});
    } else {
        bindSettings();
    }
}());
