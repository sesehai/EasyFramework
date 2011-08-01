<?php
$log_config = array(
	'loggers' => array(
		'base' => ROOT_PATH.'/var/',
		'system' => 'system',
		'app' => 'app'
	),
	'levels' => array('DEBUG', 'INFO', 'ERROR', 'WARN', 'FATAL'),
	'handlers' => array(
		'file' => array(
			'driver' => 'file',
			'level' => array('INFO'),
			'formatter' => 'generic',
			'enabled' => true,
			'config' => array(
				'dir' => ROOT_PATH.'/var/log',
			),
		),
	),
	'formatters' => array(
		'generic' => '{time} {level} [{uri}] :"""{message}"""',
	),
);
