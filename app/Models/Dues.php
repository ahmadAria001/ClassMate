<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dues extends Model
{
    use HasFactory;

    protected $table = 'dues';
    protected $dateFormat = 'U';
    protected $fillable = [
        'typeDues',
        'description',
        'amt_dues',
        'amt_fund',
        'status',
        'rt_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    public function rt_id(): BelongsTo
    {
        return $this->belongsTo(RT::class, 'rt_id', 'id');
    }

    protected function docs_id(): BelongsTo
    {
        return $this->belongsTo(Docs::class, 'docs_id', 'id');
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
