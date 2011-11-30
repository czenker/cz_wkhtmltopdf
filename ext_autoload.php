<?php
$extensionPath = t3lib_extMgm::extPath('cz_wkhtmltopdf');
$extensionClassesPath = $extensionPath . 'Classes/';
return array(
	'tx_czwkhtmltopdf_temporaryfile' => $extensionClassesPath . 'TemporaryFile.php',
	'tx_czwkhtmltopdf_converter' => $extensionClassesPath . 'Converter.php',
);
?>
