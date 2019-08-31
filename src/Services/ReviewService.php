<?php

/*
 * This file is part of c4y/reviews.
 *
 * (c) Oliver Lohoff
 *
 * @license MIT License
 */

namespace C4Y\Reviews\Services;

use C4Y\Reviews\Models\CategoryModel;
use C4Y\Reviews\Models\TokenModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Routing\UrlGenerator;
use Contao\PageModel;
use NotificationCenter\Model\Notification;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ReviewService
{
    /**
     * @var
     */
    protected $request;

    /**
     * @var UrlGenerator
     */
    private $router;

    /**
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;

    /**
     * @var ContaoFramework
     */
    private $contao;

    public function __construct(
        UrlGenerator $router,
        TokenGeneratorInterface $tokenGenerator,
        ContaoFramework $contao
    ) {
        $this->router = $router;
        $this->tokenGenerator = $tokenGenerator;
        $this->contao = $contao;

        // to load the Contao models
        $this->contao->initialize();
    }

    /**
     * @param $user
     * @param $recipient
     * @param $categoryId
     */
    public function sendLink($user, $recipient, $categoryId)
    {
        // generate alias
        $categoryModel = CategoryModel::findById($categoryId);
        $alias = PageModel::findById($categoryModel->url)->alias;

        // create and store Token
        $token = $this->tokenGenerator->generateToken();
        $tokenModel = new TokenModel();
        $tokenModel->token = $token;
        $tokenModel->tstamp = time();
        $tokenModel->expires = time() + 60 * 60 * 24 * 31; // ein Monat
        $tokenModel->category = $categoryId;
        $tokenModel->user = $user;
        $tokenModel->save();

        // Send notification
        $notificationTokens = [
            'recipient_email' => $recipient,
            'user' => $user,
            'link' => $this->router->generate(
                $alias,
                ['token' => $token],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ];

        /** @var Notification $notification */
        $notification = Notification::findByPk($categoryModel->notification);
        if (null !== $notification) {
            return $notification->send($notificationTokens);
        }

        return false;
    }
}
