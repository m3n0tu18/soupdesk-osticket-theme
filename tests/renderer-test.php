<?php
// SPDX-License-Identifier: GPL-2.0-or-later
// Copyright (C) 2026 Soup Digital and contributors.
/** Run with `php tests/renderer-test.php`; no osTicket database is required. */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require_once dirname(__DIR__) . '/renderer.php';

$checks = 0;
function check($condition, $description)
{
    global $checks;
    if (!$condition) {
        fwrite(STDERR, "FAIL: " . $description . "\n");
        exit(1);
    }
    $checks++;
}

$directory = dirname(__DIR__);
$renderer = new SoupDigitalThemeRenderer('staff', true, true, $directory);
$original = '<!doctype html><html lang="en" class="rtl"><head><title>Example</title></head>'
    . '<body><form method="post"><input type="hidden" name="__CSRFToken__" value="unchanged">'
    . '<textarea name="message">Keep &lt;code&gt; intact.</textarea></form>'
    . '<script>window.original = "preserved";</script></body></html>';
$styled = $renderer->transform($original, array('Content-Type: text/html; charset=UTF-8'));
check(strpos($styled, 'class="rtl sd-theme sd-staff sd-wordmark"') !== false, 'existing HTML attributes preserved');
check(substr_count($styled, 'id="sd-theme-styles"') === 1, 'exactly one theme stylesheet');
check(substr_count($styled, 'id="sd-theme-script"') === 1, 'exactly one enhancement script');
check(strpos($styled, 'name="viewport"') !== false, 'viewport added for staff pages');
check(strpos($styled, 'value="unchanged"') !== false, 'CSRF field untouched');
check(strpos($styled, 'Keep &lt;code&gt; intact.') !== false, 'message content untouched');
check(strpos($styled, 'window.original = "preserved";') !== false, 'existing scripts untouched');
check($renderer->transform($styled) === $styled, 'repeat transformation is idempotent');

$withViewport = str_replace('</head>', '<meta name="viewport" content="width=device-width"></head>', $original);
check(substr_count($renderer->transform($withViewport), 'name="viewport"') === 1, 'existing viewport not duplicated');
foreach (array('application/json', 'text/csv', 'application/pdf', 'image/png', 'application/zip') as $mime) {
    check($renderer->transform($original, array('Content-Type: ' . $mime)) === $original, 'non-HTML response untouched: ' . $mime);
}
check($renderer->transform($original, array('Content-Disposition: attachment; filename=export.html')) === $original, 'downloads untouched');
check($renderer->transform($original, array('Content-Encoding: gzip')) === $original, 'already encoded responses untouched');
check($renderer->transform('<div>Fragment</div>') === '<div>Fragment</div>', 'AJAX fragment untouched');
check($renderer->transform('{"data":"<html></html>"}') === '{"data":"<html></html>"}', 'JSON text untouched');

$customer = new SoupDigitalThemeRenderer('customer', false, false, $directory);
$customerHtml = $customer->transform($original);
check(strpos($customerHtml, 'class="rtl sd-theme sd-customer"') !== false, 'customer area and wordmark switch');
check(strpos($customerHtml, 'id="sd-theme-script"') === false, 'enhancement switch respected');
$missing = new SoupDigitalThemeRenderer('staff', true, true, '/missing/soup-theme');
check($missing->transform($original) === $original, 'missing CSS fails open to original HTML');

$routes = array(
    array('/index.php', '/', 'customer'),
    array('/kb/faq.php', '/', 'customer'),
    array('/pages/index.php', '/', 'customer'),
    array('/scp/login.php', '/', 'staff'),
    array('/scp/admin.php', '/', 'staff'),
    array('/helpdesk/scp/tickets.php', '/helpdesk/', 'staff'),
    array('/helpdesk/open.php', '/helpdesk/', 'customer'),
    array('/scp/admin.php', '/helpdesk/', null),
    array('/api/http.php', '/', null),
    array('/setup/index.php', '/', null),
    array('/scp/ajax.php', '/', null),
    array('/file.php', '/', null),
    array('/logo.php', '/', null),
    array('/scp/autocron.php', '/', null),
);
foreach ($routes as $route) {
    check(SoupDigitalThemeRenderer::requestArea($route[0], $route[1]) === $route[2], 'route classification: ' . $route[0]);
}
echo 'PASS: ' . $checks . " renderer checks\n";
