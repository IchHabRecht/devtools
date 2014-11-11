<?php
namespace IchHabRecht\Devtools\Slot\Extensionmanager;

	/***************************************************************
	 *  Copyright notice
	 *
	 *  (c) 2014 Nicole Cordes <typo3@cordes.co>, CPS-IT GmbH
	 *
	 *  All rights reserved
	 *
	 *  This script is part of the TYPO3 project. The TYPO3 project is
	 *  free software; you can redistribute it and/or modify
	 *  it under the terms of the GNU General Public License as published by
	 *  the Free Software Foundation; either version 3 of the License, or
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
 * Adds icons to extension manager list view
 *
 * @author Nicole Cordes <typo3@cordes.co>
 * @package TYPO3
 * @subpackage tx_devtools
 */
class ProcessActions {

	/**
	 * @var bool
	 */
	protected $isJavascriptIncluded = FALSE;

	/**
	 * @param array $extension
	 * @param array $actions
	 * @return array
	 */
	public function markModifiedExtension($extension, &$actions) {
		if (!empty($extension['_md5_values_when_last_written'])) {
			$md5HashArray = \IchHabRecht\Devtools\Utility\ExtensionUtility::getMd5HashArrayForExtension($extension['key']);
			if ($extension['_md5_values_when_last_written'] !== serialize($md5HashArray)) {
				$title = $GLOBALS['LANG']->sL(\IchHabRecht\Devtools\Controller\Slot\AbstractSlotController::LANGUAGE_FILE .
					':slot.extensionmanager.process_actions.modified_files.title');
				$actions['isModified'] = '<a href="' .
					\TYPO3\CMS\Backend\Utility\BackendUtility::getAjaxUrl(
						'DevtoolsModifiedFilesController::listFiles',
						array(
							'extensionKey' => $extension['key']
						)
					) . '" class="list-modified-files" title="' . htmlspecialchars($title) . '">' .
					\TYPO3\CMS\Backend\Utility\IconUtility::getSpriteIcon('status-dialog-warning') . '</a>';
				if (!$this->isJavascriptIncluded) {
					$this->includeJavascript();
				}
			}
		}

		return FALSE;
	}

	/**
	 * @param array $extension
	 * @param array $actions
	 * @return array
	 */
	public function updateExtensionConfigurationFile($extension, &$actions) {
		if (isset($actions['isModified'])) {
			try {
				$packageManager = \TYPO3\CMS\Core\Core\Bootstrap::getInstance()->getEarlyInstance(('TYPO3\\Flow\\Package\\PackageManager'));
				/** @var \TYPO3\Flow\Package\PackageInterface $package */
				$package = $packageManager->getPackage($extension['key']);
				if (!$package->isProtected() && $package->getPackageMetaData()->getPackageType() !== 'typo3-cms-framework') {
					$configurationFile = $package->getPackagePath() . 'ext_emconf.php';
					if (is_writable($configurationFile)) {
						$title = $GLOBALS['LANG']->sL(\IchHabRecht\Devtools\Controller\Slot\AbstractSlotController::LANGUAGE_FILE .
							':slot.extensionmanager.process_actions.update_configuration.title');
						$actions['updateConfiguration'] = '<a href="' .
							\TYPO3\CMS\Backend\Utility\BackendUtility::getAjaxUrl(
								'DevtoolsUpdateConfigurationFileController::updateConfigurationFile',
								array(
									'extensionKey' => $extension['key']
								)
							) . '" class="update-configuration-file" title="' . $title . '">' .
							\TYPO3\CMS\Backend\Utility\IconUtility::getSpriteIcon('actions-document-export-t3d') . '</a>';
					}
				}
			} catch (\TYPO3\Flow\Package\Exception\UnknownPackageException $e) {
			}
		}

		return FALSE;
	}

	/**
	 * @return void
	 */
	protected function includeJavascript() {
		/** @var \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer */
		$pageRenderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Page\\PageRenderer');
		$pageRenderer->addJsFile(
			\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('devtools') . 'Resources/Public/Javascript/bindActions.js'
		);
		$pageRenderer->addInlineLanguageLabel(
			'devtools.error.title',
			$GLOBALS['LANG']->sL(\IchHabRecht\Devtools\Controller\Slot\AbstractSlotController::LANGUAGE_FILE .
				':slot.error.title')
		);
		$pageRenderer->addInlineLanguageLabel(
			'devtools.error.message',
			$GLOBALS['LANG']->sL(\IchHabRecht\Devtools\Controller\Slot\AbstractSlotController::LANGUAGE_FILE .
				':slot.error.message')
		);
		$this->isJavascriptIncluded = TRUE;
	}

}

?>