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
 * PowerMail connector for Mantis.
 *
 * @category    Connectors
 * @package     TYPO3
 * @subpackage  tx_mantisconnect
 * @author      Xavier Perseguers <typo3@perseguers.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     SVN: $Id$
 */
class tx_mantisconnect_powermail implements tx_mantisconnect_connector {

	/**
	 * @var array
	 */
	protected static $config;

	/**
	 * @var tx_mantisconnect_mantis
	 */
	protected static $mantis;

	/**
	 * Initializes the connector.
	 *
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config, tx_mantisconnect_mantis $mantis) {
			// Use hook in submit.php to send values to Mantis
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_SubmitLastOne'][] = 'EXT:mantis_connect/connectors/' . basename(__FILE__) . ':' . __CLASS__;

		self::$config = $config;
		self::$mantis = $mantis;
	}

	/**
	 * Hook from PowerMail used to send a new issue to Mantis when the form is submitted.
	 *
	 * @param string $content
	 * @param array $conf
	 * @param array $data
	 * @param integer $ok
	 * @param tx_powermail_submit $pObj
	 * @return void
	 */
    public function PM_SubmitLastOneHook($content, array $conf, array $data, $ok, tx_powermail_submit $pObj) {
		$issueData = self::$config;

		$contentObj = t3lib_div::makeInstance('tslib_cObj');
		$contentObj->start($data);

		foreach ($issueData as $key => $value) {
			if (substr($key, -1) === '.' && isset($value['cObject'])) {
				$baseKey = substr($key, 0, strlen($key) - 1);
				$issueData[$baseKey] = $contentObj->cObjGetSingle($value['cObject'], $value['cObject.']);
				unset($issueData[$key]);
			}
		}

		self::$mantis->createIssue($issueData);
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mantis_connect/connectors/class.tx_mantisconnect_powermail.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mantis_connect/connectors/class.tx_mantisconnect_powermail.php']);
}

?>