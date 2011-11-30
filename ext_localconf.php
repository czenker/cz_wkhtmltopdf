<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
// hook is called after Caching / pages with COA_/USER_INT objects.
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = 'EXT:'.$_EXTKEY.'/Classes/Controller.php:&tx_CzWkhtmltopdf_controller->hookNoCache';

// hook is called before Caching / pages on their way in the cache.
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][] = 'EXT:'.$_EXTKEY.'/Classes/Controller.php:&tx_CzWkhtmltopdf_Controller->hookCache';
?>