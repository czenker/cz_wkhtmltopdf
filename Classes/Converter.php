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
 * takes a string of HTML or a tx_CzWkhtmltopdf_TemporaryFile representing an HTML file
 * and converts it into a string of PDF or a tx_CzWkhtmltopdf_TemporaryFile representing a PDF file.
 *
 * @license WTFPL v2
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class tx_CzWkhtmltopdf_Converter {

	/**
	 * @see http://code.google.com/p/wkhtmltopdf/wiki/IntegrationWithPhp
	 *
	 * @param string $input
	 * @param array $options
	 * @return
	 */
	public function convert($input, $options = array()) {

		$binary = Tx_CzWkhtmltopdf_Config::getBinaryPath();

		if(empty($binary)) {
			throw new InvalidArgumentException('No binary for wkhtmltopdf was specified.');
		}

		/*
		 * use stdin and stdout for file creation (no temporary files needed)
		 *
		 * short explanation:
		 *   1. the two dashes (-) in the $cmd ask the binary to read from stdin and write to stdout
		 *   2. proc_open() executes the binary and allows reading/writing of stdin and stdout
		 *
		 * @see http://code.google.com/p/wkhtmltopdf/wiki/IntegrationWithPhp
		 * @see http://php.net/manual/en/function.proc-open.php
		 */

		$cmd = sprintf(
			'%s %s - -',
			escapeshellcmd($binary),
			$this->formatBinaryOptions($options)
		);

		$proc=proc_open($cmd,array(0=>array('pipe','r'),1=>array('pipe','w'),2=>array('pipe','w')),$pipes);
        fwrite($pipes[0],$input);
        fclose($pipes[0]);
        $stdout=stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr=stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $returnCode = proc_close($proc);

		if(intval($returnCode) > 1) {
			// usually thrown when an invalid binary is called
			throw new RuntimeException('A shell error occured while trying to run WKPDF: '.$returnCode);
		}

		$stderrL = strtolower($stderr);
		if(strpos($stderrL,'error') !== FALSE || strpos($stderrL,'unknown') !== FALSE) {
			t3lib_div::devLog('WKPDF threw an error: <pre>'.$stderr.'</pre>', Tx_CzWkhtmltopdf_Config::EXTKEY, t3lib_div::SYSLOG_SEVERITY_FATAL);
			throw new RuntimeException('WKPDF threw an error when trying to create a PDF. Developers see the developer log for more information.');
		}

		if(trim($stdout) == '') {
			t3lib_div::devLog('WKPDF did not return anything: <pre>'.$stderr.'</pre>', Tx_CzWkhtmltopdf_Config::EXTKEY, t3lib_div::SYSLOG_SEVERITY_FATAL);
			throw new RuntimeException('WKPDF did not return anything when trying to create a PDF. Developers see the developer log for more information.');
		}

		return $stdout;
	}

	/**
	 * format and escape all options for the binary call
	 *
	 * @param array $options
	 * @return string
	 */
	protected function formatBinaryOptions($options) {
		$return = array();
		foreach($options as $name=>$value) {
			if($value !== '') {
				$return[] = sprintf(
					'--%s %s',
					escapeshellcmd($name),
					escapeshellarg($value)
				);
			} else {
				$return[] = '--'.escapeshellcmd($name);
			}
		}
		return implode(' ',$return);
	}

}

 
