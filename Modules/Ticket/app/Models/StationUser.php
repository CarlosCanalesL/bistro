<?php

namespace Modules\Ticket\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StationUser extends Model
{
    protected $table = 'station_users';

    /**
     * @var string primary key
     */
    protected $primaryKey = 'station_user_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'station_id',
        'user_id'
    ];


    /**
     * Get the user.
     */
    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class, 'station_id', 'station_id');
    }

    /**
     * Get the product.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
