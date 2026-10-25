<?php
// SPDX-License-Identifier: GPL-2.0-or-later
// Copyright (C) 2026 Soup Digital and contributors.
if (!defined('INCLUDE_DIR')) {
    http_response_code(404);
    exit;
}

/** Retain osTicket's colour field and native widget, adding local reset metadata. */
class SoupDigitalThemeColorField extends ColorChoiceField
{
    static $widget = 'SoupDigitalThemeColorWidget';
}

class SoupDigitalThemeColorWidget extends ColorPickerWidget
{
    function render($options = array())
    {
        ob_start();
        parent::render($options);
        $input = ob_get_clean();
        $default = SoupDigitalThemePalette::normalize($this->field->get('default'));
        echo preg_replace('/<input\b/', '<input data-sd-default-color="' . $default . '"', $input, 1);

        if (!$this->field->get('sd_palette_controls')) {
            return;
        }
        ?>
        <style>
            /* Only this plugin's instance form; other osTicket Reset controls remain visible. */
            form:has(.sd-restore-colours) input[type="reset"],
            form:has(.sd-restore-colours) button[type="reset"] {
                display: none !important;
            }
        </style>
        <p>
            <button type="button" class="button sd-restore-colours">Restore default colours</button>
            <span role="status" class="sd-palette-status" aria-live="polite"></span>
        </p>
        <p>Restore the original Soup palette, then click Save Changes.</p>
        <script><?php
            // Settings controls must work even when panel styling/enhancements are disabled.
            echo file_get_contents(__DIR__ . '/assets/js/settings.js');
        ?></script>
        <?php
    }
}
