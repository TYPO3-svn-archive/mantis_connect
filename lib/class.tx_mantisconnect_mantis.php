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

if (t3lib_div::int_from_ver(TYPO3_version) < 4005000) {
	$pathEmMod = PATH_typo3 . 'mod/tools/em/';

	if (!defined('SOAP_1_2')) {
		require_once($pathEmMod . 'class.nusoap.php');
	}
	require_once($pathEmMod . 'class.em_soap.php');
}

/**
 * Library to connect to Mantis.
 *
 * @category    Library
 * @package     TYPO3
 * @subpackage  tx_mantisconnect
 * @author      Xavier Perseguers <typo3@perseguers.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     SVN: $Id$
 */
class tx_mantisconnect_mantis {

	/**
	 * @var string
	 */
	protected $username;

	/**
	 * @var string
	 */
	protected $password;

	/**
	 * @var em_soap
	 */
	protected $soap;

	/**
	 * Default constructor.
	 *
	 * @param string $wsdl
	 * @param string $username
	 * @param string $password
	 */
	public function __construct($wsdl, $username, $password) {
		$options = array(
        	'wsdl'        => $wsdl,
			'soapoptions' => array(
				'exceptions' => 1,
			),
        );
        $this->username = $username ? $username : FALSE;
        $this->password = $password ? $password : FALSE;

        if (t3lib_div::int_from_ver(TYPO3_version) >= 4005000) {
			$this->soap = t3lib_div::makeInstance('tx_em_connection_soap');
		} else {
			$this->soap = t3lib_div::makeInstance('em_soap');
		}

        try {
        	$this->soap->init($options, $this->username, $this->password);
        } catch (Exception $e) {
        	die('Could not initialize SOAP.');
        }
	}

	/**
	 * Returns the list of projects from Mantis bugtracker.
	 *
	 * @return array
	 */
	public function getProjects() {
		return $this->callMantis('mc_projects_get_user_accessible');
	}

	/**
	 * Returns the list of categories for a given Mantis project.
	 *
	 * @param integer $projectId
	 * @return array
	 */
	public function getCategories($projectId) {
		return $this->callMantis('mc_project_get_categories', array('project_id' => $projectId));
	}

	/**
	 * Creates an issue in Mantis.
	 *
	 * @param array $data
	 * @return integer
	 */
	public function createIssue(array $data) {
			// Take care of subkey 'id' for Mantis API
		$subkeys = array('project', 'view_state', 'severity', 'handler', 'priority', 'status', 'resolution', 'reproducibility');
		foreach ($data as $key => &$value) {
			if (t3lib_div::inArray($subkeys, $key)) {
				$value = array('id' => $value);
			}
		}

		return $this->callMantis('mc_issue_add', array('issue' => $data));
	}

	/**
	 * Performs a SOAP call to Mantis.
	 *
	 * @param string $method
	 * @param array $additionalParameters
	 * @return mixed
	 */
	protected function callMantis($method, array $additionalParameters = array()) {
		$parameters = array(
			'username' => $this->username,
			'password' => $this->password,
		);
		$parameters = array_merge($parameters, $additionalParameters);

		return $this->soap->call($method, $parameters);
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mantis_connect/lib/class.tx_mantisconnect_mantis.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mantis_connect/lib/class.tx_mantisconnect_mantis.php']);
}

?>