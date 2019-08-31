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
use C4Y\Reviews\Models\TokenModel;
use C4Y\Reviews\Services\FormFactory;
use C4Y\Reviews\Services\ReviewService;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\Email;
use Contao\Input;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\Template;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use NotificationCenter\Model\Notification;

class ReviewFormModule extends AbstractFrontendModuleController
{
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
    private $reviewService;

    /**
     * @var Connection
     */
    private $connection;


    /**
     * FormController constructor.
     *
     * @param FormFactory $formFactory
     */
    public function __construct(
        FormFactory $formFactory,
        TokenModel $tokenModel,
        ReviewModel $reviewModel,
        ReviewService $reviewService,
        Connection $connection
        ) {
        $this->formFactory = $formFactory;
        $this->tokenModel = $tokenModel;
        $this->reviewModel = $reviewModel;
        $this->reviewService = $reviewService;
        $this->connection = $connection;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
    {
        $token = Input::get('token');

        if (empty($token)) {
            // redirect
            $url = PageModel::findById($model->reviews_jumpToError)->getFrontendUrl();

            return $this->redirect($url);
        }

        $tokenModel = $this->tokenModel->findBy('token', $token);

        if (null === $tokenModel || time() > $tokenModel->expires) {
            // redirect
            $url = PageModel::findById($model->reviews_jumpToError)->getFrontendUrl();

            return $this->redirect($url);
        }

        $objForm = $this->formFactory->create('reviews_form', 'POST', function ($objHaste) {
            return \Input::post('FORM_SUBMIT') === $objHaste->getFormId();
        });

        $objForm->bindModel($this->reviewModel);

        $objForm->addFormField('rating', [
            'label' => $GLOBALS['TL_LANG']['reviews']['form_review_rating'][0],
            'inputType' => 'reviews_rating',
            'options' => [1, 2, 3, 4, 5],
            'eval' => ['mandatory' => true],
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

        if ($objForm->validate()) {

            // save review
            $this->reviewModel->pid = $tokenModel->category;
            $this->reviewModel->user = $tokenModel->user;
            $this->reviewModel->tstamp = time();
            $this->reviewModel->comment = '';
            $this->reviewModel->review_date = time();
            $this->reviewModel->comment_date = 0;
            $this->reviewModel->published = '';
            $savedReview = $this->reviewModel->save();

            // delete token
            $tokenModel->delete();

            $statement = $this->connection->createQueryBuilder()
                ->select("apiToken, title, notification_admin")
                ->from("tl_c4y_reviews_category")
                ->where("id = :id")
                ->setParameter("id", $tokenModel->category)
                ->execute();

            $categoryResult = $statement->fetch(\PDO::FETCH_OBJ);

            if (false === $result) {
                return new JsonResponse(['error' => true, 'msg' => 'Token invalid or id not exists']);
            }

            // send notification to administrator
            $link = sprintf('%s://%s/api/reviews/publish/%s?apiToken=%s',
                $request->getScheme(),
                $request->getHost(),
                $savedReview->id,
                $categoryResult->apiToken
            );

            $notificationTokens = [
                'link' => $link,
                'user' => $tokenModel->user,
                'category' => $categoryResult->title,
                'rating' => $_POST["rating"],
                'review' => $_POST["review"]
            ];

            /** @var Notification $notification */
            $notification = Notification::findByPk($categoryResult->notification_admin);
            if (null !== $notification) {
                $notification->send($notificationTokens);
            }

            // redirect
            $url = PageModel::findById($model->jumpTo)->getFrontendUrl();

            return $this->redirect($url);
        }

        $template->form = $objForm->generate();

        return $template->getResponse();
    }
}
