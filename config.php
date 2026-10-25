<?php
// SPDX-License-Identifier: GPL-2.0-or-later
// Copyright (C) 2026 Soup Digital and contributors.
if (!defined('INCLUDE_DIR')) {
    http_response_code(404);
    exit;
}
require_once INCLUDE_DIR . 'class.plugin.php';
require_once __DIR__ . '/palette.php';
require_once __DIR__ . '/settings.php';

class SoupDigitalThemeConfig extends PluginConfig
{
    private static $savedAppearance = array();

    /** Only expose new values after osTicket has successfully persisted them. */
    function updateAll($updates)
    {
        if (!parent::updateAll($updates)) {
            return false;
        }
        $values = array();
        foreach ($this->getOptions() as $key => $field) {
            if (array_key_exists($key, $updates)) {
                $values[$key] = $field->to_php($updates[$key]);
            }
        }
        // The admin controller and bootstrap can hold different config objects.
        // Share the saved values only within this request and this instance.
        $id = $this->getId();
        self::$savedAppearance[$id] = array_replace(
            isset(self::$savedAppearance[$id]) ? self::$savedAppearance[$id] : array(), $values
        );
        return true;
    }

    public function getAppearance()
    {
        $values = array();
        foreach ($this->getOptions() as $key => $field) {
            $values[$key] = $this->get($key);
        }
        $id = $this->getId();
        return isset(self::$savedAppearance[$id])
            ? array_replace($values, self::$savedAppearance[$id]) : $values;
    }

    function getOptions()
    {
        $options = array(
            'customer_enabled' => new BooleanField(array(
                'id' => 'soupdesk_customer_enabled',
                'label' => 'Enable customer theme',
                'default' => true,
                'configuration' => array('desc' => 'Apply theme styling to the customer portal.'),
            )),
            'staff_enabled' => new BooleanField(array(
                'id' => 'soupdesk_staff_enabled',
                'label' => 'Enable agent and admin theme',
                'default' => true,
                'configuration' => array('desc' => 'Apply theme styling to the staff panels and login.'),
            )),
            'wordmark_enabled' => new BooleanField(array(
                'id' => 'soupdesk_wordmark_enabled',
                'label' => 'Show text wordmark',
                'default' => false,
                'configuration' => array('desc' => 'Use a text wordmark. Untick to retain your configured osTicket logo.'),
            )),
            'enhancements_enabled' => new BooleanField(array(
                'id' => 'soupdesk_enhancements_enabled',
                'label' => 'Enable responsive enhancements',
                'default' => true,
                'configuration' => array('desc' => 'Add mobile navigation, scrollable ticket tables and stacked form layouts.'),
            )),
        );
        $options['brand_name'] = new TextboxField(array(
            'id' => 'soupdesk_brand_name',
            'label' => 'Text wordmark', 'default' => 'Helpdesk',
            'configuration' => array('size' => 30, 'length' => 80),
            'hint' => 'Only shown when the text wordmark is enabled. Otherwise your osTicket logo is retained.',
        ));
        foreach (SoupDigitalThemePalette::definitions() as $key => $definition) {
            $options['color_' . $key] = new SoupDigitalThemeColorField(array(
                'id' => 'soupdesk_color_' . $key,
                'label' => $definition[0], 'default' => $definition[1],
                'sd_palette_controls' => $key === 'navigation_hover',
                'validators' => function ($field, $value) {
                    if (SoupDigitalThemePalette::normalize($value) === null)
                        $field->addError('Choose a colour or enter a hex value such as #FFC107.');
                },
            ));
        }
        return $options;
    }
}

