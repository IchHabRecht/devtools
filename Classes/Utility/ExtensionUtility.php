<?php
namespace IchHabRecht\Devtools\Utility;

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

use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extensionmanager\Utility\EmConfUtility;

/**
 * Functions for extension management
 *
 * @author Nicole Cordes <typo3@cordes.co>
 */
final class ExtensionUtility
{
    /**
     * @var PackageManager
     */
    private $packageManager;

    public function __construct(PackageManager $packageManager = null)
    {
        $this->packageManager = $packageManager ?: GeneralUtility::makeInstance(PackageManager::class);
    }

    /**
     * @param string $extensionKey
     * @return array
     */
    public function getMd5HashArrayForExtension($extensionKey)
    {
        $md5HashArray = [];
        $extensionPath = $this->packageManager->getPackage($extensionKey)->getPackagePath();
        $filesArray = GeneralUtility::getAllFilesAndFoldersInPath(
            [],
            $extensionPath,
            '',
            false,
            99,
            $GLOBALS['TYPO3_CONF_VARS']['EXT']['excludeForPackaging']
        );
        foreach ($filesArray as $file) {
            $relativeFileName = substr($file, strlen($extensionPath));
            if ($relativeFileName !== 'ext_emconf.php') {
                $fileContent = GeneralUtility::getUrl($file);
                $md5HashArray[$relativeFileName] = substr(md5($fileContent), 0, 4);
            }
        }

        return $md5HashArray;
    }

    /**
     * @param string $extensionKey
     * @return bool
     */
    public function updateConfiguration($extensionKey)
    {
        $this->packageManager->scanAvailablePackages();
        $extensionConfigurationPath = $this->packageManager->getPackage($extensionKey)->getPackagePath() . 'ext_emconf.php';
        $EM_CONF = null;
        if (file_exists($extensionConfigurationPath)) {
            $_EXTKEY = $extensionKey;
            include $extensionConfigurationPath;
        }

        if ($EM_CONF === null || empty($EM_CONF[$extensionKey])) {
            return false;
        }

        $currentMd5HashArray = $this->getMd5HashArrayForExtension($extensionKey);
        $EM_CONF[$extensionKey]['_md5_values_when_last_written'] = serialize($currentMd5HashArray);

        $emConfUtility = GeneralUtility::makeInstance(EmConfUtility::class);
        $extensionData = [
            'extKey' => $extensionKey,
            'EM_CONF' => $EM_CONF[$extensionKey],
        ];
        $emConfContent = $emConfUtility->constructEmConf($extensionData);
        GeneralUtility::writeFile($extensionConfigurationPath, $emConfContent);

        return true;
    }
}
