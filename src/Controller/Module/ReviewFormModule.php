<?php

/*
 * This file is part of c4y/reviews.
 *
 * (c) Oliver Lohoff
 *
 * @license MIT License
 */

namespace C4Y\Reviews\Controller\Module;

use C4Y\Reviews\Models\CategoryModel;
use C4Y\Reviews\Models\ReviewModel;
use C4Y\Reviews\Models\TokenModel;
use C4Y\Reviews\Services\FormFactory;
use C4Y\Reviews\Services\ReviewService;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\Input;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\Template;
use Codefog\HasteBundle\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use NotificationCenter\Model\Notification;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;

/**
 * @FrontendModule(ReviewFormModule::TYPE, category="miscellaneous")
 */
class ReviewFormModule extends AbstractFrontendModuleController
{
    public const TYPE = 'reviews_form';

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var TokenModel
     */
    protected $tokenModel;

    /**
     * @var ReviewModel
     */
    protected $reviewModel;

    /**
     * @var ReviewService
     */
    protected $reviewService;


    /**
     * FormController constructor.
     *
     * @param FormFactory $formFactory
     */
    public function __construct(
        FormFactory $formFactory,
        TokenModel $tokenModel,
        ReviewModel $reviewModel,
        ReviewService $reviewService
        ) {
        $this->formFactory = $formFactory;
        $this->tokenModel = $tokenModel;
        $this->reviewModel = $reviewModel;
        $this->reviewService = $reviewService;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
    {
        $token = Input::get('token');
        $tokenModel = $this->getTokenModel($token);

        if($tokenModel == false) {
            $url = PageModel::findById($model->reviews_jumpToError)->getAbsoluteUrl();
            return $this->redirect($url);
        }

        $objForm = $this->getForm();

        if ($objForm->validate()) {
            $review = $this->saveReview($tokenModel);
            $category = CategoryModel::findByPk($tokenModel->category);
            $link = $this->getReviewLink($request, $review, $category);
            $notificationToken = $this->getNotificationToken($link, $review, $category);
            $this->sendNotification($category, $notificationToken);
            $tokenModel->delete();

            $url = PageModel::findById($model->jumpTo)->getAbsoluteUrl();

            return $this->redirect($url);
        }

        $template->form = $objForm->generate();

        return $template->getResponse();
    }

    /**
     * @return Form
     */
    protected function getForm(): Form
    {
        $objForm = $this->formFactory->create('reviews_form', 'POST', function ($objHaste) {
            return \Input::post('FORM_SUBMIT') === $objHaste->getFormId();
        });

        $objForm->setBoundModel($this->reviewModel);

        $objForm->addFormField('rating', [
            'label' => $GLOBALS['TL_LANG']['reviews']['form_review_rating'][0],
            'inputType' => 'reviews_rating',
            'options' => [1, 2, 3, 4, 5],
            'eval' => ['mandatory' => true],
        ]);

        $objForm->addFormField('headline', [
            'label' => $GLOBALS['TL_LANG']['reviews']['form_review_headline'][0],
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 128],
        ]);

        $objForm->addFormField('review', [
            'label' => $GLOBALS['TL_LANG']['reviews']['form_review_review'][0],
            'inputType' => 'textarea',
            'eval' => ['mandatory' => true],
        ]);

        $objForm->addFormField('submit', [
            'label' => $GLOBALS['TL_LANG']['reviews']['form_review_submit'][0],
            'inputType' => 'submit',
        ]);

        return $objForm;
    }

    /**
     * @param $token
     * @return TokenModel|bool
     */
    protected function getTokenModel($token)
    {
        if (empty($token)) {
            return false;
        }

        $tokenModel = $this->tokenModel->findOneBy('token', $token);

        if (null === $tokenModel || time() > $tokenModel->expires) {
            return false;
        }

        return $tokenModel;
    }

    /**
     * @param TokenModel $tokenModel
     * @return ReviewModel
     */
    protected function saveReview(TokenModel $tokenModel): ReviewModel
    {
        // save review
        $this->reviewModel->pid = $tokenModel->category;
        $this->reviewModel->user = $tokenModel->user;
        $this->reviewModel->tstamp = time();
        $this->reviewModel->comment = '';
        $this->reviewModel->review_date = time();
        $this->reviewModel->comment_date = 0;
        $this->reviewModel->published = '';

        return $this->reviewModel->save();
    }

    /**
     * @param Request $request
     * @param ReviewModel $reviewModel
     * @param CategoryModel $categoryModel
     * @return string
     */
    protected function getReviewLink(Request $request, ReviewModel $reviewModel, CategoryModel $categoryModel): string
    {
        $link = sprintf('%s://%s:%s/api/reviews/publish/%s?apiToken=%s',
            $request->getScheme(),
            $request->getHost(),
            $request->getPort(),
            $reviewModel->id,
            $categoryModel->apiToken
        );

        return $link;
    }

    /**
     * @param string $link
     * @param ReviewModel $tokenModel
     * @param CategoryModel $reviewCategoryModel
     * @return array
     */
    protected function getNotificationToken(string $link, ReviewModel $tokenModel, CategoryModel $reviewCategoryModel): array
    {
        return [
            'link' => $link,
            'user' => $tokenModel->user,
            'category' => $reviewCategoryModel->title,
            'rating' => $_POST["rating"],
            'review' => $_POST["review"]
        ];
    }

    /**
     * @param CategoryModel $category
     * @param array $notificationToken
     */
    protected function sendNotification(CategoryModel $category, array $notificationToken)
    {
        /** @var Notification $notification */
        $notification = Notification::findByPk($category->notification_admin);
        if (null !== $notification) {
            $notification->send($notificationToken);
        }
    }
}
