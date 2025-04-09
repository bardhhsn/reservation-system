<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationChangeRequest extends Model
{
    protected $fillable = [
        'reservation_id',
        'user_id',
        'type',
        'new_data',
        'status'
    ];

    protected $casts = [
        'new_data' => 'array'
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
