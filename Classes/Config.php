<?php

/**
 * A class holding configuration from the extensions configuration in TYPO3_CONF_VARS
 *
 * @license WTFPL v2
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class Tx_CzWkhtmltopdf_Config {

	const EXTKEY = 'cz_wkhtmltopdf';

	/**
	 * the configuration
	 * @var array
	 */
	protected static $data = null;

	/**
	 * get a value
	 * @param string $name
	 * @throws InvalidArgumentException
	 */
	public static function get($name) {
		self::init();

		if(!self::exists($name)) {
			throw new InvalidArgumentException(sprintf(
				'The value "%s" was not set. Did you update the Extensions settings of "%s"?',
				$name,
				self::EXTKEY
			));
		}

		return self::$data[$name];
	}

	/**
	 * check if the value exists
	 *
	 * @param $name
	 * @return boolean
	 */
	public static function exists($name) {
		self::init();
		return is_array(self::$data) && array_key_exists($name, self::$data);
	}

	/**
	 * set a value of an array of values
	 *
	 * @param string|array $name
	 * @param string $value
	 * @throws InvalidArgumentException
	 */
	public static function set($name, $value = null) {
		self::init();
		if(is_string($name)) {
			self::$data[$name] = $value;
		} elseif(is_array($name)) {
			self::$data = array_merge(
				self::$data,
				$name
			);
		} else {
			throw new InvalidArgumentException('The value "name" must be a string or array.');
		}
	}

	/**
	 * initializing method that will be called as soon as it is needed
	 */
	protected static function init() {
		if(is_null(self::$data)) {
			self::$data = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][self::EXTKEY]);
		}
	}

	/**
	 * get the configured path for the wkhtmltopdf binary
	 * 
	 * @static
	 * @return string
	 */
	public static function getBinaryPath() {
		$binPath = self::get('binPath');
		if($binPath === 'i386') {
			return t3lib_extMgm::extPath(self::EXTKEY, 'Vendor/wkhtmltopdf/wkhtmltopdf-i386');
		} elseif($binPath === 'amd64') {
			return t3lib_extMgm::extPath(self::EXTKEY, 'Vendor/wkhtmltopdf/wkhtmltopdf-amd64');
		} else {
			return self::get('binPathCustom');
		}
	}

	/**
	 * get the operation mode of this extension
	 *
	 * @static
	 * @return int
	 */
	public static function getMode() {
		return intval(self::get('mode'));
	}

	/**
	 * get the configured valid pageTypes
	 *
	 * @static
	 * @return array
	 */
	public static function getPageTypes() {
		return t3lib_div::intExplode(',', self::get('pageTypes'), true);
	}

}