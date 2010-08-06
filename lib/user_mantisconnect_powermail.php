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
 * Userfunctions for Powermail.
 *
 * @category    Library
 * @package     TYPO3
 * @subpackage  tx_mantisconnect
 * @author      Xavier Perseguers <typo3@perseguers.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     SVN: $Id$
 */
class user_mantisconnect_powermail {

	/**
	 * Returns a Powermail select form field with Mantis projects.
	 * 
	 * @param string $content
	 * @param array $config
	 * @return array
	 */
	public function getProjects($content, array $config) {
		$config = $config['userFunc.'];

		if (!$config['wsdl']) {
			die('Constant plugin.tx_mantisconnect.wsdl is undefined');
		}

		$mantis = t3lib_div::makeInstance(
			'tx_mantisconnect_mantis',
			$config['wsdl'],
			$config['username'],
			$config['password']
		);

		$projects = $mantis->getProjects();
		$items = array();
		if (is_array($projects)) {
			$this->addProjects($items, $projects, 0);
		}

		return $this->getPowermailSelect($this->cObj->data['uid'], $this->cObj->data['label'], $items);
	}

	/**
	 * Returns a select form field for Powermail.
	 *
	 * @param integer $uid
	 * @param string $label
	 * @param array $items
	 */
	protected function getPowermailSelect($uid, $label, array $items) {
		$cssClasses = array(
			'tx_powermail_pi1_fieldwrap_html',
			'tx_powermail_pi1_fieldwrap_html_select',
			'tx_powermail_pi1_fieldwrap_html_' . $uid,
		);
		$field = 'uid' . $uid;

		$content .= '<div id="powermaildiv_' . $field . '" class="' . implode(' ', $cssClasses) . '">
						<label for="' . $field . '">' . $label .
						'<span class="powermail_mandatory">*</span></label>
					';

		$cssClasses = array(
			'required',
			'powermail_select',
			'powermail_' . $field,
		);
		$content .= '<select id="' . $field . '" class="' . implode(' ', $cssClasses) . '" size="1" name="tx_powermail_pi1[' . $field . ']>';
		foreach ($items as $item) {
			$content .= '<option value="' . $item[1] . '">' . $item[0] . '</option>';
		}
		$content .= '</select>';
		$content .= '</div>';

		return $content;
	}

	/**
	 * Recursively adds projects and subprojects to array $items.
	 *
	 * @param array $items
	 * @param array $projects
	 * @param integer $level
	 * @return void
	 */
	protected function addProjects(array &$items, array $projects, $level) {
		foreach ($projects as $project) {
			$name = str_repeat('&nbsp;', 4 * $level) . $project['name'];
			$items[] = array($name, $project['id']);
			if (is_array($project['subprojects'])) {
				$this->addProjects($items, $project['subprojects'], $level + 1);
			}
		}
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mantis_connect/lib/user_mantisconnect_powermail.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mantis_connect/lib/user_mantisconnect_powermail.php']);
}

?>