<?php
/**
 *
 * Adyen Payment Module
 *
 * Copyright (c) 2023 Adyen N.V.
 * This file is open source and available under the MIT license.
 * See the LICENSE file for more info.
 *
 * Author: Adyen <magento@adyen.com>
 */

namespace Adyen\Payment\Helper\Webhook;

use Adyen\Payment\Logger\AdyenLogger;
use Adyen\Payment\Model\Notification;
use Adyen\Webhook\Exception\InvalidDataException;

class WebhookHandlerFactory
{
    private $authorisationWebhookHandler;
    private $captureWebhookHandler;
    private $offerClosedWebhookHandler;
    private $adyenLogger;
    private $refundWebhookHandler;
    private $refundFailedWebhookHandler;
    private $manualReviewAcceptWebhookHandler;
    private $manualReviewRejectWebhookHandler;
    private $recurringContractWebhookHandler;
    private $pendingWebhookHandler;
    private $cancellationWebhookHandler;
    private $cancelOrRefundWebhookHandler;
    private $orderClosedWebhookHandler;

    public function __construct(
        AdyenLogger $adyenLogger,
        AuthorisationWebhookHandler $authorisationWebhookHandler,
        CaptureWebhookHandler $captureWebhookHandler,
        OfferClosedWebhookHandler $offerClosedWebhookHandler,
        RefundWebhookHandler $refundWebhookHandler,
        RefundFailedWebhookHandler $refundFailedWebhookHandler,
        ManualReviewAcceptWebhookHandler $manualReviewAcceptWebhookHandler,
        ManualReviewRejectWebhookHandler $manualReviewRejectWebhookHandler,
        RecurringContractWebhookHandler $recurringContractWebhookHandler,
        PendingWebhookHandler $pendingWebhookHandler,
        CancellationWebhookHandler $cancellationWebhookHandler,
        CancelOrRefundWebhookHandler $cancelOrRefundWebhookHandler,
        OrderClosedWebhookHandler $orderClosedWebhookHandler
    ) {
        $this->adyenLogger = $adyenLogger;
        $this->authorisationWebhookHandler = $authorisationWebhookHandler;
        $this->captureWebhookHandler = $captureWebhookHandler;
        $this->offerClosedWebhookHandler = $offerClosedWebhookHandler;
        $this->refundWebhookHandler = $refundWebhookHandler;
        $this->refundFailedWebhookHandler = $refundFailedWebhookHandler;
        $this->manualReviewAcceptWebhookHandler = $manualReviewAcceptWebhookHandler;
        $this->manualReviewRejectWebhookHandler = $manualReviewRejectWebhookHandler;
        $this->recurringContractWebhookHandler = $recurringContractWebhookHandler;
        $this->pendingWebhookHandler = $pendingWebhookHandler;
        $this->cancellationWebhookHandler = $cancellationWebhookHandler;
        $this->cancelOrRefundWebhookHandler = $cancelOrRefundWebhookHandler;
        $this->orderClosedWebhookHandler = $orderClosedWebhookHandler;
    }

    /**
     * @throws InvalidDataException
     */
    public function create(string $eventCode): WebhookHandlerInterface
    {
        switch ($eventCode) {
            case Notification::HANDLED_EXTERNALLY:
            case Notification::AUTHORISATION:
                return $this->authorisationWebhookHandler;
            case Notification::CAPTURE:
                return $this->captureWebhookHandler;
            case Notification::OFFER_CLOSED:
                return $this->offerClosedWebhookHandler;
            case Notification::REFUND:
                return $this->refundWebhookHandler;
            case Notification::REFUND_FAILED:
                return $this->refundFailedWebhookHandler;
            case Notification::MANUAL_REVIEW_ACCEPT:
                return $this->manualReviewAcceptWebhookHandler;
            case Notification::MANUAL_REVIEW_REJECT:
                return $this->manualReviewRejectWebhookHandler;
            case Notification::RECURRING_CONTRACT:
                return $this->recurringContractWebhookHandler;
            case Notification::PENDING:
                return $this->pendingWebhookHandler;
            case Notification::CANCELLATION:
                return $this->cancellationWebhookHandler;
            case Notification::CANCEL_OR_REFUND:
                return $this->cancelOrRefundWebhookHandler;
            case Notification::ORDER_CLOSED:
                return $this->orderClosedWebhookHandler;
        }

        $exceptionMessage = sprintf(
            'Unknown webhook type: %s. This type is not yet handled by the Adyen Magento plugin', $eventCode
        );

        $this->adyenLogger->addAdyenWarning($exceptionMessage);
        /*
         * InvalidDataException is used for consistency. Since Webhook Module
         * throws the same exception for unknown webhook event codes.
         */
        throw new InvalidDataException(__($exceptionMessage));
    }
}
