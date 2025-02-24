<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [
        'name',
        'path',
        'user_id',
        'folder_id',
        'size',
        'type',
    ];

    /**
     * Relasi ke User (pemilik file).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Folder (jika file ada di dalam folder).
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }
}
