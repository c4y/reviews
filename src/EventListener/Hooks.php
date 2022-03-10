<?php

/*
 * This file is part of c4y/reviews.
 *
 * (c) Oliver Lohoff
 *
 * @license MIT License
 */

namespace C4Y\Reviews\EventListener;

use Doctrine\DBAL\Connection;

class Hooks
{
    /**
     * Hooks constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * adds CSS to Frontend if checkbox is set in page layout.
     *
     * @param $objPage
     * @param $objLayout
     * @param $objPty
     */
    public function addCSSToFrontend($objPage, $objLayout, $objPty)
    {
        if ($objLayout->addReviewsCss) {
            $GLOBALS['TL_FRAMEWORK_CSS'][] = 'bundles/reviews/reviews.css';
        }
    }

    /**
     * remove expired token.
     */
    public function removeExpiredToken()
    {
        $this->connection->createQueryBuilder()
            ->delete('tl_c4y_reviews_token')
            ->where('expires < :tstamp')
            ->setParameter('tstamp', time())
            ->executeQuery();
    }
}
