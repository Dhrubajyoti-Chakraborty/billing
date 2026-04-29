<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
     protected $fillable = [
        'bill_no',
        'customer_name',
        'bill_date',
        'item_total',
        'gst_percent',
        'gst_amount',
        'grand_total'
    ];

    public function items()
    {
        return $this->hasMany(BillItem::class);
    }
}
