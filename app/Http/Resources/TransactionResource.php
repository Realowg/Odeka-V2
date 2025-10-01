<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'txn_id' => $this->txn_id,
            'type' => $this->type,
            'amount' => (float) $this->amount,
            'earning' => (float) $this->earning_net_user,
            'fee' => (float) $this->earning_net_admin,
            'payment_gateway' => $this->payment_gateway,
            'status' => $this->approved ? 'approved' : 'pending',
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

