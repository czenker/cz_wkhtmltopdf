<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2
 *
 *
 *            DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
 *                   Version 2, December 2004
 *
 * Copyright (C) 2012 Christian Zenker <christian.zenker@599media.de>
 *
 * Everyone is permitted to copy and distribute verbatim or modified
 * copies of this license document, and changing it is allowed as long
 * as the name is changed.
 *
 *           DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
 *  TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION
 *
 * 0. You just DO WHAT THE FUCK YOU WANT TO.
 */


/**
 * @license WTFPL v2
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class tx_CzWkhtmltopdf_Controller {

	/**
	 * @var tslib_fe
	 */
	protected $pObj;

	/**
	 * called before any page content is send to the browser
	 *
	 * @param array $params
	 * @param tslib_fe $pObj
	 * @return void
	 */
	public function hookOutput(&$params, $pObj) {
		$this->pObj = $pObj;

		if(!$this->pObj->no_cache) {
			// if: page is cached -> page should already be processed
			return;
		} elseif(!$this->isEnabled()) {
			// if: post-processing is not enabled for this page type
			return;
		} elseif($this->pObj->config['config']['tx_czwkhtmltopdf.']['disableInt']) {
			//@deprecated don't use disabledInt anymore. Use stdWrap of enable instead
			//if: PDF generation was disabled for non-cached pages
			$this->throw404('PDF generation was disabled for this page.');
		} else {
			$this->processHook();
		}
	}

	/**
	 * called before anything is written to the cache
	 *
	 * @param tslib_fe $pObj
	 * @return void
	 */
	public function hook_indexContent($pObj) {
		$this->pObj = $pObj;

		if($this->pObj->no_cache) {
			// if: page is not cached -> page will be processed before output
			return;
		} elseif(!$this->isEnabled()) {
			// if: post-processing is not enabled for this page type
			return;
		} else {
			$this->processHook();
		}
	}

	/**
	 * process a hook
	 *
	 * @return void
	 */
	protected function processHook() {
		$converter = t3lib_div::makeInstance('tx_CzWkhtmltopdf_Converter');

		if(!$converter) {
			throw new RuntimeException('Converter could not be initialized.');
		}

		$this->pObj->content = $converter->convert($this->pObj->content, $this->pObj->config['config']['tx_czwkhtmltopdf.']['binParameters.']);
	}

	/**
	 * returns true if this page should be converted to PDF
	 * if explicitly disabled it throws an exception for TYPO3 4.6 and up or
	 * calls tslib_fe::pageNotFoundAndExit() for TYPO3 4.5 and below
	 *
	 * @throws t3lib_error_http_PageNotFoundException
	 * @return boolean
	 */
	protected function isEnabled() {

		if(
			!array_key_exists('tx_czwkhtmltopdf.', $this->pObj->config['config']) ||
			(
				!array_key_exists('enable', $this->pObj->config['config']['tx_czwkhtmltopdf.']) &&
				!array_key_exists('enable.', $this->pObj->config['config']['tx_czwkhtmltopdf.'])
			)
		) {
			//if: tx_czwkhtmltopdf was not configured for this page type
			return false;
		} elseif($GLOBALS['TSFE']->cObj->stdWrap(
			$this->pObj->config['config']['tx_czwkhtmltopdf.']['enable'],
			$this->pObj->config['config']['tx_czwkhtmltopdf.']['enable.']
		)) {
			//if: tx_czwkhtmltopdf was configured and is enabled
			return true;
		} else {
			//if: tx_czwkhtmltopdf was explicitly disabled
			$this->throw404('PDF generation was disabled for this page.');
		}
	}

	/**
	 * abort page rendering and show a 404 page
	 *
	 * @param $message
	 * @throws t3lib_error_http_PageNotFoundException
	 */
	protected function throw404($message) {

		if(class_exists('t3lib_error_http_PageNotFoundException')) {
			//if: TYPO3 4.6 and above
			throw new t3lib_error_http_PageNotFoundException($message);
		} else {
			//if: TYPO3 4.5 and below
			$this->pObj->pageNotFoundAndExit($message);
		}
	}

}

 
