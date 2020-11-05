<?php

/*
 * This file is part of c4y/reviews.
 *
 * (c) Oliver Lohoff
 *
 * @license MIT License
 */

$GLOBALS['TL_LANG']['tl_c4y_reviews_category']['title_legend'] = 'Title';
$GLOBALS['TL_LANG']['tl_c4y_reviews_category']['settings_legend'] = 'Settings';

// Fields
$GLOBALS['TL_LANG']['tl_c4y_reviews_category']['title'] = ['Title', ''];
$GLOBALS['TL_LANG']['tl_c4y_reviews_category']['url'] = ['URL', 'Where is the form to leave a review? Create a front-end module reviews - form, embed it on a page and enter this page here.'];
$GLOBALS['TL_LANG']['tl_c4y_reviews_category']['notification'] = ['Notification', 'Please select the notification.'];
$GLOBALS['TL_LANG']['tl_c4y_reviews_category']['notification_admin'] = ['Admin notification', 'The notification for the admin.'];
$GLOBALS['TL_LANG']['tl_c4y_reviews_category']['apiToken'] = ['API Token', 'This token is required to send an invitation via API. POST / api / reviews / sendlink with a JSON body and user, email, category, apiToken. See also the readme. The automatically generated token can be overwritten.'];
