<?php

/*
 * This file is part of c4y/reviews.
 *
 * (c) Oliver Lohoff
 *
 * @license MIT License
 */

$GLOBALS['TL_DCA']['tl_c4y_reviews_token'] = [
    'config' => [
        'dataContainer' => 'Table',
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],

    // Fields
    'fields' => [
        'id' => [
            'search' => true,
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp' => [
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'token' => [
            'sql' => "varchar(128) NOT NULL default ''",
        ],
        'expires' => [
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'category' => [
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'user' => [
            'sql' => "varchar(255) NOT NULL default ''",
        ],
    ],
];
