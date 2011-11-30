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
		//TODO: I think there is a better way to get the temp folder
		$tempFolder = PATH_site.'typo3temp/';
		$tempServerFolder = rtrim($obj->baseUrl, '/').'typo3temp/';

		$fileName = $pObj->cHash ?
			$pObj->cHash :
			rand()
		;

		file_put_contents($tempFolder.$fileName.'.html', $pObj->content);

		system(sprintf(
			'/tmp/wkhtmltopdf-i386 %s %s',
			$tempServerFolder.$fileName.'.html',
			$tempFolder.$fileName.'.pdf'
		));

		$pObj->content = file_get_contents($tempFolder.$fileName.'.pdf');
		header('Content-Type: application/pdf');

	}
}

 
