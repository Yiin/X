<?php

namespace App\Domain\Model\Documents\Payment;

use League\Fractal;
use App\Domain\Model\System\ActivityLog\ActivityTransformer;

class PaymentTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
        'history'
    ];

    public function transform(Payment $payment)
    {
        return [
            'uuid' => $payment->uuid,

            'client_uuid' => $payment->client_uuid,
            'invoice_uuid' => $payment->invoice_uuid,

            'amount' => +$payment->amount,
            'refunded' => +$payment->refunded,
            'currency' => $payment->currency,
            'payment_type' => $payment->paymentType,
            'payment_date' => $payment->payment_date,
            'payment_reference' => $payment->payment_reference,

            'is_disabled' => $payment->is_disabled,

            'created_at' => $payment->created_at,
            'updated_at' => $payment->updated_at,
            'deleted_at' => $payment->deleted_at,
            'archived_at' => $payment->archived_at
        ];
    }

    public function includeHistory(Payment $payment)
    {
        return $this->collection($payment->getHistory(), new ActivityTransformer);
    }
}