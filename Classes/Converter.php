<?php

/**
 * takes a string of HTML or a tx_CzWkhtmltopdf_TemporaryFile representing an HTML file
 * and converts it into a string of PDF or a tx_CzWkhtmltopdf_TemporaryFile representing a PDF file.
 *
 * @license WTFPL v2
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class tx_CzWkhtmltopdf_Converter {

	/**
	 * @param string|tx_CzWkhtmltopdf_TemporaryFile $input
	 * @param tx_CzWkhtmltopdf_TemporaryFile|null $output
	 * @return
	 */
	public function convert($input, $output = null) {
		if(!$input instanceof tx_CzWkhtmltopdf_TemporaryFile) {
			/**
			 * @var tx_CzWkhtmltopdf_TemporaryFile
			 */
			$input = t3lib_div::makeInstance('tx_CzWkhtmltopdf_TemporaryFile')->setContent($input);
		}

		/**
		 * just remember if this method should return the result as string
		 * @var bool
		 */
		$directReturn = is_null($out);

		if(!$output instanceof tx_CzWkhtmltopdf_TemporaryFile) {
			$output = t3lib_div::makeInstance('tx_CzWkhtmltopdf_TemporaryFile');
		}

		$cmd = sprintf(
			'%s %s %s &2>1',
			Tx_CzWkhtmltopdf_Config::getBinaryPath(), // binary
			$input->getServerFilePath(), // input file
			$output->getFilePath() // output file
		);
		
		system($cmd);

		if($directReturn) {
			return $output->getContent();
		}
	}

}

 
