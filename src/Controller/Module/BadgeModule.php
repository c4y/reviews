<?php

/*
 * This file is part of c4y/reviews.
 *
 * (c) Oliver Lohoff
 *
 * @license MIT License
 */

namespace C4Y\Reviews\Controller\Module;

use Contao\Controller;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\Image\PictureFactoryInterface;
use Contao\FilesModel;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\Template;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BadgeModule extends AbstractFrontendModuleController
{
    private $connection;
    private $picture;
    private $frontendTemplate;

    public function __construct(Connection $connection, PictureFactoryInterface $picture)
    {
        $this->connection = $connection;
        $this->picture = $picture;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
    {
        // Badge
        $statement = $this->connection->createQueryBuilder()
            ->select('AVG(t.rating) AS rating', 'COUNT(t.rating) AS numberOfReviews')
            ->from('tl_c4y_reviews', 't')
            ->where('t.published = "1"')
            ->andWhere('t.pid =:category')
            ->setParameter('category', $model->reviews_category)
            ->execute();

        $result = $statement->fetch(\PDO::FETCH_OBJ);

        $template->numberOfReviews = $result->numberOfReviews;
        $template->rating = sprintf('%.2f', $result->rating);
        $template->ratingPercentage = 100 * $result->rating / 5;
        $template->hideBadge = ('1' === $_COOKIE['reviews_hide_badge']) ? true : false;

        $badgeLogo = new \stdClass();
        $badgeFile = FilesModel::findById($model->reviews_badge_logo);
        $badgeConfig = [
            'singleSRC' => $badgeFile->path,
            'size' => $model->reviews_badge_size,
        ];
        Controller::addImageToTemplate($badgeLogo, $badgeConfig, null, null, $badgeFile);
        $template->badgeLogo = $badgeLogo;
        // if you want to use $this->insert('image', $this->badgeLogo); inside the template, you
        // have to typecast the class
        //$template->badgeLogo = (array) $badgeLogo;

        // Reviews
        $statement = $this->connection->createQueryBuilder()
            ->select('t.*')
            ->from('tl_c4y_reviews', 't')
            ->where('t.published = "1"')
            ->andWhere('t.pid =:category')
            ->orderBy('review_date', 'DESC')
            ->setMaxResults($model->reviews_badge_numberOfReviews)
            ->setParameter('category', $model->reviews_category)
            ->execute();

        $reviews = $statement->fetchAll(\PDO::FETCH_CLASS);

        foreach ($reviews as $review) {
            $review->ratingPercentage = 100 * $review->rating / 5;
        }

        $template->reviews = $reviews;
        $template->list_logo = FilesModel::findById($model->reviews_list_logo)->path;

        $listLogo = new \stdClass();
        $listFile = FilesModel::findById($model->reviews_list_logo);
        $listConfig = [
            'singleSRC' => $listFile->path,
            'size' => $model->reviews_list_size,
        ];
        Controller::addImageToTemplate($listLogo, $listConfig, null, null, $listFile);
        $template->listLogo = $listLogo;
        $template->jumpTo = PageModel::findById($model->jumpTo)->getFrontendUrl();

        return $template->getResponse();
    }
}
