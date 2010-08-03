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
 * FlexForm Helper.
 *
 * @category    FlexForm Helper
 * @package     TYPO3
 * @subpackage  tx_mantisconnect
 * @author      Xavier Perseguers <typo3@perseguers.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     SVN: $Id$
 */
class tx_mantisconnect_flexform {

	/**
	 * @var array
	 */
	protected $config = array();

	/**
	 * @var tx_mantisconnect_mantis
	 */
	protected $mantis;

	/**
	 * Returns the available connectors.
	 *
	 * @param array $settings
	 * @return array
	 */
	public function getConnectors(array $settings) {
		$connectors = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mantis_connect']['connectors'];
		$items = array();
		foreach ($connectors as $connector) {
			$label = $GLOBALS['LANG']->sL($connector['label']);
			$key = $connector['id'];
			$items[] = array($label, $key);
		}
		$settings['items'] = array_merge($settings['items'], $items);

		return $settings;
	}

	/**
	 * Returns the list of projects from Mantis bugtracker.
	 *
	 * @param array $settings
	 * @return array
	 */
	public function getProjects(array $settings) {
		$this->initialize($settings);

		if ($this->mantis) {
			$projects = $this->mantis->getProjects();
			if (is_array($projects)) {
				$items = array();
				$this->addProjects($items, $projects, 0);
				$settings['items'] = array_merge($settings['items'], $items);
			}
		}

		return $settings;
	}

	/**
	 * Returns the list of categories for a given Mantis project.
	 *
	 * @param array $settings
	 * @return array
	 */
	public function getCategories(array $settings) {
		$this->initialize($settings);

		if ($this->mantis) {
			$categories = $this->mantis->getCategories($this->config['project']);
			$items = array();
			foreach ($categories as $key => $category) {
				$items[] = array($category, $category);
			}
			$settings['items'] = array_merge($settings['items'], $items);
		}

		return $settings;
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

	/**
	 * Initializes the configuration.
	 *
	 * @param array $settings
	 * @return void
	 */
	protected function initialize(array $settings) {
		$flexForm = $settings['row'][$settings['field']];
		$flexForm = t3lib_div::xml2array($flexForm);

		foreach ($flexForm['data'] as $sheet => $data) {
			foreach ($data as $lang => $value) {
				foreach ($value as $key => $val) {
					$this->config[$key] = $this->getFFvalue($flexForm, $key, $sheet);
				}
			}
		}

		if ($this->config['wsdl']) {
			$this->mantis = t3lib_div::makeInstance(
				'tx_mantisconnect_mantis',
				$this->config['wsdl'],
				$this->config['username'],
				$this->config['password']
			);
		}
	}

	/**
	 * Return value from somewhere inside a FlexForm structure
	 *
	 * @param	array		FlexForm data
	 * @param	string		Field name to extract. Can be given like "test/el/2/test/el/field_templateObject" where each part will dig a level deeper in the FlexForm data.
	 * @param	string		Sheet pointer, eg. "sDEF"
	 * @param	string		Language pointer, eg. "lDEF"
	 * @param	string		Value pointer, eg. "vDEF"
	 * @return	string		The content.
	 */
	protected function getFFvalue(array $T3FlexForm_array, $fieldName, $sheet = 'sDEF', $lang = 'lDEF', $value = 'vDEF') {
		$sheetArray = $T3FlexForm_array['data'][$sheet][$lang];
		if (is_array($sheetArray)) {
			return $this->getFFvalueFromSheetArray($sheetArray, explode('/', $fieldName), $value);
		}
	}

	/**
	 * Returns part of $sheetArray pointed to by the keys in $fieldNameArray
	 *
	 * @param	array		Multidimensiona array, typically FlexForm contents
	 * @param	array		Array where each value points to a key in the FlexForms content - the input array will have the value returned pointed to by these keys. All integer keys will not take their integer counterparts, but rather traverse the current position in the array an return element number X (whether this is right behavior is not settled yet...)
	 * @param	string		Value for outermost key, typ. "vDEF" depending on language.
	 * @return	mixed		The value, typ. string.
	 * @see getFFvalue()
	 */
	protected function getFFvalueFromSheetArray(array $sheetArray, array $fieldNameArr, $value) {
		$tempArr = $sheetArray;
		foreach ($fieldNameArr as $k => $v) {
			if (t3lib_div::testInt($v)) {
				if (is_array($tempArr)) {
					$c=0;
					foreach ($tempArr as $values) {
						if ($c == $v)	{
							$tempArr=$values;
							break;
						}
						$c++;
					}
				}
			} else {
				$tempArr = $tempArr[$v];
			}
		}
		return $tempArr[$value];
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mantis_connect/pi1/class.tx_mantisconnect_flexform.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mantis_connect/pi1/class.tx_mantisconnect_flexform.php']);
}

?>