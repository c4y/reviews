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
use Contao\ModuleModel;
use Contao\Template;
use Haste\Util\Pagination;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;

/**
 * @FrontendModule(ReviewListModule::TYPE, category="miscellaneous")
 */
class ReviewListModule extends AbstractFrontendModuleController
{
    public const TYPE = 'reviews_list';

    protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
    {
        $total = ReviewModel::countPublishedByPid($model->reviews_category);

        $objPagination = new Pagination($total, $model->perPage, 'page_rp'.$model->id);

        if ($objPagination->isOutOfRange()) {
            $objHandler = new $GLOBALS['TL_PTY']['error_404']();
            $objHandler->generate($GLOBALS['objPage']->id);
            exit;
        }

        $arrOptions = array(
            "limit" => $objPagination->getLimit(),
            "offset" => $objPagination->getOffset(),
            "order" => 'review_date DESC'
        );
        $reviews = ReviewModel::findPublishedByPid($model->reviews_category, $arrOptions);

        $template->pagination = $objPagination->generate();
        $template->reviews = $reviews;

        return $template->getResponse();
    }
}
