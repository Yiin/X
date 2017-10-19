<?php

namespace App\Domain\Model\Documents\Quote;

use App\Domain\Constants\Bill\DiscountTypes;
use App\Domain\Model\Documents\Bill\BillItem;
use App\Domain\Model\Documents\Client\Client;
use App\Domain\Model\Documents\Invoice\Invoice;
use App\Domain\Model\Documents\Shared\BillableDocument;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends BillableDocument
{
    use SoftDeletes;

    protected $fillable = [
        'client_uuid',
        'status'
    ];

    protected $hidden = [
        'user_uuid',
        'company_uuid'
    ];

    public function transform()
    {
        return [
            'uuid' => $this->uuid,

            'relationships' => [
                'client' => $this->client_uuid,
                'invoice' => $this->invoice ? $this->invoice->uuid : null
            ],

            'amount' => +$this->amount(),

            'quote_date' => $this->bill->date,
            'due_date' => $this->bill->due_date,
            'partial' => +$this->bill->partial,
            'quote_number' => $this->bill->number,
            'po_number' => $this->bill->po_number,
            'discount_type' => $this->bill->discount_type,
            'discount_value' => +$this->bill->discount,
            'items' => $this->bill->items->map(function (BillItem $item) {
                return $item->transform();
            }),
            'note_to_client' => $this->bill->notes,
            'terms' => $this->bill->terms,
            'footer' => $this->bill->footer,

            'status' => $this->status,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'archived_at' => $this->archived_at,
            'deleted_at' => $this->deleted_at
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function invoice()
    {
        return $this->morphOne(Invoice::class, 'invoiceable', null, 'invoiceable_uuid');
    }
}