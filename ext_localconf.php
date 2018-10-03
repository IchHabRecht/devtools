<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (TYPO3_MODE === 'BE') {
    \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher')->connect(
        \TYPO3\CMS\Extensionmanager\ViewHelpers\ProcessAvailableActionsViewHelper::class,
        \TYPO3\CMS\Extensionmanager\ViewHelpers\ProcessAvailableActionsViewHelper::SIGNAL_ProcessActions,
        \IchHabRecht\Devtools\Slot\Extensionmanager\ProcessActions::class,
        'addActions'
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers']['devtools'] =
        \IchHabRecht\Devtools\Command\ExtensionConfigurationCommandController::class;
}
