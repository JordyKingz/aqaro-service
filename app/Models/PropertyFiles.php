<?php

namespace App\Models;

use App\Models\Enums\FileType;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyFiles extends Model
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
        'name',
        'storage_url',
        'type',
        'property_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'types' => AsEnumCollection::class.':'.FileType::class,
    ];

    public function property() {
        return $this->belongsTo(Property::class);
    }
}
