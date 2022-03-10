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
use Contao\PageModel;
use NotificationCenter\Model\Notification;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ReviewService
{
    /**
     * @var
     */
    protected $request;

    /**
     * @var RouterInterface
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
        TokenGeneratorInterface $tokenGenerator,
        ContaoFramework $contao
    ) {
        $this->tokenGenerator = $tokenGenerator;
        $this->contao = $contao;

        // to load the Contao models
        $this->contao->initialize();
    }

    /**
     * @param string $user
     * @param string $recipient
     * @param int $categoryId
     * @return array|false
     */
    public function sendLink(string $user, string $recipient, int $categoryId)
    {
        // generate alias
        $categoryModel = CategoryModel::findById($categoryId);
        $url = PageModel::findById($categoryModel->url)->getAbsoluteUrl();

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
            'link' => $url . "?token=" . $token
        ];

        /** @var Notification $notification */
        $notification = Notification::findByPk($categoryModel->notification);
        if (null !== $notification) {
            return $notification->send($notificationTokens);
        }

        return false;
    }
}
