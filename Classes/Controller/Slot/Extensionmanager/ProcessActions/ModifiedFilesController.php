<?php
namespace IchHabRecht\Devtools\Controller\Slot\Extensionmanager\ProcessActions;

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

use IchHabRecht\Devtools\Controller\Slot\AbstractSlotController;
use IchHabRecht\Devtools\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Http\AjaxRequestHandler;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Shows modified files for an extension
 *
 * @author Nicole Cordes <typo3@cordes.co>
 */
class ModifiedFilesController extends AbstractSlotController
{
    /**
     * @var ExtensionUtility
     */
    protected $extensionUtility;

    /**
     * @var string
     */
    protected $translationPrefix = 'extensionmanager.process_actions.modified_files';

    public function __construct(ExtensionUtility $extensionUtility = null)
    {
        $this->extensionUtility = $extensionUtility ?: GeneralUtility::makeInstance(ExtensionUtility::class);
    }

    /**
     * @param array $ajaxParams
     * @param AjaxRequestHandler $ajaxObject
     */
    public function listFiles($ajaxParams, $ajaxObject)
    {
        $extensionKey = GeneralUtility::_GP('extensionKey');
        if (empty($extensionKey)) {
            return;
        }

        $packageManager = GeneralUtility::makeInstance(PackageManager::class);
        $extensionConfigurationPath = $packageManager->getPackage($extensionKey)->getPackagePath() . 'ext_emconf.php';
        $EM_CONF = null;
        if (file_exists($extensionConfigurationPath)) {
            $_EXTKEY = $extensionKey;
            include $extensionConfigurationPath;
        }

        if ($EM_CONF === null || empty($EM_CONF[$extensionKey])) {
            return;
        }
        $originalMd5HashArray = (array)unserialize($EM_CONF[$extensionKey]['_md5_values_when_last_written'], ['allowed_classes' => false]);
        $originalFileArray = array_keys($originalMd5HashArray);
        $currentMd5HashArray = $this->extensionUtility->getMd5HashArrayForExtension($extensionKey);
        $currentFileArray = array_keys($currentMd5HashArray);

        $removedFiles = array_diff($originalFileArray, $currentFileArray);
        $removedFiles = array_filter($removedFiles);
        $newFiles = array_diff($currentFileArray, $originalFileArray);
        $newFiles = array_filter($newFiles);
        $changedFiles = array_diff($originalMd5HashArray, $currentMd5HashArray);
        $changedFiles = array_filter($changedFiles);

        $messageArray = [];
        if (!empty($changedFiles)) {
            $messageArray[] = '<strong>' . $this->translate('changed_files') . ':</strong><br />' .
                implode('<br />', array_keys($changedFiles));
        }
        if (!empty($newFiles)) {
            $messageArray[] = '<strong>' . $this->translate('new_files') . ':</strong><br />' .
                implode('<br />', $newFiles);
        }
        if (!empty($removedFiles)) {
            $messageArray[] = '<strong>' . $this->translate('removed_files') . ':</strong><br />' .
                implode('<br />', $removedFiles);
        }

        $ajaxObject->setContentFormat('json');
        $ajaxObject->addContent('title', $this->translate('title'));
        $ajaxObject->addContent('message', implode('<br /><br />', $messageArray));
    }
}
