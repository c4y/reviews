<?php

/*
 * This file is part of c4y/reviews.
 *
 * (c) Oliver Lohoff
 *
 * @license MIT License
 */

namespace C4Y\Reviews\Controller\Module;

use C4Y\Reviews\Services\FormFactory;
use C4Y\Reviews\Services\ReviewService;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\Input;
use Contao\ModuleModel;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;

/**
 * @FrontendModule(SendReviewLinkModule::TYPE, category="miscellaneous")
 */
class SendReviewLinkModule extends AbstractFrontendModuleController
{
    public const TYPE = 'reviews_sendlink';

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var ReviewService
     */
    protected $reviewService;

    /**
     * FormController constructor.
     *
     * @param FormFactory $formFactory
     */
    public function __construct(FormFactory $formFactory, ReviewService $reviewService)
    {
        $this->formFactory = $formFactory;
        $this->reviewService = $reviewService;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
    {
        $objForm = $this->formFactory->create('sendlink', 'POST', function ($objHaste) {
            return \Input::post('FORM_SUBMIT') === $objHaste->getFormId();
        });

        $objForm->addFormField('user', [
            'label' => $GLOBALS['TL_LANG']['reviews']['form_user'][0],
            'inputType' => 'text',
            'eval' => ['mandatory' => true],
        ]);

        $objForm->addFormField('email', [
            'label' => $GLOBALS['TL_LANG']['reviews']['form_email'][0],
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'rgxp' => 'email'],
        ]);

        $objForm->addFormField('submit', [
            'label' => $GLOBALS['TL_LANG']['reviews']['form_sendlink_submit'][0],
            'inputType' => 'submit',
        ]);

        if ($objForm->validate()) {
            $this->reviewService->sendLink(Input::post('user'), Input::post('email'), $model->reviews_category);
            $template->msg = $GLOBALS['TL_LANG']['reviews']['form_sendlink_msg'][0];
            $template->url = $request->getRequestUri();
            $template->buttonLabel = $GLOBALS['TL_LANG']['reviews']['form_sendlink_buttonLabel'][0];
        } else {
            $template->form = $objForm->generate();
        }

        return $template->getResponse();
    }
}
