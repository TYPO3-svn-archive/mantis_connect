<?php

########################################################################
# Extension Manager/Repository config file for ext "mantis_connect".
#
# Auto generated 08-08-2010 19:28
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Mantis Connector',
	'description' => 'This extension allows you to connect TYPO3 to a Mantis bug tracker.',
	'category' => 'service',
	'author' => 'Xavier Perseguers',
	'author_email' => 'typo3@perseguers.ch',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.5.0',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.0-0.0.0',
			'typo3' => '4.3.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:17:{s:9:"ChangeLog";s:4:"c21a";s:16:"ext_autoload.php";s:4:"0f5e";s:12:"ext_icon.gif";s:4:"b640";s:17:"ext_localconf.php";s:4:"929b";s:14:"ext_tables.php";s:4:"8632";s:16:"locallang_db.xml";s:4:"1fdd";s:47:"connectors/class.tx_mantisconnect_powermail.php";s:4:"6eac";s:51:"connectors/interface.tx_mantisconnect_connector.php";s:4:"7301";s:14:"doc/manual.sxw";s:4:"fcf8";s:37:"lib/class.tx_mantisconnect_mantis.php";s:4:"2212";s:36:"lib/user_mantisconnect_powermail.php";s:4:"6fc1";s:39:"pi1/class.tx_mantisconnect_flexform.php";s:4:"b2d7";s:34:"pi1/class.tx_mantisconnect_pi1.php";s:4:"b1be";s:16:"pi1/flexform.xml";s:4:"8726";s:17:"pi1/locallang.xml";s:4:"a0c2";s:20:"static/constants.txt";s:4:"793f";s:16:"static/setup.txt";s:4:"625c";}',
);

?>