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
			$input = t3lib_div::makeInstance('tx_CzWkhtmltopdf_TemporaryFile', 'html')->setContent($input);
		}

		/**
		 * just remember if this method should return the result as string
		 * @var bool
		 */
		$directReturn = is_null($out);

		if(!$output instanceof tx_CzWkhtmltopdf_TemporaryFile) {
			$output = t3lib_div::makeInstance('tx_CzWkhtmltopdf_TemporaryFile');
		}

		if(!$input || !$output) {
			throw new RuntimeException('Input or Output file object could not be created.');
		}

		$binary = Tx_CzWkhtmltopdf_Config::getBinaryPath();
		$inputServerFilePath = $input->getServerFilePath();
		$outputFilePath = $output->getFilePath();

		if(empty($binary)) {
			throw new InvalidArgumentException('No binary for wkhtmltopdf was specified.');
		}
		if(empty($inputServerFilePath)) {
			throw new InvalidArgumentException('Could not determine the file path for the input file.');
		}
		if(empty($outputFilePath)) {
			throw new InvalidArgumentException('Could not determine the file path for the output file.');
		}

		$cmd = sprintf(
			'%s %s %s 2>&1',
			$binary,
			escapeshellarg($inputServerFilePath),
			escapeshellarg($outputFilePath)
		);
		
		if(system($cmd, $returnVar) == FALSE) {
			throw new RuntimeException('Something went wrong while trying to create the PDF.');
		};

		if($directReturn) {
			return $output->getContent();
		}
	}

}

 
