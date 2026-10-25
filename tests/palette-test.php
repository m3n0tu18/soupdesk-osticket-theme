<?php
// SPDX-License-Identifier: GPL-2.0-or-later
// Copyright (C) 2026 Soup Digital and contributors.
/** Pure PHP settings, validation and rendering tests. */
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/renderer.php';
$checks = 0;
function verify($ok, $description) {
    global $checks;
    if (!$ok) { fwrite(STDERR, 'FAIL: ' . $description . "\n"); exit(1); }
    $checks++;
}
verify(SoupDigitalThemePalette::normalize('#abc') === '#AABBCC', 'short hex expanded');
verify(SoupDigitalThemePalette::normalize(' #fF0088 ') === '#FF0088', 'hex canonicalized');
foreach (array('', 'red', '#12345', '#12345678', '#000;display:none', '</style><script>alert(1)</script>', array(), null) as $input) {
    verify(SoupDigitalThemePalette::normalize($input) === null, 'invalid colour rejected');
}
$palette = array('primary' => '#2463eb', 'primary_text' => '#fff', 'navigation' => '#14213d', 'background' => '#edf2f7', 'surface' => '#ffffff', 'border' => 'bad', 'unknown' => '#123456');
$brand = 'Desk "A" \\ $1 </style><script>alert(1)</script> Café';
$css = SoupDigitalThemePalette::css($palette, $brand);
verify(strpos($css, '--sd-primary:#2463EB;') !== false, 'custom accent emitted');
verify(strpos($css, '--sd-border:#E5E5E5;') !== false, 'invalid stored colour falls back');
verify(strpos($css, '--sd-unknown') === false, 'unknown settings ignored');
verify(stripos($css, '</style') === false, 'brand cannot terminate style');
verify(strpos($css, '\\"A\\"') !== false, 'brand quotes escaped');
verify(SoupDigitalThemePalette::brand('') === 'Helpdesk', 'empty brand fallback');
verify(strlen(SoupDigitalThemePalette::brand(str_repeat('x', 90))) === 80, 'brand length bounded');
$html = '<!doctype html><html><head><title>Sample</title></head><body><input name="draft" value="keep"></body></html>';
foreach (array('customer', 'staff') as $area) {
    $renderer = new SoupDigitalThemeRenderer($area, true, true, dirname(__DIR__), $palette, $brand);
    $out = $renderer->transform($html);
    verify(substr_count($out, '</style>') === 1, $area . ' style boundary intact');
    verify(strpos($out, '--sd-primary:#2463EB;') !== false, $area . ' instance palette applied');
    verify(strpos($out, 'data-sd-brand="Desk &quot;A&quot; \\ $1 &lt;/style&gt;') !== false, $area . ' brand attribute escaped without replacement interpretation');
    verify(strpos($out, 'value="keep"') !== false, $area . ' draft untouched');
}
// The real osTicket settings form supplies these field types. Stub only persistence
// and fields here to verify our option/validator wiring without a database.
if (!class_exists('PluginConfig')) {
    class PluginConfig {}
    class ColorPickerWidget {}
    class BooleanField { public $options; function __construct($options) { $this->options = $options; } }
    class TextboxField extends BooleanField {}
    class ColorChoiceField extends BooleanField { public $errors = array(); function addError($message) { $this->errors[] = $message; } }
}
$stub = sys_get_temp_dir() . '/soup-theme-test-' . bin2hex(random_bytes(8));
mkdir($stub); file_put_contents($stub . '/class.plugin.php', '<?php');
define('INCLUDE_DIR', $stub . '/');
try {
    require dirname(__DIR__) . '/config.php';
    $config = new SoupDigitalThemeConfig();
    $options = $config->getOptions();
    $again = $config->getOptions();
    $ids = array();
    foreach ($options as $name => $field) {
        verify(isset($field->options['id']) && $field->options['id'] === $again[$name]->options['id'], 'stable setting ID across repeated builds: ' . $name);
        $ids[] = $field->options['id'];
    }
    verify(count($ids) === count(array_unique($ids)), 'setting IDs are unique');
    verify($options['wordmark_enabled']->options['default'] === false, 'new installs retain configured logo');
    foreach (SoupDigitalThemePalette::definitions() as $key => $definition) {
        $field = $options['color_' . $key];
        verify($field instanceof ColorChoiceField, 'native picker: ' . $key);
        $validate = $field->options['validators'];
        $validate($field, $definition[1]);
        verify(count($field->errors) === 0, 'valid picker input: ' . $key);
        $validate($field, '#000;bad');
        verify(count($field->errors) === 1, 'invalid picker input: ' . $key);
    }
} finally { unlink($stub . '/class.plugin.php'); rmdir($stub); }
echo 'PASS: ' . $checks . " palette/settings checks\n";
