<?php

/*
 * This file is part of c4y/reviews.
 *
 * (c) Oliver Lohoff
 *
 * @license MIT License
 */

$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE'] = array_merge_recursive(
    (array) $GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE'],
    [
        'contao' => [
            'reviews_link' => [
                'recipients' => ['recipient_email'],
                'email_subject' => ['link', 'user', 'recipient_email'],
                'email_text' => ['link', 'user', 'recipient_email'],
                'email_html' => ['link', 'user', 'recipient_email'],
                'file_name' => ['link', 'user', 'recipient_email'],
                'file_content' => ['link', 'user', 'recipient_email'],
                'email_sender_name' => ['recipient_email'],
                'email_sender_address' => ['recipient_email'],
                'email_recipient_cc' => ['recipient_email'],
                'email_recipient_bcc' => ['recipient_email'],
                'email_replyTo' => ['recipient_email'],
            ],
        ],
    ]
);

$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE'] = array_merge_recursive(
    (array) $GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE'],
    [
        'contao' => [
            'reviews_admin' => [
                'email_text' => ['link', 'user', 'rating', 'review', 'category'],
                'email_html' => ['link', 'user', 'rating', 'review', 'category']
            ],
        ],
    ]
);

// Backend modules
$GLOBALS['BE_MOD']['content']['c4y_reviews'] = [
    'tables' => ['tl_c4y_reviews_category', 'tl_c4y_reviews'],
];

// Models
$GLOBALS['TL_MODELS']['tl_c4y_reviews_token'] = 'C4Y\Reviews\Models\TokenModel';
$GLOBALS['TL_MODELS']['tl_c4y_reviews'] = 'C4Y\Reviews\Models\ReviewModel';
$GLOBALS['TL_MODELS']['tl_c4y_reviews_category'] = 'C4Y\Reviews\Models\CategoryModel';

// Form-Fields
$GLOBALS['TL_FFL']['reviews_rating'] = 'C4Y\Reviews\Forms\FormRating';

// Hooks
$GLOBALS['TL_HOOKS']['getPageLayout'][] = ['C4Y\Reviews\EventListener\Hooks', 'addCSSToFrontend'];

// Cron
$GLOBALS['TL_CRON']['daily'][] = ['C4Y\Reviews\EventListener\Hooks', 'removeExpiredToken'];
