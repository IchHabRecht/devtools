<?php
namespace IchHabRecht\Devtools\Controller\Slot;

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
abstract class AbstractSlotController {

	/**
	 * @var string
	 */
	const LANGUAGE_FILE = 'LLL:EXT:devtools/Resources/Private/Language/locallang.xlf';

	/**
	 * @var string
	 */
	protected $translationPrefix = '';

	/**
	 * @param string $key
	 * @return string
	 */
	protected function translate($key) {
		return $GLOBALS['LANG']->sL(static::LANGUAGE_FILE .
			':slot.' . $this->translationPrefix . '.' . $key);
	}

}

?>