<?php

/*
 * This file is part of c4y/reviews.
 *
 * (c) Oliver Lohoff
 *
 * @license MIT License
 */

$GLOBALS['TL_DCA']['tl_c4y_reviews_category'] = [
    // Config
    'config' => [
        'dataContainer' => 'Table',
        'ctable' => ['tl_c4y_reviews'],
        'switchToEdit' => true,
        'enableVersioning' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],

    // List
    'list' => [
        'sorting' => [
            'mode' => 1,
            'flag' => 1,
            'fields' => ['title'],
            'panelLayout' => 'filter;search',
        ],
        'label' => [
            'fields' => ['title'],
        ],
        'operations' => [
            'edit' => [
                'href' => 'table=tl_c4y_reviews',
                'icon' => 'edit.svg',
            ],
            'editheader' => [
                'href' => 'act=edit',
                'icon' => 'header.svg',
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\''.$GLOBALS['TL_LANG']['MSC']['deleteConfirm'].'\'))return false;Backend.getScrollOffset()"',
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
        ],
    ],

    // Palettes
    'palettes' => [
        'default' => '{title_legend},title;{settings_legend},url,notification,notification_admin,apiToken',
    ],

    // Fields
    'fields' => [
        'id' => [
            'search' => true,
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid' => [
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'tstamp' => [
            'search' => true,
            'sql' => 'int(10) unsigned NOT NULL',
        ],
        'title' => [
            'exclude' => true,
            'inputType' => 'text',
            'search' => true,
            'eval' => ['mandatory' => true, 'decodeEntities' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'url' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'pageTree',
            'eval' => ['multiple' => false],
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'notification' => [
            'exclude' => true,
            'inputType' => 'select',
            'options_callback' => array("tl_c4y_reviews_category", "getNotifications"),
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'notification_admin' => [
            'exclude' => true,
            'inputType' => 'select',
            'options_callback' => array("tl_c4y_reviews_category", "getAdminNotifications"),
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'apiToken' => [
            'exclude' => true,
            'inputType' => 'text',
            'search' => true,
            'load_callback' => array(
                array("tl_c4y_reviews_category", "getUuid")
            ),
            'eval' => ['mandatory' => false, 'decodeEntities' => true, 'maxlength' => 255, 'tl_class' => 'clr'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
    ],
];

class tl_c4y_reviews_category extends Contao\Backend
{
    /**
     * Import the back end user object.
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('Contao\BackendUser', 'User');
    }

    public function getNotifications()
    {
        $arrNotifications = array();
        $objNotifications = $this->Database->execute("SELECT id, title, type FROM tl_nc_notification WHERE type='reviews_link' ORDER BY title");

        while ($objNotifications->next())
        {
            $arrNotifications[$objNotifications->id] = $objNotifications->title;
        }

        return $arrNotifications;
    }

    public function getAdminNotifications()
    {
        $arrNotifications = array();
        $objNotifications = $this->Database->execute("SELECT id, title, type FROM tl_nc_notification WHERE type='reviews_admin' ORDER BY title");

        while ($objNotifications->next())
        {
            $arrNotifications[$objNotifications->id] = $objNotifications->title;
        }

        return $arrNotifications;
    }

    public function getUuid($varValue, $dc)
    {
        if(empty($varValue)) {
            $varValue = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );
        }

        return $varValue;
    }
}
