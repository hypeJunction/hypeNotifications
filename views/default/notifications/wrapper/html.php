<?php

use Pelago\Emogrifier;

$view = elgg_view('notifications/wrapper/html/template', $vars);

$css = elgg_view('notifications/wrapper/html/template.css');
$css .= elgg_view('elements/components.css');
$css .= elgg_view('elements/buttons.css');

$emogrifier = new Emogrifier($view, $css);
echo $emogrifier->emogrify();
