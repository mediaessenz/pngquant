<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "pngquant"
 *
 * Manual updates:
 * Only the data in the array - anything else is removed by next write.
 * "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'pngquant',
	'description' => 'Integration of pngquant library (lossy compression of PNG images - https://pngquant.org/).',
	'category' => 'backend',
	'version' => '1.0.3',
	'author' => 'Fabrice MORIN',
	'author_email' => 'fmo@sword.eu',
	'author_company' => 'Sword-Group',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => '0',
	'clearCacheOnLoad' => 1,
	'constraints' => array(
		'depends' => array(
			'typo3' => '6.2.0-7.99.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);
