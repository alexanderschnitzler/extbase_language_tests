<?php
namespace SCHNITZLER\ExtbaseLanguageTests\Tests\Functional;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

abstract class AbstractTestCase extends \TYPO3\CMS\Core\Tests\FunctionalTestCase {

	/**
	 * @var int
	 */
	protected $page;

	/**
	 * @var array
	 */
	protected $coreExtensionsToLoad = array('extbase', 'fluid');

	/**
	 * @var array
	 */
	protected $testExtensionsToLoad = array(
		'typo3conf/ext/extbase_language_tests'
	);

	/**
	 * @var array
	 */
	protected $typoScriptToLoad = array(
		'EXT:extbase_language_tests/Configuration/TypoScript/Page.ts',
		'EXT:extbase_language_tests/Configuration/TypoScript/LanguageUid/0.ts'
	);

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
	 */
	protected $persistentManager;

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface The object manager
	 */
	protected $objectManager;

	/**
	 * @param string $typoScript
	 */
	public function addTypoScriptToLoad($typoScript) {
		$this->typoScriptToLoad[] = $typoScript;
	}

	/**
	 * Sets up this test suite.
	 */
	public function setUp() {
		parent::setUp();

		$this->page = 1;

		$this->importDataSet(ORIGINAL_ROOT . 'typo3conf/ext/extbase_language_tests/Tests/Functional/Fixtures/pages.xml');
		$this->importDataSet(ORIGINAL_ROOT . 'typo3conf/ext/extbase_language_tests/Tests/Functional/Fixtures/pages_language_overlay.xml');
		$this->importDataSet(ORIGINAL_ROOT . 'typo3conf/ext/extbase_language_tests/Tests/Functional/Fixtures/sys_language.xml');
		$this->importDataSet(ORIGINAL_ROOT . 'typo3conf/ext/extbase_language_tests/Tests/Functional/Fixtures/tx_extbaselanguagetests_domain_model_item.xml');

		$this->objectManager = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->persistentManager = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
	}

	/**
	 * @throws \TYPO3\CMS\Core\Error\Http\ServiceUnavailableException
	 */
	protected function setUpFrontend() {
		$this->setUpFrontendRootPage($this->page, $this->typoScriptToLoad);

		$GLOBALS['TT'] = new \TYPO3\CMS\Core\TimeTracker\NullTimeTracker();
		$GLOBALS['TSFE'] = new TypoScriptFrontendController($GLOBALS['TYPO3_CONF_VARS'], $this->page, 0, 1);
		$GLOBALS['TSFE']->connectToDB();
		$GLOBALS['TSFE']->initFEuser();
		$GLOBALS['TSFE']->checkAlternativeIdMethods();
		$GLOBALS['TSFE']->clear_preview();
		$GLOBALS['TSFE']->determineId();
		$GLOBALS['TSFE']->initTemplate();
		$GLOBALS['TSFE']->getFromCache();
		$GLOBALS['TSFE']->getConfigArray();
		$GLOBALS['TSFE']->settingLanguage();
		$GLOBALS['TSFE']->settingLocale();
	}
}
