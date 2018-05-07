<?php
namespace IchHabRecht\Devtools\Command;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018 Sven Friese <sven@widerheim.de>, familie redlich digital GmbH
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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

class ExtensionConfigurationCommandController extends CommandController
{
    protected $languageFile = 'LLL:EXT:devtools/Resources/Private/Language/locallang.xlf';

    /**
     * @param string $extensionKey
     * @return string
     */
    public function updateCommand($extensionKey)
    {
        $extensionUtility = GeneralUtility::makeInstance(ExtensionUtility::class);
        $updated = $extensionUtility->updateConfiguration($extensionKey);

        $translationKey = 'slot.extensionmanager.process_actions.update_configuration.message';
        if (!$updated) {
            $translationKey = 'slot.error.message';
        }

        $output = sprintf($GLOBALS['LANG']->sL($this->languageFile . ':' . $translationKey . ''), $extensionKey);

        return $output;
    }
}
