<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Controller;

use BitBag\ShopwareAppSystemBundle\Model\Feedback\Notification\Success;
use BitBag\ShopwareAppSystemBundle\Response\FeedbackResponse;
use Symfony\Component\HttpFoundation\Response;

final class OrderController
{
    public function __invoke(): Response
    {
        return new FeedbackResponse(new Success('Order controller works!'));
    }
}
