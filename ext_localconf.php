<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
// hook is called after Caching / pages with COA_/USER_INT objects.
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = 'EXT:'.$_EXTKEY.'/Classes/class.tx_czwkhtmltopdf_controller.php:&tx_czwkhtmltopdf_controller->hookNoCache';

// hook is called before Caching / pages on their way in the cache.
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][] = 'EXT:'.$_EXTKEY.'/Classes/class.tx_czwkhtmltopdf_controller.php:&tx_czwkhtmltopdf_controller->hookCache';
?>