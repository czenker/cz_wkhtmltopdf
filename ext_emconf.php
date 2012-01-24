<?php

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Generate PDFs using wkhtmltopdf',
	'description' => 'A wrapper to let TYPO3 generate PDF files from html pages. Uses wkhtmltopdf,
	a binary that is using the print functionality of the webkit render engine to create PDFs.',
	'category' => 'fe',
	'author' => 'Christian Zenker',
	'author_email' => 'http://www.599media.de',
	'author_company' => '599media, Freiberg',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => '',
	// if other extensions use the contentPostProc hooks, we should typically generate the pdf after that
	'priority' => 'bottom',
	'module' => '',
	'state' => 'experimental',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '0.0.0-dev',
	'constraints' => array(
		'depends' => array(
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => null,
);

?>