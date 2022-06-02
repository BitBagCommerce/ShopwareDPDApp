<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Tests\Factory;

use BitBag\ShopwareAppSystemBundle\Model\Feedback\Notification\Error;
use BitBag\ShopwareAppSystemBundle\Model\Feedback\Notification\Success;
use BitBag\ShopwareAppSystemBundle\Model\Feedback\Notification\Warning;
use BitBag\ShopwareAppSystemBundle\Response\FeedbackResponse;
use BitBag\ShopwareDpdApp\Factory\FeedbackResponseFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class FeedbackResponseFactoryTest extends WebTestCase
{
    private TranslatorInterface $translator;

    protected function setUp(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects(self::exactly(2))
                   ->method('trans')
                   ->willReturnCallback(function ($value) {
                       return $value;
                   });

        $this->translator = $translator;
    }

    public function testReturnError(): void
    {
        $feedbackResponse = new FeedbackResponseFactory($this->translator);

        $messageKey = 'foo';

        self::assertEquals(
            new FeedbackResponse(new Error($this->translator->trans($messageKey))),
            $feedbackResponse->returnError($messageKey)
        );
    }

    public function testReturnSuccess(): void
    {
        $feedbackResponse = new FeedbackResponseFactory($this->translator);

        $messageKey = 'foo';

        self::assertEquals(
            new FeedbackResponse(new Success($this->translator->trans($messageKey))),
            $feedbackResponse->returnSuccess($messageKey)
        );
    }

    public function testReturnWarning(): void
    {
        $feedbackResponse = new FeedbackResponseFactory($this->translator);

        $messageKey = 'foo';

        self::assertEquals(
            new FeedbackResponse(new Warning($this->translator->trans($messageKey))),
            $feedbackResponse->returnWarning($messageKey)
        );
    }
}
