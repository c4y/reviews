<?php

/*
 * This file is part of c4y/reviews.
 *
 * (c) Oliver Lohoff
 *
 * @license MIT License
 */

$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] = str_replace(
    ',combineScripts',
    ',combineScripts,addReviewsCss',
    $GLOBALS['TL_DCA']['tl_layout']['palettes']['default']
);

// Fields
$GLOBALS['TL_DCA']['tl_layout']['fields']['addReviewsCss'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr'],
    'sql' => "char(1) NOT NULL default ''",
];
