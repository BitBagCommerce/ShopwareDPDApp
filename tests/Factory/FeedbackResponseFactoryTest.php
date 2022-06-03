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
    private FeedbackResponseFactory $feedbackResponseFactory;

    protected function setUp(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')
                   ->willReturnCallback(function (string $value) {
                       return $value;
                   });

        $this->feedbackResponseFactory = new FeedbackResponseFactory($translator);
    }

    public function testCreateError(): void
    {
        self::assertEquals(
            new FeedbackResponse(new Error('')),
            $this->feedbackResponseFactory->createError('')
        );
    }

    public function testCreateSuccess(): void
    {
        self::assertEquals(
            new FeedbackResponse(new Success('')),
            $this->feedbackResponseFactory->createSuccess('')
        );
    }

    public function testCreateWarning(): void
    {
        self::assertEquals(
            new FeedbackResponse(new Warning('')),
            $this->feedbackResponseFactory->createWarning('')
        );
    }
}
