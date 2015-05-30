<?php
namespace SCHNITZLER\ExtbaseLanguageTests\Domain\Repository;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

/**
 * Class ItemRepository
 *
 * @package ExtbaseTeam\BlogExample\Domain\Repository
 */
class ItemRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	/**
	 * @return void
	 */
	public function initializeObject() {
		if (!$GLOBALS['TSFE'] instanceof \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController) {
			throw new \LogicException('An instantiated frontend is needed for proper query settings');
		}

		/** @var Typo3QuerySettings $querySettings */
		$querySettings = $this->objectManager->get(QuerySettingsInterface::class);
		$querySettings->setRespectStoragePage(FALSE);

		if ($querySettings instanceof Typo3QuerySettings) {
			$querySettings->useQueryCache(FALSE);
		}

		$this->setDefaultQuerySettings($querySettings);
	}
}
