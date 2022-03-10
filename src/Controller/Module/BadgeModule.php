<?php

/*
 * This file is part of c4y/reviews.
 *
 * (c) Oliver Lohoff
 *
 * @license MIT License
 */

namespace C4Y\Reviews\Controller\Module;

use C4Y\Reviews\Models\ReviewModel;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;

/**
 * @FrontendModule(BadgeModule::TYPE, category="miscellaneous")
 */
class BadgeModule extends AbstractFrontendModuleController
{
    public const TYPE = 'reviews_badge';

    protected $studio;

    public function __construct(Studio $studio)
    {
        $this->studio = $studio;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
    {
        $template->statistic = ReviewModel::getStatistic($model->reviews_category);
        $template->hideBadge = (isset($_COOKIE['reviews_hide_badge']) && $_COOKIE['reviews_hide_badge'] == '1') ? true : false;

        $figureBuilder = $this->studio->createFigureBuilder();

        $template->badgeLogo = $figureBuilder
            ->fromUuid($model->reviews_badge_logo)
            ->setSize($model->reviews_badge_size)
            ->build();

        $template->listLogo = $figureBuilder
            ->fromUuid($model->reviews_list_logo)
            ->setSize($model->reviews_list_size)
            ->build();

        $arrOptions = array(
            "limit"=>$model->reviews_badge_numberOfReviews,
            "order" => 'review_date DESC'
        );
        $reviews = ReviewModel::findPublishedByPid($model->reviews_category, $arrOptions);

        $template->reviews = $reviews;
        $template->jumpTo = PageModel::findById($model->jumpTo)->getFrontendUrl();

        return $template->getResponse();
    }
}
