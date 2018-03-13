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

use IchHabRecht\Devtools\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Package\Exception\UnknownPackageException;
use TYPO3\CMS\Core\Package\Package;
use TYPO3\CMS\Core\Package\PackageManager;

/**
 * Adds icons to extension manager list view
 *
 * @author Nicole Cordes <typo3@cordes.co>
 */
class ProcessActions
{
    /**
     * @var IconFactory
     */
    protected $iconFactory;

    /**
     * @var bool
     */
    protected $isJavascriptIncluded = false;

    /**
     * @param IconFactory $iconFactory
     */
    public function injectIconFactory(IconFactory $iconFactory)
    {
        $this->iconFactory = $iconFactory;
    }

    /**
     * @param array $extension
     * @param array $actions
     * @return bool
     */
    public function processActions($extension, &$actions)
    {
        if ($this->isExtensionModified($extension)) {
            if (!$this->isJavascriptIncluded) {
                $this->includeJavascript();
            }
            $actions[] = $this->markModifiedExtension($extension);
            $actions[] = $this->updateExtensionConfigurationFile($extension);
        } else {
            $actions[] = '<span class="btn btn-default disabled">' .
                $this->iconFactory->getIcon('empty-empty', Icon::SIZE_SMALL)->render() . '</span>';
            $actions[] = '<span class="btn btn-default disabled">' .
                $this->iconFactory->getIcon('empty-empty', Icon::SIZE_SMALL)->render() . '</span>';
        }

        return false;
    }

    /**
     * @param array $extension
     * @return bool
     */
    protected function isExtensionModified($extension)
    {
        if (!empty($extension['_md5_values_when_last_written'])) {
            $md5HashArray = ExtensionUtility::getMd5HashArrayForExtension($extension['key']);

            return $extension['_md5_values_when_last_written'] !== serialize($md5HashArray);
        }

        return false;
    }

    /**
     * @param array $extension
     * @return string
     */
    protected function markModifiedExtension($extension)
    {
        $title = $GLOBALS['LANG']->sL(\IchHabRecht\Devtools\Controller\Slot\AbstractSlotController::LANGUAGE_FILE .
            ':slot.extensionmanager.process_actions.modified_files.title');

        return '<a href="' .
        \TYPO3\CMS\Backend\Utility\BackendUtility::getAjaxUrl(
            'DevtoolsModifiedFilesController::listFiles',
            [
                'extensionKey' => $extension['key'],
            ]
        ) . '" class="btn btn-default list-modified-files" title="' . htmlspecialchars($title) . '">' .
        $this->iconFactory->getIcon('status-dialog-warning', Icon::SIZE_SMALL)->render() . '</a>';
    }

    /**
     * @param array $extension
     * @return string
     */
    protected function updateExtensionConfigurationFile($extension)
    {
        try {
            $packageManager = Bootstrap::getInstance()->getEarlyInstance(PackageManager::class);
            /** @var Package $package */
            $package = $packageManager->getPackage($extension['key']);
            if (!$package->isProtected() && $package->getPackageMetaData()->getPackageType() !== 'typo3-cms-framework') {
                $configurationFile = $package->getPackagePath() . 'ext_emconf.php';
                if (is_writable($configurationFile)) {
                    $title = $GLOBALS['LANG']->sL(\IchHabRecht\Devtools\Controller\Slot\AbstractSlotController::LANGUAGE_FILE .
                        ':slot.extensionmanager.process_actions.update_configuration.title');

                    return '<a href="' .
                    \TYPO3\CMS\Backend\Utility\BackendUtility::getAjaxUrl(
                        'DevtoolsUpdateConfigurationFileController::updateConfigurationFile',
                        [
                            'extensionKey' => $extension['key'],
                        ]
                    ) . '" class="btn btn-default update-configuration-file" title="' . $title . '">' .
                    $this->iconFactory->getIcon('actions-document-export-t3d', Icon::SIZE_SMALL)->render() . '</a>';
                }
            }
        } catch (UnknownPackageException $e) {
        }

        return '';
    }

    /**
     * Add Javascript files and settings
     */
    protected function includeJavascript()
    {
        /** @var \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer */
        $pageRenderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Page\\PageRenderer');
        $pageRenderer->addJsFile(
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('devtools') . 'Resources/Public/JavaScript/bindActions.js'
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
        $this->isJavascriptIncluded = true;
    }
}
