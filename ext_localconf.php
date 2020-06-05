<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

//$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Lang\LanguageService::class] = [
//    'className' => \BPN\BpnLang\CMS\Lang\LanguageService::class
//];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Core\Localization\LanguageServiceFactory::class] = [
    'className' => \BPN\BpnLang\CMS\Core\Localization\LanguageServiceFactory::class
];

// Add BE control
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/backend.php']['renderPreProcess'][] =
    \BPN\BpnLang\BackEnd\BackEndController::class . '->renderPreProcess';
