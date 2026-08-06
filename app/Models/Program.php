<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    use HasFactory;

    protected $fillable = ['bidang_id', 'nama'];

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class);
    }

    public function kegiatans(): HasMany
    {
        return $this->hasMany(Kegiatan::class);
    }
}
