<?php

$cfg = require __DIR__ . '/../vendor/mediawiki/mediawiki-phan-config/src/config-library.php';

$cfg['plugins'] = array_merge( $cfg['plugins'], [
	'PHPDocRedundantPlugin',
] );

return $cfg;
