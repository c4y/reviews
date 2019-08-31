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
 * Class TokenModel.
 *
 * @property int id
 * @property int pid
 * @property int tstamp
 * @property string user
 * @property int rating
 * @property string review
 * @property int review_date
 * @property string comment
 * @property int comment_date
 * @property string published
 */
class ReviewModel extends Model
{
    protected static $strTable = 'tl_c4y_reviews';

    public static function findPublishedByPid($intPid, array $arrOptions = [])
    {
        $t = static::$strTable;

        $arrColumns[] = "$t.pid=?";
        $arrValues[] = $intPid;

        if (!static::isPreviewMode($arrOptions)) {
            $time = time();
            $arrColumns[] = "$t.published='1'";
        }

        $arrOptions['order'] = "$t.review_date";

        return static::findBy($arrColumns, $arrValues, $arrOptions);
    }
}
