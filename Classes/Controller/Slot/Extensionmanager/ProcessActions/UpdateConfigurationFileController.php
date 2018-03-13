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

use IchHabRecht\Devtools\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extensionmanager\Utility\EmConfUtility;

/**
 * Shows modified files for an extension
 *
 * @author Nicole Cordes <typo3@cordes.co>
 */
class UpdateConfigurationFileController extends \IchHabRecht\Devtools\Controller\Slot\AbstractSlotController
{
    /**
     * @var string
     */
    protected $translationPrefix = 'extensionmanager.process_actions.update_configuration';

    /**
     * @param array $ajaxParams
     * @param \TYPO3\CMS\Core\Http\AjaxRequestHandler $ajaxObject
     * @return string
     */
    public function updateConfigurationFile($ajaxParams, $ajaxObject)
    {
        $extensionKey = GeneralUtility::_GP('extensionKey');
        if (empty($extensionKey)) {
            return;
        }
        $packageManager = Bootstrap::getInstance()->getEarlyInstance(PackageManager::class);
        $packageManager->scanAvailablePackages();
        $extensionConfigurationPath = $packageManager->getPackage($extensionKey)->getPackagePath() . 'ext_emconf.php';
        $EM_CONF = null;
        if (file_exists($extensionConfigurationPath)) {
            $_EXTKEY = $extensionKey;
            include $extensionConfigurationPath;
        }

        if ($EM_CONF === null || empty($EM_CONF[$extensionKey])) {
            return;
        }

        $currentMd5HashArray = ExtensionUtility::getMd5HashArrayForExtension($extensionKey);
        $EM_CONF[$extensionKey]['_md5_values_when_last_written'] = serialize($currentMd5HashArray);

        $emConfUtility = GeneralUtility::makeInstance(EmConfUtility::class);
        $extensionData = [
            'extKey' => $extensionKey,
            'EM_CONF' => $EM_CONF[$extensionKey],
        ];
        $emConfContent = $emConfUtility->constructEmConf($extensionData);
        GeneralUtility::writeFile($extensionConfigurationPath, $emConfContent);

        $ajaxObject->setContentFormat('json');
        $ajaxObject->addContent('title', $this->translate('title'));
        $ajaxObject->addContent('message', sprintf($this->translate('message'), $extensionKey));
    }
}
