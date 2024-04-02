<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Civilian extends Model
{
    use HasFactory;

    protected $table = 'civilian';
    protected $fillable = [
        'nik',
        'fullName',
        'birthplace',
        'birthdate',
        'residentstatus',
        'family_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dateFormat = 'U';

    protected function family_id(): BelongsTo
    {
        return $this->belongsTo(Family::class, 'family_id', 'id');
    }

    protected function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    protected function updated_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    protected function deleted_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id');
    }
}
