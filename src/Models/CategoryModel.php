<?php

/*
 * This file is part of c4y/reviews.
 *
 * (c) Oliver Lohoff
 *
 * @license MIT License
 */

namespace C4Y\Reviews\Models;

use Contao\Model;

/**
 * Class CategoryModel.
 *
 * @property int id
 * @property int pid
 * @property int tstamp
 * @property string title
 * @property string url
 * @property int notification
 * @property int notification_admin
 * @property string apiToken
 */
class CategoryModel extends Model
{
    protected static $strTable = 'tl_c4y_reviews_category';
}
