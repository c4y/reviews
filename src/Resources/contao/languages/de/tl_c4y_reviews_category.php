<?php

/*
 * This file is part of c4y/reviews.
 *
 * (c) Oliver Lohoff
 *
 * @license MIT License
 */

$GLOBALS['TL_LANG']['tl_c4y_reviews_category']['title_legend'] = 'Titel';
$GLOBALS['TL_LANG']['tl_c4y_reviews_category']['settings_legend'] = 'Einstellungen';

// Fields
$GLOBALS['TL_LANG']['tl_c4y_reviews_category']['title'] = ['Titel', ''];
$GLOBALS['TL_LANG']['tl_c4y_reviews_category']['url'] = ['URL', 'Wo befindet sich das Formular, um eine Bewertung abzugeben? Erstellen Sie ein Frontend-Modul Bewertungen - Formular, binden Sie es auf einer Seite ein und geben Sie diese Seite hier an.'];
$GLOBALS['TL_LANG']['tl_c4y_reviews_category']['notification'] = ['Benachrichtung', 'Bitte die Benachrichtigung auswählen.'];
$GLOBALS['TL_LANG']['tl_c4y_reviews_category']['notification_admin'] = ['Admin-Benachrichtigung', 'Die Notification für den Admin.'];
$GLOBALS['TL_LANG']['tl_c4y_reviews_category']['apiToken'] = ['API Token', 'Dieser Token wird benötigt, um per API eine Einladung zu verschicken. POST /api/reviews/sendlink mit einem JSON Body und User, Email, Category, apiToken. Siehe hierzu auch die Readme. Der automatisch generierte Token kann überschrieben werden.'];
