<?php

/*
 * This file is part of c4y/reviews.
 *
 * (c) Oliver Lohoff
 *
 * @license MIT License
 */

namespace C4Y\Reviews\Models;

use C4Y\Reviews\Dto\Statistic;
use Contao\Database;
use Contao\Model;

/**
 * Class ReviewModel.
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
            $arrColumns[] = "$t.published='1'";
        }

        return static::findBy($arrColumns, $arrValues, $arrOptions);
    }

    /**
     * @param $id
     * @param $token
     * @param array $arrOptions
     * @return ReviewModel|false
     */
    public static function findPublishedByIdAndToken($id, $token, array $arrOptions = [])
    {
        $sql = "SELECT r.*, c.apiToken FROM tl_c4y_reviews r
                LEFT JOIN tl_c4y_reviews_category c
                ON c.id=r.pid
                WHERE
                    r.id=? AND
                    c.apiToken = ? AND
                    r.published=''";

        $objStatement = Database::getInstance()->prepare($sql);
        $objStatement->limit(1);
        $objResult = $objStatement->execute($id, $token);

        return ($objResult->numRows > 0) ? new ReviewModel($objResult) : false;
    }

    /**
     * @param $pid
     * @param array $arrOptions
     * @return int
     */
    public static function countPublishedByPid($pid, $arrOptions=array()):int
    {
        $t = static::$strTable;

        $arrColumns[] = "$t.pid=?";
        $arrValues[] = $pid;

        if (!static::isPreviewMode($arrOptions)) {
            $arrColumns[] = "$t.published='1'";
        }

        return static::countBy($arrColumns, $arrValues, $arrOptions);
    }

    /**
     * @param $pid
     * @return Statistic
     */
    public static function getStatistic($pid): Statistic
    {
        $t = static::$strTable;

        $sql = "SELECT COALESCE(AVG(rating), 0) AS rating, COUNT(rating) AS numberOfReviews FROM $t
                    WHERE published=1 AND pid=?";
        $objStatement = Database::getInstance()->prepare($sql);
        $objResult = $objStatement->execute($pid);

        $statistic = new Statistic();
        $statistic->setRating($objResult->rating);
        $statistic->setNumberOfReviews($objResult->numberOfReviews);
        $statistic->setRatingInPercent(100 * $objResult->rating / 5);

        return $statistic;
    }
}
