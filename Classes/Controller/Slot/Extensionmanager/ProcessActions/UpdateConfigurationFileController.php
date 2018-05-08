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
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
     * @var ExtensionUtility
     */
    protected $extensionUtility;

    public function __construct(ExtensionUtility $extensionUtility = null)
    {
        $this->extensionUtility = $extensionUtility ?: GeneralUtility::makeInstance(ExtensionUtility::class);
    }

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

        $updated = $this->extensionUtility->updateConfiguration($extensionKey);

        if (!$updated) {
            return;
        }

        $ajaxObject->setContentFormat('json');
        $ajaxObject->addContent('title', $this->translate('title'));
        $ajaxObject->addContent('message', sprintf($this->translate('message.success'), $extensionKey));
    }
}
