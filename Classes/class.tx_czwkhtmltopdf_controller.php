<?php

/**
 * @license WTFPL v2
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class tx_czwkhtmltopdf_controller {

	/**
	 * convert non-cached pages via a TYPO3 hook
	 *
	 * @return void
	 */
	public function hookNoCache(&$params, &$obj) {
		$this->processHook($params, $obj);
	}

	/**
	 * convert cached pages via a TYPO3 hook
	 *
	 * @return void
	 */
	public function hookCache(&$params, &$obj) {
//		$this->processHook($params, $obj);
	}

	/**
	 * process a hook
	 *
	 * @param $params
	 * @param tslib_fe $pObj
	 * @return void
	 */
	public function processHook(&$params, &$pObj) {

		$converter = t3lib_div::makeInstance('tx_CzWkhtmltopdf_Converter');
		$pdfFile = t3lib_div::makeInstance('tx_CzWkhtmltopdf_TemporaryFile');
		$converter->convert($pObj->content, $pdfFile);

		$pObj->content = $pdfFile->getContent();
		header('Content-Type: application/pdf');

	}
}

 
