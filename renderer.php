<?php
// SPDX-License-Identifier: GPL-2.0-or-later
// Copyright (C) 2026 Soup Digital and contributors.
/**
 * Presentation-only HTML transformer; independent of the osTicket database.
 * No routing, form values, ticket content or authentication logic is changed.
 */
require_once __DIR__ . '/palette.php';

class SoupDigitalThemeRenderer
{
    private $area;
    private $wordmark;
    private $enhancements;
    private $directory;
    private $palette;
    private $brand;

    public function __construct($area, $wordmark, $enhancements, $directory, $palette = array(), $brand = 'Helpdesk')
    {
        $this->area = $area === 'staff' ? 'staff' : 'customer';
        $this->wordmark = (bool) $wordmark;
        $this->enhancements = (bool) $enhancements;
        $this->directory = $directory;
        $this->palette = is_array($palette) ? $palette : array();
        $this->brand = SoupDigitalThemePalette::brand($brand);
    }

    /** Derive the panel from the executing script, including subdirectory installs. */
    public static function requestArea($scriptName, $rootPath)
    {
        $root = '/' . trim($rootPath, '/');
        $root = $root === '/' ? '/' : $root . '/';
        if (strpos($scriptName, $root) !== 0) {
            return null;
        }
        $relative = substr($scriptName, strlen($root));
        // Only ordinary page controllers qualify. AJAX, API and file responses do not.
        if (!preg_match('~^(scp/|kb/|pages/)?[a-zA-Z0-9_-]+\.php$~D', $relative)) {
            return null;
        }
        if (in_array(basename($relative), array(
            'ajax.php', 'api.php', 'cron.php', 'autocron.php', 'file.php',
            'image.php', 'logo.php', 'captcha.php',
        ), true)) {
            return null;
        }
        return strpos($relative, 'scp/') === 0 ? 'staff' : 'customer';
    }

    public function transform($html, $headers = array())
    {
        foreach ($headers as $header) {
            if (stripos($header, 'Content-Disposition:') === 0
                || (stripos($header, 'Content-Type:') === 0
                    && stripos($header, 'text/html') === false)
                || stripos($header, 'Content-Encoding:') === 0) {
                return $html;
            }
        }
        // Full document checks prevent JSON, fragments, downloads and redirects changing.
        if (!preg_match('~^\s*(?:<!doctype[^>]*>\s*)?<html\b~i', $html)
            || stripos($html, '</head>') === false
            || stripos($html, '</body>') === false
            || strpos($html, 'id="sd-theme-styles"') !== false) {
            return $html;
        }
        $cssFile = $this->directory . '/assets/css/' . $this->area . '.css';
        if (!is_readable($cssFile) || ($css = file_get_contents($cssFile)) === false) {
            return $html;
        }

        $classes = 'sd-theme sd-' . $this->area;
        if ($this->wordmark) {
            $classes .= ' sd-wordmark';
        }
        $html = preg_replace_callback('~<html\b([^>]*)>~i', function ($match) use ($classes) {
            $attrs = $match[1];
            if (preg_match('~\bclass\s*=\s*([\x22\x27])(.*?)\1~is', $attrs)) {
                $attrs = preg_replace_callback('~\bclass\s*=\s*([\x22\x27])(.*?)\1~is',
                    function ($class) use ($classes) {
                        return 'class=' . $class[1] . $class[2] . ' ' . $classes . $class[1];
                    }, $attrs, 1);
            } else {
                $attrs .= ' class="' . $classes . '"';
            }
            return '<html' . $attrs . '>';
        }, $html, 1);

        $brandAttribute = htmlspecialchars($this->brand, ENT_QUOTES, 'UTF-8');
        $html = preg_replace_callback('~<html\b~i', function ($match) use ($brandAttribute) {
            return $match[0] . ' data-sd-brand="' . $brandAttribute . '"';
        }, $html, 1);
        $css .= "\n" . SoupDigitalThemePalette::css($this->palette, $this->brand);
        $extra = '';
        if (!preg_match('~<meta\b[^>]*\bname\s*=\s*[\x22\x27]?viewport\b~i', $html)) {
            $extra .= '<meta name="viewport" content="width=device-width, initial-scale=1">' . "\n";
        }
        // Inline local assets work even when the webserver denies /include/ URLs.
        $extra .= '<style id="sd-theme-styles" data-version="1.1.0-beta.9" media="screen">' . "\n"
            . str_ireplace('</style', '< /style', $css) . "\n</style>\n";
        $headEnd = stripos($html, '</head>');
        $html = substr_replace($html, $extra, $headEnd, 0);

        $jsFile = $this->directory . '/assets/js/theme.js';
        if ($this->enhancements && is_readable($jsFile)
            && ($js = file_get_contents($jsFile)) !== false) {
            $script = '<script id="sd-theme-script">' . "\n"
                . str_ireplace('</script', '< /script', $js) . "\n</script>\n";
            $html = substr_replace($html, $script, strripos($html, '</body>'), 0);
        }
        return $html;
    }
}
