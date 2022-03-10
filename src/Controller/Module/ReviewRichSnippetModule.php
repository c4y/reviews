<?php

/*
 * This file is part of c4y/reviews.
 *
 * (c) Oliver Lohoff
 *
 * @license MIT License
 */

namespace C4Y\Reviews\Controller\Module;

use C4Y\Reviews\Dto\Statistic;
use C4Y\Reviews\Models\ReviewModel;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\ModuleModel;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;

/**
 * @FrontendModule(ReviewRichSnippetModule::TYPE, category="miscellaneous")
 */
class ReviewRichSnippetModule extends AbstractFrontendModuleController
{
    public const TYPE = 'reviews_richsnippet';

    protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
    {
        /* @var Statistic $statistic */
        $statistic = ReviewModel::getStatistic($model->reviews_category);

        $template->numberOfReviews = $statistic->getNumberOfReviews();
        $template->avgRating = sprintf('%.2f', $statistic->getRating());

        return $template->getResponse();
    }
}
