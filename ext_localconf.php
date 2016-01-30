<?php

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

// Hook for image upload
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_extfilefunc.php']['processData']['sg_pngquant'] = \SwordGroup\Pngquant\Hooks\FileUploadHook::class;

/** @var $signalSlotDispatcher \TYPO3\CMS\Extbase\SignalSlot\Dispatcher */
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);

// Slot connection for processed image generation
$signalSlotDispatcher->connect(
	\TYPO3\CMS\Core\Resource\ResourceStorage::class,
	\TYPO3\CMS\Core\Resource\Service\FileProcessingService::SIGNAL_PostFileProcess,
	\SwordGroup\Pngquant\Slots\PngProcessing::class,
	'postProcessFile'
);

// Command line
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = \SwordGroup\Pngquant\Command\PngquantCommandController::class;
