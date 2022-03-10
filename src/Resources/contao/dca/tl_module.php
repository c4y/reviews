<?php

/*
 * This file is part of c4y/reviews.
 *
 * (c) Oliver Lohoff
 *
 * @license MIT License
 */

use C4Y\Reviews\Controller\Module\BadgeModule;
use C4Y\Reviews\Controller\Module\ReviewFormModule;
use C4Y\Reviews\Controller\Module\ReviewListModule;
use C4Y\Reviews\Controller\Module\ReviewRichSnippetModule;
use C4Y\Reviews\Controller\Module\SendReviewLinkModule;

$GLOBALS['TL_DCA']['tl_module']['palettes'][BadgeModule::TYPE] = '{title_legend},name,headline,type,reviews_category,reviews_list_header_description,jumpTo,reviews_badge_logo,reviews_badge_size,reviews_list_logo,reviews_list_size,reviews_badge_numberOfReviews,customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes'][ReviewFormModule::TYPE] = '{title_legend},name,headline,type,customTpl,reviews_jumpToError,jumpTo;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes'][SendReviewLinkModule::TYPE] = '{title_legend},name,headline,type,reviews_category,customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes'][ReviewListModule::TYPE] = '{title_legend},name,headline,type,reviews_category,perPage,customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes'][ReviewRichSnippetModule::TYPE] = '{title_legend},name,type,reviews_category,customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['fields']['reviews_category'] = [
    'exclude' => true,
    'inputType' => 'select',
    'foreignKey' => 'tl_c4y_reviews_category.title',
    'eval' => ['tl_class' => 'clr'],
    'sql' => 'int(10) unsigned NOT NULL default 0',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['reviews_jumpToError'] = [
    'exclude' => true,
    'inputType' => 'pageTree',
    'eval' => ['tl_class' => 'clr'],
    'sql' => 'int(10) unsigned NOT NULL default 0',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['reviews_list_header_description'] = [
    'exclude' => true,
    'inputType' => 'text',
    'default' => '',
    'eval' => ['tl_class' => 'clr'],
    'sql' => 'varchar(255) NOT NULL default ""',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['reviews_badge_logo'] = [
    'exclude' => true,
    'inputType' => 'fileTree',
    'eval' => ['fieldType' => 'radio', 'filesOnly' => true, 'mandatory' => true, 'tl_class' => 'clr'],
    'sql' => 'binary(16) NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['reviews_badge_size'] = [
    'exclude' => true,
    'inputType' => 'imageSize',
    'reference' => &$GLOBALS['TL_LANG']['MSC'],
    'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'],
    'options_callback' => static function () {
        return Contao\System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(Contao\BackendUser::getInstance());
    },
    'sql' => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['reviews_list_logo'] = [
    'exclude' => true,
    'inputType' => 'fileTree',
    'eval' => ['fieldType' => 'radio', 'filesOnly' => true, 'mandatory' => true, 'tl_class' => 'clr'],
    'sql' => 'binary(16) NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['reviews_list_size'] = [
    'exclude' => true,
    'inputType' => 'imageSize',
    'reference' => &$GLOBALS['TL_LANG']['MSC'],
    'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'],
    'options_callback' => static function () {
        return Contao\System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(Contao\BackendUser::getInstance());
    },
    'sql' => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['reviews_badge_numberOfReviews'] = [
    'exclude' => true,
    'inputType' => 'text',
    'default' => 10,
    'eval' => ['tl_class' => 'clr', 'rgxp' => 'digit'],
    'sql' => 'int(10) unsigned NOT NULL default 10',
];
