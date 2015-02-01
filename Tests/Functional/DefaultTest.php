<?php
namespace SCHNITZLER\ExtbaseLanguageTests\Tests\Functional;

require_once 'AbstractTestCase.php';

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
class LanguageOverlayTest extends AbstractTestCase {

	/**
	 * @var \SCHNITZLER\ExtbaseLanguageTests\Domain\Repository\ItemRepository
	 */
	protected $itemRepository;

	/**
	 * @test
	 */
	public function LanguageOverlayDisabledTest() {

		/**
		 * sys_language_overlay set to FALSE will disable overlaying of records completely,
		 * therefore translated records should be fetched directly given by the value of
		 * sys_language_uid.
		 *
		 * This mode does fetch:
		 * - All translated records with the language uid defined by sys_language_uid
		 *
		 * This mode does not fetch:
		 * - Default language records.
		 */

		/**
		 * Test Setup
		 */
		$this->addTypoScriptToLoad('EXT:extbase_language_tests/Configuration/TypoScript/LanguageUid/1.ts');
		$this->addTypoScriptToLoad('EXT:extbase_language_tests/Configuration/TypoScript/LanguageOverlay/Disabled.ts');
		$this->setUpFrontend();
		$this->itemRepository = $this->objectManager->get(\SCHNITZLER\ExtbaseLanguageTests\Domain\Repository\ItemRepository::class);

		$queryResult = $this->itemRepository->findAll();
		$query = $queryResult->getQuery();

		/*********************************************************************************************************************************/

		$defaultLangaugeRecordCount = 0;
		foreach ($queryResult as $item) {
			/** @var \SCHNITZLER\ExtbaseLanguageTests\Domain\Model\Item $item */

			$languageUid = (int) $item->_getProperty('_languageUid');

			if ($languageUid !== 1) {
				$defaultLangaugeRecordCount++;
			}
		}

		/**
		 * Check that language settings affect the query settings
		 */
		$this->assertEquals(1, $query->getQuerySettings()->getLanguageUid());
		$this->assertEquals(FALSE, $query->getQuerySettings()->getLanguageOverlayMode());

		/**
		 * There should be 2 default language records, whereas only one is translated
		 * and therefore overlays one of the default records.
		 */
		$this->assertCount(2, $queryResult);

		/**
		 * There shouldn't be any records with a language_uid other than 1
		 */
		$this->assertSame(0, $defaultLangaugeRecordCount);
	}

	/**
	 * @test
	 */
	public function LanguageOverlayEnabledTest() {

		/**
		 * sys_language_overlay set to TRUE causes sys_language_uid to not have an actual
		 * effect but instead treats it like it's set to 0.
		 *
		 * As a result, the persistence should fetch all records of the default language
		 * and overlay configured fields with the language set by sys_language_uid.
		 *
		 * This mode does fetch:
		 * - All default language records
		 * - All translations of translated records as overlay
		 *
		 * This mode does not fetch:
		 * - Records that only exist in a non default (> 0) language.
		 */

		/**
		 * Test Setup
		 */
		$this->addTypoScriptToLoad('EXT:extbase_language_tests/Configuration/TypoScript/LanguageUid/1.ts');
		$this->addTypoScriptToLoad('EXT:extbase_language_tests/Configuration/TypoScript/LanguageOverlay/Enabled.ts');
		$this->setUpFrontend();
		$this->itemRepository = $this->objectManager->get(\SCHNITZLER\ExtbaseLanguageTests\Domain\Repository\ItemRepository::class);

		$queryResult = $this->itemRepository->findAll();
		$query = $queryResult->getQuery();

		/*********************************************************************************************************************************/

		$foreignLangaugeRecordCount = 0;
		foreach ($queryResult as $item) {
			/** @var \SCHNITZLER\ExtbaseLanguageTests\Domain\Model\Item $item */

			$languageUid = (int) $item->_getProperty('_languageUid');

			if ($languageUid > 0) {
				$foreignLangaugeRecordCount++;
			}
		}

		/**
		 * Check that language settings affect the query settings
		 */
		$this->assertEquals(1, $query->getQuerySettings()->getLanguageUid());
		$this->assertEquals(TRUE, $query->getQuerySettings()->getLanguageOverlayMode());

		/**
		 * There should be 2 default language records, whereas only one is translated
		 * and therefore overlays one of the default records.
		 */
		$this->assertCount(2, $queryResult);

		/**
		 * There shouldn't be any records with a language_uid other than 0
		 */
		$this->assertSame(0, $foreignLangaugeRecordCount);
	}

	/**
	 * @test
	 */
	public function LanguageOverlaySetToHideNonTranslatedTest() {

		/**
		 * sys_language_overlay set to hideNonTranslated pretty much acts like it's set
		 * to true but it does no show records that cannot be overlayed, i.e. not
		 * translated records
		 *
		 * This mode does fetch:
		 * - All default language records
		 * - All translations of translated records as overlay
		 *
		 * This mode does not fetch:
		 * - Records that only exist in a non default (> 0) language.
		 * - Records that cannot be overlayed due to a missing translation
		 */

		/**
		 * Test Setup
		 */
		$this->addTypoScriptToLoad('EXT:extbase_language_tests/Configuration/TypoScript/LanguageUid/1.ts');
		$this->addTypoScriptToLoad('EXT:extbase_language_tests/Configuration/TypoScript/LanguageOverlay/HideNonTranslated.ts');
		$this->setUpFrontend();
		$this->itemRepository = $this->objectManager->get(\SCHNITZLER\ExtbaseLanguageTests\Domain\Repository\ItemRepository::class);

		$queryResult = $this->itemRepository->findAll();
		$query = $queryResult->getQuery();

		/*********************************************************************************************************************************/

		$foreignLangaugeRecordCount = 0;
		foreach ($queryResult as $item) {
			/** @var \SCHNITZLER\ExtbaseLanguageTests\Domain\Model\Item $item */

			$languageUid = (int) $item->_getProperty('_languageUid');

			if ($languageUid > 0) {
				$foreignLangaugeRecordCount++;
			}
		}

		/**
		 * Check that language settings affect the query settings
		 */
		$this->assertEquals(1, $query->getQuerySettings()->getLanguageUid());
		$this->assertEquals('hideNonTranslated', $query->getQuerySettings()->getLanguageOverlayMode());

		/**
		 * There should be 2 default language records but as only one of them is
		 * translated just one (overlayed) record should actually be fetched
		 */
		$this->assertCount(1, $queryResult);

		/**
		 * There shouldn't be any records with a language_uid other than 0
		 */
		$this->assertSame(0, $foreignLangaugeRecordCount);
	}
}
