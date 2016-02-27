<?php

namespace SwordGroup\Pngquant\Hooks;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Fabrice Morin <fmo@sword.eu>
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

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\File\ExtendedFileUtilityProcessDataHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\File\ExtendedFileUtility;
use SwordGroup\Pngquant\Service\PngquantService;

/**
 * File upload hook.
 *
 * @author Fabrice Morin <fmo@sword.eu>
 *
 */
class FileUploadHook implements ExtendedFileUtilityProcessDataHookInterface, SingletonInterface {

	/**
	 * @var \SwordGroup\Pngquant\Service\PngquantService
	 */
	protected $pngquantService;

	/**
	 * @var array
	 */
	protected $confArray = array();

	/**
	 *
	 */
	public function __construct() {
		$this->pngquantService = GeneralUtility::makeInstance(\SwordGroup\Pngquant\Service\PngquantService::class);
		$this->confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['pngquant']);
	}

	/**
	 * Hook for uploaded files.
	 *
	 * {@inheritDoc}
	 * @see \TYPO3\CMS\Core\Utility\File\ExtendedFileUtilityProcessDataHookInterface::processData_postProcessAction()
	 */
	public function processData_postProcessAction($action, array $cmdArr, array $result, ExtendedFileUtility $parentObject) {
		if(! $this->confArray['keepOriginal']) {
			$files = array_pop($result);
			if(is_array($files)) {
				foreach($files as $file) {
					/** @var $file File */
					$this->pngquantService->convertPngImage($file);
				}
			}
		}
	}

}
