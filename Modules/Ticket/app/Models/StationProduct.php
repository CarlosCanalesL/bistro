<?php

namespace Modules\Ticket\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class StationProduct extends Model
{
    protected $table = 'station_products';

    /**
     * @var string primary key
     */
    protected $primaryKey = 'station_product_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'product_id',
        'station_id',
        'user_id',
        'status',
    ];


    /**
     * Get the user.
     */
    public function usert(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->user_id = Auth::user()->user_id;
        });
    }
}
