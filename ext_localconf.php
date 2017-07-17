<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['BE']['XCLASS']['typo3/alt_db_navframe.php'] = t3lib_extMgm::extPath($_EXTKEY).'class.ux_alt_db_navframe.php';

$_EXTCONF = unserialize($_EXTCONF);    // unserializing the configuration so we can use it here
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['defaultConfig'] = intval($_EXTCONF['defaultConfig']) ? true : false;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['colorSingle'] = intval($_EXTCONF['colorSingle']) ? true : false;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['labelSingle'] = intval($_EXTCONF['labelSingle']) ? true : false;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['advancedHighlight'] = intval($_EXTCONF['advancedHighlight']) ? true : false;

?>
