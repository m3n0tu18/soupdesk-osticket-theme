<?php
// SPDX-License-Identifier: GPL-2.0-or-later
// Copyright (C) 2026 Soup Digital and contributors.
/** Model one request with separate bootstrap/controller config objects; no database. */
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
class PluginConfig
{
    public $id;
    public $values;
    public $writeSucceeds = true;
    function __construct($id, $values) { $this->id = $id; $this->values = $values; }
    function getId() { return $this->id; }
    function get($key) { return isset($this->values[$key]) ? $this->values[$key] : null; }
    function updateAll($updates) { return $this->writeSucceeds; }
}
class Plugin {}
class BooleanField
{
    public $options;
    function __construct($options) { $this->options = $options; }
    function to_php($value) { return (bool) $value; }
}
class TextboxField extends BooleanField { function to_php($value) { return $value; } }
class ColorChoiceField extends TextboxField {}
class ColorPickerWidget {}
$stub = sys_get_temp_dir() . '/soup-save-test-' . bin2hex(random_bytes(8));
mkdir($stub); file_put_contents($stub . '/class.plugin.php', '<?php');
define('INCLUDE_DIR', $stub . '/');
require dirname(__DIR__) . '/theme.php';
$checks = 0;
function verifySave($ok, $description) {
    global $checks;
    if (!$ok) { fwrite(STDERR, 'FAIL: ' . $description . "\n"); exit(1); }
    $checks++;
}
function responseFor($config) {
    $plugin = new SoupDigitalThemePlugin();
    foreach (array('appearanceConfig' => $config, 'area' => 'staff') as $name => $value) {
        $property = new ReflectionProperty($plugin, $name);
        $property->setAccessible(true);
        $property->setValue($plugin, $value);
    }
    return $plugin->filterOutput('<!doctype html><html><head></head><body>Sample</body></html>', PHP_OUTPUT_HANDLER_FINAL);
}
try {
    $old = array('staff_enabled'=>true, 'customer_enabled'=>true,
        'wordmark_enabled'=>false, 'enhancements_enabled'=>false,
        'brand_name'=>'Old desk', 'color_primary'=>'#FFC107');
    $bootstrap = new SoupDigitalThemeConfig('p1i1', $old);
    $controller = new SoupDigitalThemeConfig('p1i1', $old);
    verifySave(strpos(responseFor($bootstrap), '--sd-primary:#FFC107;') !== false, 'initial palette');
    verifySave($controller->updateAll(array('color_primary'=>'#00ff00', 'brand_name'=>'New desk')), 'successful save');
    $html = responseFor($bootstrap);
    verifySave(strpos($html, '--sd-primary:#00FF00;') !== false, 'same save response uses new colour');
    verifySave(strpos($html, 'data-sd-brand="New desk"') !== false, 'same save response uses new branding');
    $other = new SoupDigitalThemeConfig('p1i2', $old);
    verifySave(strpos(responseFor($other), '--sd-primary:#FFC107;') !== false, 'another instance remains isolated');
    $controller->writeSucceeds = false;
    verifySave(!$controller->updateAll(array('color_primary'=>'#2463eb')), 'failed write reported');
    verifySave(strpos(responseFor($bootstrap), '--sd-primary:#00FF00;') !== false, 'failed write does not alter palette');
    $controller->writeSucceeds = true;
    verifySave($controller->updateAll(array('staff_enabled'=>'0', 'color_primary'=>'#00ff00')), 'disable saved');
    verifySave(strpos(responseFor($bootstrap), 'id="sd-theme-styles"') === false, 'panel disable takes effect on save response');
    verifySave($controller->updateAll(array('staff_enabled'=>'1')), 'enable saved');
    verifySave(strpos(responseFor($bootstrap), 'id="sd-theme-styles"') !== false, 'panel enable takes effect on save response');
    verifySave(strpos(responseFor($bootstrap), '--sd-primary:#00FF00;') !== false, 'successive partial writes retain saved colours');
} finally { unlink($stub . '/class.plugin.php'); rmdir($stub); }
echo 'PASS: ' . $checks . " save lifecycle checks\n";
