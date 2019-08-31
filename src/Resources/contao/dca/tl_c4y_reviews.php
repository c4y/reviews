<?php

/*
 * This file is part of c4y/reviews.
 *
 * (c) Oliver Lohoff
 *
 * @license MIT License
 */

$GLOBALS['TL_DCA']['tl_c4y_reviews'] = [
    // Config
    'config' => [
        'dataContainer' => 'Table',
        'ptable' => 'tl_c4y_reviews_category',
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
            'flag' => 6,
            'fields' => ['review_date'],
            'panelLayout' => 'filter;search',
        ],
        'label' => [
            'fields' => ['user', 'rating', 'review'],
            'format' => '%s, (Rating: %s), Bewertung: %s',
        ],
        'operations' => [
            'edit' => [
                'href' => 'act=edit',
                'icon' => 'edit.svg',
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\''.$GLOBALS['TL_LANG']['MSC']['deleteConfirm'].'\'))return false;Backend.getScrollOffset()"',
            ],
            'toggle' => [
                'icon' => 'invisible.svg',
                'attributes' => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => ['tl_c4y_reviews', 'toggleIcon'],
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
        ],
    ],

    // Palettes
    'palettes' => [
        'default' => '{review_legend},user,rating,review,review_date,comment',
    ],

    // Fields
    'fields' => [
        'id' => [
            'search' => true,
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid' => [
            'search' => true,
            'sql' => 'int(10) unsigned NOT NULL',
        ],
        'tstamp' => [
            'search' => true,
            'sql' => 'int(10) unsigned NOT NULL',
        ],
        'user' => [
            'exclude' => true,
            'inputType' => 'textarea',
            'search' => true,
            'eval' => ['mandatory' => true, 'decodeEntities' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'rating' => [
            'exclude' => true,
            'inputType' => 'text',
            'search' => true,
            'eval' => ['mandatory' => true, 'decodeEntities' => true, 'maxlength' => 1, 'tl_class' => 'w50'],
            'sql' => "varchar(1) NOT NULL default ''",
        ],
        'review' => [
            'exclude' => true,
            'inputType' => 'textarea',
            'default' => '',
            'search' => true,
            'eval' => ['mandatory' => true, 'decodeEntities' => true, 'maxlength' => 1000, 'tl_class' => 'w50'],
            'sql' => "mediumtext NOT NULL default ''",
        ],
        'review_date' => [
            'exclude' => true,
            'inputType' => 'text',
            'default' => time(),
            'search' => true,
            'eval' => ['mandatory' => true, 'rgxp' => 'date', 'datepicker' => true, 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql' => 'int(10) unsigned NOT NULL',
        ],
        'comment' => [
            'exclude' => true,
            'inputType' => 'textarea',
            'search' => true,
            'default' => '',
            'eval' => ['decodeEntities' => true, 'maxlength' => 1000, 'tl_class' => 'clr'],
            'sql' => "mediumtext NOT NULL default ''",
        ],
        'published' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'sql' => "char(1) NOT NULL default ''",
        ],
    ],
];

class tl_c4y_reviews extends Contao\Backend
{
    /**
     * Import the back end user object.
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('Contao\BackendUser', 'User');
    }

    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        if (Contao\Input::get('tid')) {
            $this->toggleVisibility(Contao\Input::get('tid'), (1 == Contao\Input::get('state')), (@func_get_arg(12) ?: null));
            $this->redirect($this->getReferer());
        }

        $href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);

        if ($row['published']) {
            $icon = 'visible.svg';
        }

        return '<a href="'.$this->addToUrl($href).'" title="'.Contao\StringUtil::specialchars($title).'"'.$attributes.'>'.Contao\Image::getHtml($icon, $label, 'data-state="'.($row['published'] ? 1 : 0).'"').'</a> ';
    }

    public function toggleVisibility($intId, $blnVisible, Contao\DataContainer $dc = null)
    {
        // Set the ID and action
        Contao\Input::setGet('id', $intId);
        Contao\Input::setGet('act', 'toggle');

        if ($dc) {
            $dc->id = $intId; // see #8043
        }

        // Set the current record
        if ($dc) {
            $objRow = $this->Database->prepare('SELECT * FROM tl_c4y_reviews WHERE id=?')
                ->limit(1)
                ->execute($intId);

            if ($objRow->numRows) {
                $dc->activeRecord = $objRow;
            }
        }

        $objVersions = new Contao\Versions('tl_c4y_reviews', $intId);
        $objVersions->initialize();

        $time = time();

        // Update the database
        $this->Database->prepare("UPDATE tl_c4y_reviews SET tstamp=$time, published='".($blnVisible ? '1' : '')."' WHERE id=?")
            ->execute($intId);

        if ($dc) {
            $dc->activeRecord->tstamp = $time;
            $dc->activeRecord->invisible = ($blnVisible ? '1' : '');
        }

        $objVersions->create();
    }
}
