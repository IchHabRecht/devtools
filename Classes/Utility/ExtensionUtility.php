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

/**
 * Functions for extension management
 *
 * @author Nicole Cordes <typo3@cordes.co>
 * @package TYPO3
 * @subpackage tx_devtools
 */
final class ExtensionUtility {

	/**
	 * @param string $extensionKey
	 * @return array
	 */
	static public function getMd5HashArrayForExtension($extensionKey) {
		$md5HashArray = array();
		/** @var \TYPO3\CMS\Core\Package\PackageManager $packageManager */
		$packageManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Package\\PackageManager');
		$extensionPath = $packageManager->getPackage($extensionKey)->getPackagePath();
		$filesArray = \TYPO3\CMS\Core\Utility\GeneralUtility::getAllFilesAndFoldersInPath(
			array(),
			$extensionPath,
			'',
			FALSE,
			99,
			$GLOBALS['TYPO3_CONF_VARS']['EXT']['excludeForPackaging']
		);
		foreach ($filesArray as $file) {
			$relativeFileName = substr($file, strlen($extensionPath));
			if ($relativeFileName !== 'ext_emconf.php') {
				$fileContent = \TYPO3\CMS\Core\Utility\GeneralUtility::getUrl($file);
				$md5HashArray[$relativeFileName] = substr(md5($fileContent), 0, 4);
			}
		}

		return $md5HashArray;
	}

}

?>