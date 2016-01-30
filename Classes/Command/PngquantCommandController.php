<?php
namespace SwordGroup\Pngquant\Command;

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

use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * Pngquant Command Controller.
 *
 */
class PngquantCommandController extends CommandController {

	/**
	 * @var \SwordGroup\Pngquant\Service\PngquantService
	 * @inject
	 */
	protected $pngquantService;

	/**
	 * Convert PNG files from specified storage
	 * @param int $storageUid
	 */
	public function convertCommand($storageUid) {
		$this->pngquantService->convertStorage($storageUid);
	}
}
