<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Folder extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [
        'name',
        'user_id',
    ];

    /**
     * Relasi ke model User (pemilik folder).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke model File (file yang ada dalam folder ini).
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }
}
