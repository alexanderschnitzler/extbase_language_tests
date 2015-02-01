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
class LanguageModeTest extends AbstractTestCase {

	/**
	 * @var \SCHNITZLER\ExtbaseLanguageTests\Domain\Repository\ItemRepository
	 */
	protected $itemRepository;

	public function setUp() {
		parent::setUp();

		/**
		 * content_fallback takes action whenever content elements of the
		 * desired languages are mising. It then uses the given fallback
		 * chain to determine the right fallback language and resolves all
		 * content element with this language.
		 *
		 * Important: The detailed functionality is then dependent on the
		 * sys_language_overlay setting.
		 *
		 * Example:
		 *   Given languages:
		 *     0 - default (english)
		 *     1 - german
		 *     2 - espanol
		 *
		 *   CE 1:
		 *     - Default only
		 *
		 *   CE 2:
		 *     - Default and german translation
		 *
		 *   CE 3:
		 *     - German only
		 *
		 *   Story:
		 *     A website is requestes in spanish but there are not records
		 *     in this language available. The fallback chain falls back to
		 *     german, then default (english).
		 *
		 *     With sys_language_overlay disabled, typo3 fetches all german
		 *     elements directly and displays them. In this case content
		 *     element sthat only exist in german, are shown as well.
		 *
		 *     With sys_language_overlay enabled, typo3 sets the actual
		 *     language to default and tries to overlay all elements that
		 *     have a german translation. In this case content elements
		 *     that only exist in german, aren't shown.
		 *
		 *     Actually the sys_language_overlay mode simply defines the
		 *     direction in which fallback elements are resolved. If disabled
		 *     TYPO3 tries to fetch as many elements as possible that exist in
		 *     the first fallback language as possible.
		 *
		 *     If enabled, TYPO3 fetches all default records and tries to
		 *     overlay as much as possible.
		 *
		 * This setting is really hard to handle because it is highly
		 * dependent on a lot of variables. This scenario only works if
		 * there are these three languages, language 2 is requested and
		 * there is no page overlay for this language. As soon as a
		 * page overlay exists the behaviour is as follows
		 *
		 * - With sys_language_overlay disabled typo3 searches only for
		 *   content elements that exist in the the requested language.
		 *   This can be translated records or records that exclusively
		 *   exist in this language. If no records are found, the test
		 *   page is blank for sure.
		 *
		 * - With sys_language_overlay enabled typo3 again fetches all
		 *   default records and tries to overlay them as close as
		 *   possible. In this setting only translated records are shown.
		 *   Also the fallback chain will be followed, so it may be, that
		 *   the content is a mixture of 3 languages.
		 */
	}

	/**
	 * @test
	 */
	public function ContentFallbackWithOverlayDisabledTest() {

		/**
		 * Test Setup
		 */
		$this->addTypoScriptToLoad('EXT:extbase_language_tests/Configuration/TypoScript/LanguageUid/2.ts');
		$this->addTypoScriptToLoad('EXT:extbase_language_tests/Configuration/TypoScript/LanguageOverlay/Disabled.ts');
		$this->addTypoScriptToLoad('EXT:extbase_language_tests/Configuration/TypoScript/LanguageMode/ContentFallback.ts');
		$this->setUpFrontend();
		$this->itemRepository = $this->objectManager->get(\SCHNITZLER\ExtbaseLanguageTests\Domain\Repository\ItemRepository::class);

		$queryResult = $this->itemRepository->findAll();
		$query = $queryResult->getQuery();

		/*********************************************************************************************************************************/

		/**
		 * There should be 0 records because overlaying is disabled and
		 * no records with sys_language_uid exist.
		 */
		$this->assertCount(0, $queryResult);

		/**
		 * Check that language settings affect the query settings
		 */
		$this->assertEquals(FALSE, $query->getQuerySettings()->getLanguageOverlayMode());
		$this->assertEquals(2, $query->getQuerySettings()->getLanguageUid());
	}

	/**
	 * @test
	 */
	public function ContentFallbackWithOverlayEnabledTest() {

		/**
		 * Test Setup
		 */
		$this->addTypoScriptToLoad('EXT:extbase_language_tests/Configuration/TypoScript/LanguageUid/2.ts');
		$this->addTypoScriptToLoad('EXT:extbase_language_tests/Configuration/TypoScript/LanguageOverlay/Enabled.ts');
		$this->addTypoScriptToLoad('EXT:extbase_language_tests/Configuration/TypoScript/LanguageMode/ContentFallback.ts');
		$this->setUpFrontend();
		$this->itemRepository = $this->objectManager->get(\SCHNITZLER\ExtbaseLanguageTests\Domain\Repository\ItemRepository::class);

		$queryResult = $this->itemRepository->findAll();
		$query = $queryResult->getQuery();

		/*********************************************************************************************************************************/

		/**
		 * Check that language settings affect the query settings
		 */
		$this->assertEquals(TRUE, $query->getQuerySettings()->getLanguageOverlayMode());
		$this->assertEquals(2, $query->getQuerySettings()->getLanguageUid());

		/**
		 * There should be 2 records because overlaying is enabled and
		 * no records with language id 2 exist but there are 2 default
		 * language records, which are partly overlayed to languge 1.
		 */
		$this->assertCount(2, $queryResult);
	}

	/**
	 * @test
	 */
	public function ContentFallbackWithOverlaySetToHideNonTranslatedTest() {

		/**
		 * Test Setup
		 */
		$this->addTypoScriptToLoad('EXT:extbase_language_tests/Configuration/TypoScript/LanguageUid/2.ts');
		$this->addTypoScriptToLoad('EXT:extbase_language_tests/Configuration/TypoScript/LanguageOverlay/HideNonTranslated.ts');
		$this->addTypoScriptToLoad('EXT:extbase_language_tests/Configuration/TypoScript/LanguageMode/ContentFallback.ts');
		$this->setUpFrontend();
		$this->itemRepository = $this->objectManager->get(\SCHNITZLER\ExtbaseLanguageTests\Domain\Repository\ItemRepository::class);

		$queryResult = $this->itemRepository->findAll();
		$query = $queryResult->getQuery();

		/*********************************************************************************************************************************/

		/**
		 * Check that language settings affect the query settings
		 */
		$this->assertEquals('hideNonTranslated', $query->getQuerySettings()->getLanguageOverlayMode());
		$this->assertEquals(2, $query->getQuerySettings()->getLanguageUid());

		/**
		 * There should be 0 records because overlaying is enabled and
		 * there is a page overlay for language 2 but no records with a
		 * translation of language 2 exist.
		 */
		$this->assertCount(0, $queryResult);
	}
}
