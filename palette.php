<?php
// SPDX-License-Identifier: GPL-2.0-or-later
// Copyright (C) 2026 Soup Digital and contributors.
/** Validated instance palette. Only allowlisted, opaque hex values reach CSS. */
class SoupDigitalThemePalette
{
    public static function definitions()
    {
        return array(
            'text' => array('Text / charcoal', '#2D2E2D'),
            'surface' => array('Panel background', '#FAF9F6'),
            'muted' => array('Muted text', '#808080'),
            'background' => array('Page background', '#F2F2F2'),
            'border' => array('Borders', '#E5E5E5'),
            'secondary' => array('Secondary text', '#707070'),
            'subtle' => array('Subtle panel background', '#F9F9F9'),
            'primary' => array('Primary buttons / accent', '#FFC107'),
            'primary_hover' => array('Primary hover / selection', '#FFE69B'),
            'primary_text' => array('Primary button text', '#2D2E2D'),
            'navigation' => array('Navigation background', '#2D2E2D'),
            'navigation_text' => array('Navigation text', '#FAF9F6'),
            'navigation_hover' => array('Navigation hover', '#454645'),
        );
    }

    public static function normalize($value)
    {
        if (!is_string($value)) return null;
        $value = trim($value);
        if (!preg_match('/^#(?:[a-f0-9]{3}|[a-f0-9]{6})$/iD', $value)) return null;
        if (strlen($value) === 4) {
            $value = '#' . $value[1] . $value[1] . $value[2] . $value[2] . $value[3] . $value[3];
        }
        return strtoupper($value);
    }

    public static function brand($value)
    {
        if (!is_string($value) || !preg_match('//u', $value)) return 'Helpdesk';
        $value = trim(preg_replace('/[\x00-\x1f\x7f]/', '', $value));
        return $value === '' ? 'Helpdesk' : implode('', array_slice(preg_split('//u', $value, -1, PREG_SPLIT_NO_EMPTY), 0, 80));
    }

    public static function css($values, $brand)
    {
        $css = 'html.sd-theme {';
        foreach (self::definitions() as $key => $definition) {
            $value = isset($values[$key]) ? self::normalize($values[$key]) : null;
            $css .= '--sd-' . str_replace('_', '-', $key) . ':' . ($value ?: $definition[1]) . ';';
        }
        // Escape CSS string syntax and HTML style terminators independently.
        $brand = str_replace(array('\\', '"', '<', '>'), array('\\\\', '\\"', '\\3c ', '\\3e '), self::brand($brand));
        return $css . '--sd-brand-name:"' . $brand . '";}';
    }
}
