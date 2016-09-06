<?php

namespace SwordGroup\Pngquant\Service;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Resource\File;

/**
 * Pngquant service.
 *
 * @author Fabrice Morin <fmo@sword.eu>
 *
 */
class PngquantService implements SingletonInterface {

	/**
	 * @var string
	 */
	const EXTENSION_PNG = 'png';

	/**
	 * Pngquant command line template.
	 * @var string
	 */
	const COMMAND = '@EXECUTABLE@ @NOFS@ @OUTPUT@ @SPEED@ @QUALITY@ @IEBUG@ @INPUT@ --force';

	/**
	 * @var \TYPO3\CMS\Core\Log\Logger
	 */
	protected $logger = NULL;

	/**
	 * Extension configuration.
	 * @var array
	 */
	protected $confArray = array();

	/**
	 * @var \TYPO3\CMS\Core\Resource\StorageRepository
	 */
	protected $storageRepository = NULL;

	/**
	 * Initializes logger and retrieve extension configuration.
	 */
	public function __construct() {
		$this->logger = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Log\LogManager::class)->getLogger(__CLASS__);
		$this->confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['pngquant']);
	}

	/**
	 * Convert all PNG images (except processed) from specified storage.
	 *
	 * @param int $storageUid
	 * @return boolean
	 */
	public function convertStorage($storageUid) {
		$this->storageRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Resource\StorageRepository::class);
		$storage = $this->storageRepository->findByUid($storageUid);
		if($storage) {
			// Convert all storage files
			$files = $storage->getFilesInFolder($storage->getRootLevelFolder(FALSE), 0, 0, TRUE, TRUE);
			foreach($files as $file) {
				if($file instanceof File) {
					$this->convertPngImage($file);
				}
				unset($file);
			}
		} else {
			$this->logger->error('No storage found', array('storage' => $storageUid));
			return FALSE;
		}
	}

	/**
	 * Convert PNG image using pngquant command.
	 *
	 * @param AbstractFile $file
	 */
	public function convertPngImage(AbstractFile $file) {
		try {
			if(self::EXTENSION_PNG !== $file->getExtension()) {
				return;
			}

			// Ignore processed file which uses original file
			if($file instanceof ProcessedFile && $file->usesOriginalFile()) {
				$this->logger->debug('Do not convert processed file identical with its original file', array('file' => $inputFilePath));
				return;
			}

			// Set input/output files for pngquant command
			// Input file is the the specified file we want to quantize
			// Output file is a temporary file in typo3temp directory
			$inputFilePath = PATH_site . $file->getPublicUrl();
			$outputFilePath = GeneralUtility::tempnam('sg_pngquant_', '.' . self::EXTENSION_PNG);

			// Build command line
			$cmd = $this->buildCommand($inputFilePath, $outputFilePath);

			$result = CommandUtility::exec($cmd, $output, $returnValue);
			if(0 === $returnValue) {
				// Replace content
				if($file instanceof ProcessedFile) {
					// For processed file, only convert real processed file (i.e. different than their original file)
					// Temporary file is automatically removed when updating a processed file
					$this->logger->debug('Update processed file', array('cmd' => $cmd));
					$file->updateWithLocalFile($outputFilePath);
				} elseif(! $this->confArray['keepOriginal']) {
					// Convert original files according to extension configuration
					// After conversion the temporary file is removed
					$this->logger->debug('Update original file', array('cmd' => $cmd));
					$contents = @file_get_contents($outputFilePath);
					$file->setContents($contents);
				}
			} else {
				$this->logger->error('Convert image', array('cmd' => $cmd, 'result' => $result, 'output' => $output, 'returnValue' => $returnValue));
			}
		} catch(\RuntimeException $e) {
			$this->logger->error($e->getMessage());
		}

		// Remove temporary file, if exists
		if(file_exists($outputFilePath)) {
			$this->removeTemporaryFile($outputFilePath);
		}
	}

	/**
	 * Remove temporary file.
	 *
	 * @param string $filePath
	 */
	protected function removeTemporaryFile($filePath) {
		if(!GeneralUtility::unlink_tempfile($filePath)) {
			$this->logger->error('Failed to remove file', array('filepath' => $filePath));
		}
	}

	/**
	 * Build pngquant command.
	 *
	 * @param string $inputFilePath
	 * @param string $outputFilePath
	 * @return string
	 */
	protected function buildCommand($inputFilePath, $outputFilePath) {
		// Build parameters using configuration
		$executable = $this->confArray['executable'];
		$nofs = $this->confArray['nofs'] ? '--nofs' : '';
		$speed = '--speed ' . $this->confArray['speed'];
		$quality = $this->confArray['quality'] ? '--quality ' . $this->confArray['quality'] : '';
		$iebug = $this->confArray['iebug'] ? '--iebug' : '';
		$output = '--output=' . $outputFilePath;

		// Build command
		$cmd = str_replace(
			array('@EXECUTABLE@', '@NOFS@', '@OUTPUT@', '@SPEED@', '@QUALITY@', '@IEBUG@', '@INPUT@'),
			array($executable, $nofs, $output, $speed, $quality, $iebug, $inputFilePath),
			self::COMMAND);

		return $cmd;
	}
}
