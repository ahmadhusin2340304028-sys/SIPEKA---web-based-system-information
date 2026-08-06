<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kegiatan extends Model
{
    use HasFactory;

    protected $fillable = ['program_id', 'nama'];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function subKegiatans(): HasMany
    {
        return $this->hasMany(SubKegiatan::class);
    }
}
