<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillItem extends Model
{
     protected $fillable = [
        'bill_id',
        'item_name',
        'quantity',
        'price',
        'amount'
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
