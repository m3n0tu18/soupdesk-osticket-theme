<?php
// SPDX-License-Identifier: GPL-2.0-or-later
// Copyright (C) 2026 Soup Digital and contributors.
if (!defined('INCLUDE_DIR')) {
    http_response_code(404);
    exit;
}
require_once INCLUDE_DIR . 'class.plugin.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/renderer.php';

class SoupDigitalThemePlugin extends Plugin
{
    var $config_class = 'SoupDigitalThemeConfig';
    private static $started = false;
    private $appearanceConfig;
    private $area;
    private $streamed = false;

    function isMultiInstance()
    {
        return false;
    }

    function bootstrap()
    {
        // A zero-byte DISABLED file provides an emergency filesystem off switch.
        if (self::$started || PHP_SAPI === 'cli'
            || is_file(__DIR__ . '/DISABLED')
            || (defined('SOUP_DIGITAL_THEME_DISABLED') && SOUP_DIGITAL_THEME_DISABLED)
            || !empty($_SERVER['HTTP_X_PJAX'])) {
            return;
        }
        $area = SoupDigitalThemeRenderer::requestArea(
            isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '', ROOT_PATH
        );
        $config = $this->getConfig();
        if (!$area || !$config) {
            return;
        }
        $this->area = $area;
        $this->appearanceConfig = $config;
        self::$started = true;
        // In 1.18.4 bootstrap runs inside osTicket::start(), before global $ost exists.
        // Buffer complete HTML instead of relying on an unimplemented page-head signal.
        ob_start(array($this, 'filterOutput'));
    }

    public function filterOutput($buffer, $phase)
    {
        // Never retain output explicitly cleared by the host. Early streaming is passed
        // through unchanged rather than attempting to transform incomplete documents.
        if ($phase & PHP_OUTPUT_HANDLER_CLEAN) {
            return $buffer;
        }
        if (!($phase & PHP_OUTPUT_HANDLER_FINAL)) {
            $this->streamed = true;
            return $buffer;
        }
        if ($this->streamed || (http_response_code() >= 300 && http_response_code() < 400)) {
            return $buffer;
        }
        $headers = headers_list();
        foreach ($headers as $header) {
            // Content-Length has already been calculated: leave those responses alone.
            if (stripos($header, 'Content-Length:') === 0) {
                return $buffer;
            }
        }
        // Read appearance after the controller has processed a settings save.
        // CSS is inline, so the saved response needs no cache-busting reload.
        $values = $this->appearanceConfig->getAppearance();
        if (empty($values[$this->area . '_enabled'])) {
            return $buffer;
        }
        $palette = array();
        foreach (SoupDigitalThemePalette::definitions() as $key => $definition) {
            $palette[$key] = isset($values['color_' . $key]) ? $values['color_' . $key] : null;
        }
        $renderer = new SoupDigitalThemeRenderer(
            $this->area, !empty($values['wordmark_enabled']),
            !empty($values['enhancements_enabled']), __DIR__, $palette,
            isset($values['brand_name']) ? $values['brand_name'] : 'Helpdesk'
        );
        return $renderer->transform($buffer, $headers);
    }
}
