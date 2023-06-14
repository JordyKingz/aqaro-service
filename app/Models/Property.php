<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'sc_id',
        'description',
        'price_usd',
        'eth_price',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'user_id',
    ];

    public function owner() {
        return $this->belongsTo(User::class);
    }

    public function files() {
        return $this->hasMany(PropertyFiles::class);
    }
}
