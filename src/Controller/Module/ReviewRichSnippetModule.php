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

class ReviewRichSnippetModule extends AbstractFrontendModuleController
{
    private $connection;
    private $frontendTemplate;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*, AVG(t.rating) AS avgRating', 'COUNT(t.rating) AS numberOfReviews')
            ->from('tl_c4y_reviews', 't')
            ->where('t.published = "1"')
            ->andWhere('t.pid =:category')
            ->setMaxResults(1)
            ->orderBy("review_date", "DESC")
            ->setParameter('category', $model->reviews_category)
            ->execute();

        $result = $statement->fetch(\PDO::FETCH_OBJ);

        $template->numberOfReviews = $result->numberOfReviews;
        $template->avgRating = sprintf('%.2f', $result->avgRating);
        $template->user = $result->user;
        $template->review = $result->review;
        $template->review_date = $review_date;

        return $template->getResponse();
    }
}
