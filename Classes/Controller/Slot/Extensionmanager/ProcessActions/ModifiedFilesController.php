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

/**
 * Shows modified files for an extension
 *
 * @author Nicole Cordes <typo3@cordes.co>
 * @package TYPO3
 * @subpackage tx_devtools
 */
class ModifiedFilesController extends \IchHabRecht\Devtools\Controller\Slot\AbstractSlotController {

	/**
	 * @var string
	 */
	protected $translationPrefix = 'extensionmanager.process_actions.modified_files';

	/**
	 * @param array $ajaxParams
	 * @param \TYPO3\CMS\Core\Http\AjaxRequestHandler $ajaxObject
	 * @return string
	 */
	public function listFiles($ajaxParams, $ajaxObject) {
		$extensionKey = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('extensionKey');
		if (!empty($extensionKey)) {
			$packageManager = \TYPO3\CMS\Core\Core\Bootstrap::getInstance()->getEarlyInstance(('TYPO3\\Flow\\Package\\PackageManager'));
			$extensionConfigurationPath = $packageManager->getPackage($extensionKey)->getPackagePath() . 'ext_emconf.php';
			$_EXTKEY = $extensionKey;
			$EM_CONF = NULL;
			$extension = NULL;
			if (file_exists($extensionConfigurationPath)) {
				include $extensionConfigurationPath;
				if (is_array($EM_CONF[$_EXTKEY])) {
					$extension = $EM_CONF[$_EXTKEY];
				}
			}

			if (!empty($extension['_md5_values_when_last_written'])) {
				$originalMd5HashArray = (array) unserialize($extension['_md5_values_when_last_written']);
				$originalFileArray = array_keys($originalMd5HashArray);
				$currentMd5HashArray = \IchHabRecht\Devtools\Utility\ExtensionUtility::getMd5HashArrayForExtension($extensionKey);
				$currentFileArray = array_keys($currentMd5HashArray);

				$filterFunction = function($value) {
					return !empty($value);
				};
				$removedFiles = array_diff($originalFileArray, $currentFileArray);
				$removedFiles = array_filter($removedFiles, $filterFunction);
				$newFiles = array_diff($currentFileArray, $originalFileArray);
				$newFiles = array_filter($newFiles, $filterFunction);
				$changedFiles = array_diff($originalMd5HashArray, $currentMd5HashArray);
				$changedFiles = array_filter($changedFiles, $filterFunction);

				$messageArray = array();
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
	}

}

?>