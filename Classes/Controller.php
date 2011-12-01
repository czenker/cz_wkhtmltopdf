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
		if(
				Tx_CzWkhtmltopdf_Config::getMode() & 2      && // processing of non-cached pages is enabled
				$this->isPageTypeEnabled($pObj->type)       && // the pageType matches
				$pObj->no_cache                                // this page was not cached
		) {
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
		if(
				Tx_CzWkhtmltopdf_Config::getMode() & 1      && // processing of cached pages is enabled
				$this->isPageTypeEnabled($pObj->type)       && // the pageType matches
				!$pObj->no_cache                               // this page will be cached
		) {
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

	/**
	 * returns true if the extension is configured to operate on this pageType
	 *
	 * @param integer $pageType
	 * @return boolean
	 */
	protected function isPageTypeEnabled($pageType) {
		return in_array($pageType, Tx_CzWkhtmltopdf_Config::getPageTypes());
	}
}

 
