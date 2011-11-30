<?php

/**
 * handler for a file that
 * a) is temporary: it will be removed when the called script is finished
 * b) is accessible through the webserver
 *
 * @license WTFPL v2
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class tx_CzWkhtmltopdf_TemporaryFile {

	/**
	 * __construct()
	 * @param string|null $fileName
	 */
	public function __construct($fileName = null) {
		$this->setFileName($fileName);
	}

	/**
	 * __destruct()
	 *
	 * takes care of removing the file from the file system
	 */
	public function __destruct() {
		if(file_exists($this->getFilePath()) && is_file($this->getFilePath())) {
			unlink($this->getFilePath());
		}
	}

	/**
	 * the basename of the file
	 * 
	 * @var string
	 */
	protected $fileName;

	/**
	 * set the name of the file
	 *
	 * @param $fileName
	 * @return tx_czWkhtmltopdf_TemporaryFile
	 */
	public function setFileName($fileName = null) {
		if(!$fileName) {
			$tempNam = tempnam($this->getTmpFolderPath(), 'cz_wkhtmltopdf');
			$this->fileName = basename($tempNam);
		} else {
			$this->fileName = $fileName;
		}

		return $this;
	}

	/**
	 * get the full filePath to access over the local file system
	 *
	 * @return string
	 */
	public function getFilePath() {
		return $this->getTmpFolderPath().$this->fileName;
	}

	/**
	 * get the full serverFilePath to access over the web
	 *
	 * @return string
	 */
	public function getServerFilePath() {
		return $this->getTmpFolderServerPath().$this->fileName;
	}

	/**
	 * get the content of the file
	 *
	 * @return string
	 */
	public function getContent() {
		return file_get_contents($this->getFilePath());
	}

	/**
	 * set the content of the file
	 *
	 * @param string $content
	 * @return tx_CzWkhtmltopdf_TemporaryFile
	 */
	public function setContent($content) {
		file_put_contents($this->getFilePath(), $content);
		return $this;
	}

	/**
	 * get the tmp folder to access over the local file system
	 *
	 * @todo tempfolder should not be hardcoded
	 * @return string
	 */
	protected function getTmpFolderPath() {
		return rtrim(PATH_site, '/').'/typo3temp/';
	}

	/**
	 * get the tmp folder to access over the web
	 *
	 * @todo tempfolder should not be hardcoded
	 * @return string
	 */
	protected function getTmpFolderServerPath() {
		return rtrim($GLOBALS['TSFE']->baseUrl, '/').'/typo3temp/';
	}

}

 
