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
 * provides statuses for the TYPO3 reports module
 *
 * @license WTFPL v2
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class Tx_CzWkhtmltopdf_Report implements tx_reports_StatusProvider {

	/**
	 * @var array
	 */
	protected $reports = array();

	/**
     * Returns status of filesystem
     *
     * @return    array    An array of tx_reports_reports_status_Status objects
     */
	public function getStatus() {
		$this->reports = array();

		$passConfiguration = $this->checkConfiguration();
		$passProcOpen = $this->checkProcOpenFunctionExists();

		if($passConfiguration && $passProcOpen) {
			$this->checkBinary();
		}

		$this->checkOperatingSystem();
		$this->checkPostProcHook();
		$this->checkPageIndexingHook();

		return $this->reports;
	}

	/**
	 * check if the configuration of the extension is ok
	 *
	 * @return bool
	 */
	protected function checkConfiguration() {
		$pass = FALSE;
		try {
			Tx_CzWkhtmltopdf_Config::getBinaryPath();
			$pass = TRUE;
		}
		catch(InvalidArgumentException $e) {}

		$this->reports[] = t3lib_div::makeInstance('tx_reports_reports_status_Status',
			'Configuration', // title
			$pass ? 'OK' : 'ERROR', // value
			$pass ? '' : 'Please go to the Extension Manager and hit the "Update" button of the configuration of '.Tx_CzWkhtmltopdf_Config::EXTKEY , //message
			$pass ? tx_reports_reports_status_Status::OK : tx_reports_reports_status_Status::ERROR //severity
		);

		return $pass;
	}

	/**
	 * check that proc_open() is not disallowed
	 *
	 * @return bool
	 */
	protected function checkProcOpenFunctionExists() {
		$pass = function_exists('proc_open');

		$this->reports[] = t3lib_div::makeInstance('tx_reports_reports_status_Status',
			'proc_open() is available', // title
			$pass ? 'OK' : 'Disallowed', // value
			$pass ? '' : 'proc_open() is need to call the wkhtmltopdf binary. If it is disabled please ask your Administrator or Hoster for help.' , //message
			$pass ? tx_reports_reports_status_Status::OK : tx_reports_reports_status_Status::ERROR //severity
		);

		return $pass;
	}

	/**
	 * check if the binary can be run
	 *
	 * @return bool
	 */
	protected function checkBinary() {
		$binary = Tx_CzWkhtmltopdf_Config::getBinaryPath();

		if(empty($binary)) {
			$this->reports[] = t3lib_div::makeInstance('tx_reports_reports_status_Status',
				'wkhtmltopdf binary', // title
				'not configured',
				'The path to the binary is not configured. Please head over to the Extension Manager and configure the path in the configuration of '.Tx_CzWkhtmltopdf_Config::EXTKEY , //message
				tx_reports_reports_status_Status::ERROR //severity
			);

			return FALSE;
		}


		$cmd = escapeshellcmd($binary);

		$proc=proc_open($cmd,array(0=>array('pipe','r'),1=>array('pipe','w'),2=>array('pipe','w')),$pipes);
        fwrite($pipes[0],$input);
        fclose($pipes[0]);
        $stdout=stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr=stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $returnCode = proc_close($proc);

		$pass = intval($returnCode) <= 1;

		if($pass) {
			$this->reports[] = t3lib_div::makeInstance('tx_reports_reports_status_Status',
				'wkhtmltopdf binary', // title
				$binary,
				'' , //message
				tx_reports_reports_status_Status::OK //severity
			);
		} else {
			$this->reports[] = t3lib_div::makeInstance('tx_reports_reports_status_Status',
				'wkhtmltopdf binary', // title
				$stderr,
				'The error code was '.$returnCode.'.' , //message
				tx_reports_reports_status_Status::ERROR //severity
			);
		}


		return $pass;
	}

	/**
	 * check the used operating system and give some hint on the binary to use
	 */
	protected function checkOperatingSystem() {

		if(strtolower(PHP_OS) === 'linux' || strtolower(PHP_OS) === 'unix') {
			if(function_exists('exec')) {
				$architecture = exec('uname -m');
				$os = $architecture ? sprintf('%s (%s)', PHP_OS, $architecture) : PHP_OS;
			} else {
				$os = PHP_OS;
			}

			$this->reports[] = t3lib_div::makeInstance('tx_reports_reports_status_Status',
				'Operating System', // title
				$os,
				'' , //message
				tx_reports_reports_status_Status::NOTICE //severity
			);
		} elseif(strtolower(PHP_OS) === 'windows') {
			$this->reports[] = t3lib_div::makeInstance('tx_reports_reports_status_Status',
				'Operating System', // title
				PHP_OS,
				Tx_CzWkhtmltopdf_Config::EXTKEY.' does not have Windows binaries bundled. You can <a href="http://code.google.com/p/wkhtmltopdf/downloads/list" target="_blank">download them</a>. Make sure your server has the necessary binary installed and set it in the Extension Configuration.' , //message
				tx_reports_reports_status_Status::INFO //severity
			);
		} elseif(strtolower(PHP_OS) === 'darwin') {
			$this->reports[] = t3lib_div::makeInstance('tx_reports_reports_status_Status',
				'Operating System', // title
				PHP_OS,
				Tx_CzWkhtmltopdf_Config::EXTKEY.' does not have MAC OS X binaries bundled. You can <a href="http://code.google.com/p/wkhtmltopdf/downloads/list" target="_blank">download them</a>. Make sure your server has the necessary binary installed and set it in the Extension Configuration.' , //message
				tx_reports_reports_status_Status::INFO //severity
			);
		} else {
			$this->reports[] = t3lib_div::makeInstance('tx_reports_reports_status_Status',
				'Operating System', // title
				PHP_OS,
				'Support for your operating system is unknown. Check the <a href="http://code.google.com/p/wkhtmltopdf/downloads/list" target="_blank">download page of wkhtmltopdf</a> for binaries for your Operating System.' , //message
				tx_reports_reports_status_Status::INFO //severity
			);
		}
	}

	/**
	 * check for conflicts with with other extensions using 'contentPostProc-output' of 'tslib/class.tslib_fe.php'
	 */
	protected function checkPostProcHook() {
		$hooking = implode('</li><li>',$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output']);
		$hookCount = count($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output']);

		if($hookCount > 1) {
			$this->reports[] = t3lib_div::makeInstance('tx_reports_reports_status_Status',
				'Hook: contentPostProc-output', // title
				sprintf('%d classes use this hook', $hookCount),
				'Make sure the classes calling the hook don\'t influence each other negatively:<br /><ol><li>'.$hooking.'</li></ol>' , //message
				tx_reports_reports_status_Status::INFO //severity
			);
		} else {
			$this->reports[] = t3lib_div::makeInstance('tx_reports_reports_status_Status',
				'Hook: contentPostProc-output', // title
				'OK',
				Tx_CzWkhtmltopdf_Config::EXTKEY.' is the only extension using this hook.' , //message
				tx_reports_reports_status_Status::OK //severity
			);
		}
	}

	/**
	 * check for conflicts with with other extensions using 'pageIndexing' of 'tslib/class.tslib_fe.php'
	 */
	protected function checkPageIndexingHook() {
		$hooking = implode('</li><li>',$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['pageIndexing']);
		$hookCount = count($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['pageIndexing']);

		if($hookCount > 1) {
			$this->reports[] = t3lib_div::makeInstance('tx_reports_reports_status_Status',
				'Hook: pageIndexing', // title
				sprintf('%d classes use this hook', $hookCount),
				'Make sure the classes calling the hook don\'t influence each other negatively.<br /><ol><li>'.$hooking.'</li></ol>' , //message
				tx_reports_reports_status_Status::INFO //severity
			);
		} else {
			$this->reports[] = t3lib_div::makeInstance('tx_reports_reports_status_Status',
				'Hook: pageIndexing', // title
				'OK',
				Tx_CzWkhtmltopdf_Config::EXTKEY.' is the only extension using this hook.' , //message
				tx_reports_reports_status_Status::OK //severity
			);
		}
	}

}