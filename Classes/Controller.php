<?php

/**
 * @license WTFPL v2
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class tx_CzWkhtmltopdf_Controller {

	/**
	 * @return void
	 */
	public function hookAll(&$params, &$obj) {
//		$this->processHook($params, $obj);
	}

	/**
	 * @return void
	 */
	public function hookOutput(&$params, &$obj) {
		$this->processHook($params, $obj);
	}

	/**
	 * @return void
	 */
	public function hookCached(&$params, &$obj) {
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

 
