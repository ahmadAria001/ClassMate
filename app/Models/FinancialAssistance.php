<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialAssistance extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'bansos';
    protected $fillable = [
        'request_by',
        'tanggungan',
        'alasan',
        'status',
        'tanggungan',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dateFormat = 'U';

    public function request_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'request_by', 'id');
    }

    public function civilian(): HasManyThrough
    {
        return $this->hasManyThrough(Civilian::class, User::class, 'civilian_id', 'id');
    }

    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updated_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function deleted_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id');
    }
}
