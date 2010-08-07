<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_mantisconnect_pi1.php', '_pi1', 'list_type', 0);

// Allow extensions to register themselves as connector providers
$TYPO3_CONF_VARS['EXTCONF']['mantis_connect']['connectors'] = array();

// Register built-in connectors
$TYPO3_CONF_VARS['EXTCONF']['mantis_connect']['connectors'][] = array(
	'id'     => 'powermail',
	'class'  => 'tx_mantisconnect_powermail',
	'label'  => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:connector.powermail'
);
?>