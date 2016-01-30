<?php

namespace SwordGroup\Pngquant\Slots;

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
use TYPO3\CMS\Core\Resource\Service\FileProcessingService;
use TYPO3\CMS\Core\Resource\Driver\DriverInterface;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use SwordGroup\Pngquant\Service\PngquantService;

/**
 * PNG post processing for processed images.
 *
 * @author Fabrice Morin <fmo@sword.eu>
 *
 */
class PngProcessing implements SingletonInterface {

	/**
	 * @var \SwordGroup\Pngquant\Service\PngquantService
	 */
	protected $pngquantService;

	/**
	 *
	 */
	public function __construct() {
		$this->pngquantService = GeneralUtility::makeInstance(\SwordGroup\Pngquant\Service\PngquantService::class);
	}

	/**
	 * Called from FileProcessingService signal dispatch, after image process.
	 *
	 * @param FileProcessingService $fileProcessingService
	 * @param DriverInterface $driver
	 * @param ProcessedFile $processedFile
	 * @param FileInterface $file
	 * @param string $context
	 * @param array $configuration
	 */
	public function postProcessFile(FileProcessingService $fileProcessingService, DriverInterface $driver, ProcessedFile $processedFile, FileInterface $file, $context, array $configuration) {
		if($processedFile->exists() && !is_null($processedFile->getIdentifier()) && $processedFile->isUpdated()) {
			$this->pngquantService->convertPngImage($processedFile);
		}
	}
}
