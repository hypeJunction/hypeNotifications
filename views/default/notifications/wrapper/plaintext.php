<?php

$body = elgg_extract('body', $vars);

$body = elgg_strip_tags($body);
$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
$body = wordwrap($body);

echo $body;