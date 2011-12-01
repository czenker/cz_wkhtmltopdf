<?php

/**
 * @license WTFPL v2
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class tx_CzWkhtmltopdf_Controller {

	/**
	 * called before any page content is send to the browser
	 *
	 * @param array $params
	 * @param tslib_fe $pObj
	 * @return void
	 */
	public function hookOutput(&$params, $pObj) {
		if($pObj->no_cache) {
			$this->processHook($pObj);
		}
	}

	/**
	 * called before anything is written to the cache
	 *
	 * @param tslib_fe $pObj
	 * @return void
	 */
	public function hook_indexContent($pObj) {
		if(!$pObj->no_cache) {
			$this->processHook($pObj);
		}
	}

	/**
	 * process a hook
	 *
	 * @param tslib_fe $pObj
	 * @return void
	 */
	public function processHook($pObj) {

		$converter = t3lib_div::makeInstance('tx_CzWkhtmltopdf_Converter');
		$pdfFile = t3lib_div::makeInstance('tx_CzWkhtmltopdf_TemporaryFile');
		$converter->convert($pObj->content, $pdfFile);

		$pObj->content = $pdfFile->getContent();
		header('Content-Type: application/pdf');

	}
}

 
