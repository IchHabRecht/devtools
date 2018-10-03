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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Shows modified files for an extension
 *
 * @author Nicole Cordes <typo3@cordes.co>
 */
class UpdateConfigurationFileController extends \IchHabRecht\Devtools\Controller\Slot\AbstractSlotController
{
    /**
     * @var ExtensionUtility
     */
    protected $extensionUtility;

    /**
     * @var string
     */
    protected $translationPrefix = 'extensionmanager.process_actions.update_configuration';

    public function __construct(ExtensionUtility $extensionUtility = null)
    {
        $this->extensionUtility = $extensionUtility ?: GeneralUtility::makeInstance(ExtensionUtility::class);
    }

    public function updateConfigurationFile(ServerRequestInterface $request, ResponseInterface $response)
    {
        $extensionKey = GeneralUtility::_GP('extensionKey');
        if (empty($extensionKey)) {
            $response = $response->withStatus(500);
            return $response;
        }

        $updated = $this->extensionUtility->updateConfiguration($extensionKey);

        if (!$updated) {
            $response = $response->withStatus(500);
            return $response;
        }

        $response->getBody()->write(json_encode(
            [
                'title' => $this->translate('title'),
                'message' => sprintf($this->translate('message.success'), $extensionKey),
            ]
        ));

        return $response;
    }
}
