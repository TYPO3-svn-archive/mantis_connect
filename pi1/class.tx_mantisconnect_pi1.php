<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Xavier Perseguers <typo3@perseguers.ch>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Plugin 'Mantis Connect' for the 'mantis_connect' extension.
 *
 * @category    Plugin
 * @package     TYPO3
 * @subpackage  tx_mantisconnect
 * @author      Xavier Perseguers <typo3@perseguers.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     SVN: $Id$
 */
class tx_mantisconnect_pi1 extends tslib_pibase {

	public $prefixId      = 'tx_mantisconnect_pi1';
	public $scriptRelPath = 'pi1/class.tx_mantisconnect_pi1.php';
	public $extKey        = 'mantis_connect';

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * Main function.
	 *
	 * @param string $content: The Plugin content
	 * @param array $settings: The Plugin configuration
	 * @return string Content which appears on the website
	 */
	public function main($content, array $settings) {
		$this->init($settings);
		$this->pi_setPiVarDefaults();
		$this->pi_USER_INT_obj = 1;	// Configuring so caching is not expected.

		if (!$this->settings['wsdl']) {
			die('Plugin ' . $this->prefixId . ' is not configured properly!');
		}

		$connectorConfig = $this->getConnectorConfig();
		if ($connectorConfig) {
			$connector = t3lib_div::makeInstance($connectorConfig['class']);
			/* @var $connector tx_mantisconnect_connector */
			if (!($connector instanceof tx_mantisconnect_connector)) {
				throw new UnexpectedValueException('$connector must implement interface tx_mantisconnect_connector', 1280836382);
			}

			$config = array(
				'project'     => $this->settings['project'],
				'category'    => $this->settings['category'],
				'summary'     => '',
				'description' => '',
			);
			$config = array_merge($config, $this->settings['connectors.']['global.']);
			if (isset($this->settings['connectors.'][$connectorConfig['id'] . '.'])) {
				$config = array_merge($config, $this->settings['connectors.'][$connectorConfig['id'] . '.']);
			}

			$mantis = t3lib_div::makeInstance(
				'tx_mantisconnect_mantis',
				$this->settings['wsdl'],
				$this->settings['username'],
				$this->settings['password']
			);

			$connector->initialize($config, $mantis);
		}
	}

	/**
	 * Returns the configuration of the connector to be used.
	 *
	 * @return array
	 */
	protected function getConnectorConfig() {
		if ($this->settings['connector']) {
			$availableConnectors = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['connectors'];
			foreach ($availableConnectors as $connector) {
				if ($connector['id'] === $this->settings['connector']) {
					return $connector;
				}
			}
			throw new UnexpectedValueException('Invalid connector: ' . $this->settings['connector'], 1280836566);
		}
		return array();
	}

	/**
	 * This method performs various initializations.
	 *
	 * @param array $settings: Plugin configuration, as received by the main() method
	 * @return void
	 */
	protected function init(array $settings) {
			// Base configuration is equal the the plugin's TS setup
		$this->settings = $settings;

			// Load the flexform and loop on all its values to override TS setup values
			// Some properties use a different test (more strict than not empty) and yet some others no test at all
			// see http://wiki.typo3.org/index.php/Extension_Development,_using_Flexforms
		$this->pi_initPIflexForm(); // Init and get the flexform data of the plugin

			// Assign the flexform data to a local variable for easier access
		$piFlexForm = $this->cObj->data['pi_flexform'];

		if (is_array($piFlexForm['data'])) {
				// Traverse the entire array based on the language
				// and assign each configuration option to $this->settings array...
			foreach ($piFlexForm['data'] as $sheet => $langData) {
				foreach ($langData as $lang => $fields) {
					foreach (array_keys($fields) as $field) {
						$value = $this->pi_getFFvalue($piFlexForm, $field, $sheet);	

						if (!empty($value)) {
							$this->settings[$field] = $value;
						}
					}
				}
			}
		}

			// Override configuration with TS from FlexForm itself
		$flexformTyposcript = $this->settings['myTS'];
		unset($this->settings['myTS']);
		if ($flexformTyposcript) {
			require_once(PATH_t3lib.'class.t3lib_tsparser.php');
			$tsparser = t3lib_div::makeInstance('t3lib_tsparser');
			// Copy settings into existing setup
			$tsparser->setup = $this->settings;
			// Parse the new Typoscript
			$tsparser->parse($flexformTyposcript);
			// Copy the resulting setup back into settings
			$this->settings = $tsparser->setup;
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mantis_connect/pi1/class.tx_mantisconnect_pi1.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mantis_connect/pi1/class.tx_mantisconnect_pi1.php']);
}

?>