<?php

namespace Modules\Ticket\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Station extends Model
{
   protected $table = 'stations';

    /**
     * @var string primary key
     */
    protected $primaryKey = 'station_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'station_name',
        'status',
        'user_id'
    ];


    /**
     * Get the operations for the part.
     */
    public function products(): HasMany
    {
        return $this->hasMany(StationProduct::class, 'station_id');
    }

    /**
     * Get the operations for the part.
     */
    public function users(): HasMany
    {
        return $this->hasMany(StationUser::class, 'station_id');
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->user_id = Auth::user()->user_id;
        });
    }
}
