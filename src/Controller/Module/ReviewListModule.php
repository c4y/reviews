<?php

/*
 * This file is part of c4y/reviews.
 *
 * (c) Oliver Lohoff
 *
 * @license MIT License
 */

namespace C4Y\Reviews\Controller\Module;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\ModuleModel;
use Contao\Template;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReviewListModule extends AbstractFrontendModuleController
{
    private $connection;
    private $frontendTemplate;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
    {
        // total
        $statement = $this->connection->createQueryBuilder()
            ->select('COUNT(*) AS total')
            ->from('tl_c4y_reviews', 't')
            ->where('t.published = "1"')
            ->andWhere('t.pid =:category')
            ->setParameter('category', $model->reviews_category)
            ->execute();

        $total = $statement->fetch(\PDO::FETCH_OBJ)->total;

        $objPagination = new \Haste\Util\Pagination($total, $model->perPage, 'page_rp'.$this->id);

        if ($objPagination->isOutOfRange()) {
            $objHandler = new $GLOBALS['TL_PTY']['error_404']();
            $objHandler->generate($GLOBALS['objPage']->id);
            exit;
        }

        // List
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('tl_c4y_reviews', 't')
            ->where('t.published = "1"')
            ->andWhere('t.pid =:category')
            ->setFirstResult($objPagination->getOffset())
            ->setMaxResults($objPagination->getLimit())
            ->orderBy('review_date', 'DESC')
            ->setParameter('category', $model->reviews_category)
            ->execute();

        $reviews = $statement->fetchAll(\PDO::FETCH_OBJ);

        $template->pagination = $objPagination->generate();
        $template->reviews = $reviews;

        return $template->getResponse();
    }
}
