<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Event;
use App\Models\Sale;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'last_name',
        'customer_mail',
        'event_id',
        'qr_code',
        'payment_method',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }


    public function sale()
    {
        return $this->hasOne(Sale::class);
    }
}
