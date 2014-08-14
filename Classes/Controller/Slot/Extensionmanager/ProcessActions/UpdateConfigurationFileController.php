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
class UpdateConfigurationFileController extends \IchHabRecht\Devtools\Controller\Slot\AbstractSlotController {

	/**
	 * @var string
	 */
	protected $translationPrefix = 'extensionmanager.process_actions.update_configuration';

	/**
	 * @param array $ajaxParams
	 * @param \TYPO3\CMS\Core\Http\AjaxRequestHandler $ajaxObject
	 * @return string
	 */
	public function updateConfigurationFile($ajaxParams, $ajaxObject) {
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

			if ($EM_CONF !== NULL) {
				$currentMd5HashArray = \IchHabRecht\Devtools\Utility\ExtensionUtility::getMd5HashArrayForExtension($extensionKey);
				$EM_CONF[$extensionKey]['_md5_values_when_last_written'] = serialize($currentMd5HashArray);

				/** @var \TYPO3\CMS\Extensionmanager\Utility\EmConfUtility $emConfUtility */
				$emConfUtility = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\TYPO3\CMS\Extensionmanager\Utility\EmConfUtility');
				$extensionData = array(
					'extKey' => $extensionKey,
					'EM_CONF' => $EM_CONF[$extensionKey],
				);
				$emConfContent = $emConfUtility->constructEmConf($extensionData);
				\TYPO3\CMS\Core\Utility\GeneralUtility::writeFile($extensionConfigurationPath, $emConfContent);
			}

			$ajaxObject->setContentFormat('json');
			$ajaxObject->addContent('title', $this->translate('title'));
			$ajaxObject->addContent('message', sprintf($this->translate('message'), $extensionKey));
		}
	}

}

?>