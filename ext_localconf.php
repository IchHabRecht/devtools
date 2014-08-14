<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

if (TYPO3_MODE === 'BE') {
	\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher')->connect(
		'TYPO3\\CMS\\Extensionmanager\\ViewHelpers\\ProcessAvailableActionsViewHelper',
		'processActions',
		'IchHabRecht\\Devtools\\Slot\\Extensionmanager\\ProcessActions',
		'markModifiedExtension'
	);
	\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher')->connect(
		'TYPO3\\CMS\\Extensionmanager\\ViewHelpers\\ProcessAvailableActionsViewHelper',
		'processActions',
		'IchHabRecht\\Devtools\\Slot\\Extensionmanager\\ProcessActions',
		'updateExtensionConfigurationFile'
	);

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
		'DevtoolsModifiedFilesController::listFiles',
		'IchHabRecht\\Devtools\\Controller\\Slot\\Extensionmanager\\ProcessActions\\ModifiedFilesController->listFiles'
	);
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
		'DevtoolsUpdateConfigurationFileController::updateConfigurationFile',
		'IchHabRecht\\Devtools\\Controller\\Slot\\Extensionmanager\\ProcessActions\\UpdateConfigurationFileController->updateConfigurationFile'
	);
}
