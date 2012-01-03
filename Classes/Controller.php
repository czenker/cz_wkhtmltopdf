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
		if(!$pObj->config['config']['tx_czwkhtmltopdf.']['enable']) {
			// if: post-processing is not enabled for this page type
			return;
		} elseif(!$pObj->no_cache) {
			// if: page is cached -> page should already be processed
			return;
		} elseif($pObj->config['config']['tx_czwkhtmltopdf.']['disableInt']) {
			//if: PDF generation was disabled for non-cached pages
			throw new t3lib_error_http_PageNotFoundException('PDF generation was disabled for this page.');
		} else {
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
		if(!$pObj->config['config']['tx_czwkhtmltopdf.']['enable']) {
			// if: post-processing is not enabled for this page type
			return;
		} elseif($pObj->no_cache) {
			// if: page is not cached -> page will be processed before output
			return;
		} else {
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

		if(!$converter) {
			throw new RuntimeException('Converter could not be initialized.');
		}
		if(!$pdfFile) {
			throw new RuntimeException('PDF file object could not be initialized.');
		}

		$converter->convert($pObj->content, $pdfFile, $pObj->config['config']['tx_czwkhtmltopdf.']['binParameters.']);

		$pObj->content = $pdfFile->getContent();
	}
}

 
