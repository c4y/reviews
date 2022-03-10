<?php

/*
 * This file is part of c4y/reviews.
 *
 * (c) Oliver Lohoff
 *
 * @license MIT License
 */

namespace C4Y\Reviews\Controller;

use C4Y\Reviews\Models\CategoryModel;
use C4Y\Reviews\Models\ReviewModel;
use C4Y\Reviews\Services\ReviewService;
use Contao\Input;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{
    /**
     * @var
     */
    private $reviewService;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * ReviewController constructor.
     *
     * @param ReviewService $reviewService
     */
    public function __construct(ReviewService $reviewService, Connection $connection)
    {
        $this->reviewService = $reviewService;
        $this->connection = $connection;
    }

    /**
     * {
     *  "user": "API User",
     *  "email": "test@test.de",
     *  "category": 1,
     *  "apiToken": "ac43a85f-167c-41db-aa7b-7edb51d6a55b"
     * }.
     *
     * @Route("/api/reviews/sendlink", methods={"POST"}, name="sendLink")
     */
    public function sendLink(Request $request)
    {
        $body = $request->getContent();
        $data = json_decode($body);

        if (null === $data) {
            return new JsonResponse('{"error": true, "msg": "No valid json"}');
        }

        $apiToken = CategoryModel::findByPk($data->category)->apiToken;

        if (empty($apiToken)) {
            return new JsonResponse(['error' => true, 'msg' => 'Category '.$data->category.' not found']);
        }

        if ($data->apiToken !== $apiToken) {
            return new JsonResponse(['error' => true, 'msg' => 'API-Token invalid']);
        }

        $result = $this->reviewService->sendLink($data->user, $data->email, $data->category);

        if (false === $result || false === $result['1']) {
            return new JsonResponse(['error' => true, 'msg' => 'E-Mail could not be send. Typo in email?']);
        }

        return new JsonResponse('{"error": false, "msg": "E-Mail has been send"}');
    }

    /**
     * Publish a review with the API Token in the category
     * GET /api/reviews/publish/123?apiToken=12345
     * Unfortunately we can't use PUT here, otherwise
     * the link in the email won't work
     *
     * @Route("/api/reviews/publish/{id}", methods={"GET"}, name="publishReview")
     */
    public function publishReview($id, Request $request)
    {
        $apiToken = Input::get("apiToken");

        if (empty($apiToken)) {
            return new Response('You need a valid API Token');
        }

        $review = ReviewModel::findPublishedByIdAndToken($id, $apiToken);

        if ($review == false) {
            return new Response('ID or API Key is invalid');
        }

        $review->published = "1";
        $review->save();

        return new Response('The Review with ID ' . $id . ' has been published');
    }
}
