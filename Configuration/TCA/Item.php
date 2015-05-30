<?php
defined('TYPO3_MODE') or die();

$TCA['tx_extbaselanguagetests_domain_model_item'] = array(
	'ctrl' => $TCA['tx_extbaselanguagetests_domain_model_item']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'title'
	),
	'columns' => array(
		'sys_language_uid' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => Array(
					Array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages',-1),
					Array('LLL:EXT:lang/locallang_general.php:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => Array (
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
				),
				'foreign_table' => 'tx_extbaselanguagetests_domain_model_item',
				'foreign_table_where' => 'AND tx_extbaselanguagetests_domain_model_item.uid=###REC_FIELD_l18n_parent### AND tx_extbaselanguagetests_domain_model_item.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array(
			'config'=>array(
				'type'=>'passthrough'
			)
		),
		't3ver_label' => Array (
			'displayCond' => 'FIELD:t3ver_label:REQ:true',
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.versionLabel',
			'config' => Array (
				'type'=>'none',
				'cols' => 27
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check'
			)
		),
		'title' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:blog_example/Resources/Private/Language/locallang_db.xml:tx_extbaselanguagetests_domain_model_item.title',
			'config' => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim,required',
				'max' => 256
			)
		),
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid, hidden, title')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);
