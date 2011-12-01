<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}


// hook is called before outputting no matter if content comes from cache or was just generated
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = 'EXT:'.$_EXTKEY.'/Classes/Controller.php:&tx_CzWkhtmltopdf_Controller->hookOutput';

// called before page is written to cache (if it is supposed to)
/* NOTE: we don't use contentPostProc-cached here as there is some character conversion done
 * afterwards and this might break the encoding (although UTF8 should not be problematic)
 */
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['pageIndexing'][] = 'tx_CzWkhtmltopdf_Controller';


?>