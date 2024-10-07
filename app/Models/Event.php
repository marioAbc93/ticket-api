<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ticket;
use App\Models\Sale;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'date',
        'ticket_value',
        'available_tickets',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function setAvailableTicketsAttribute($value)
    {
        $this->attributes['available_tickets'] = $value > 10 ? 10 : $value;
    }
}
