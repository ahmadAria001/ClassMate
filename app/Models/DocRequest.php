<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocRequest extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'doc_request';
    protected $fillable = [
        'requestStatus',
        'request_by',
        'docs_id',
        'rt_stat',
        'responsed_by_rt',
        'rw_stat',
        'responsed_by_rw',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dateFormat = 'U';

    public function docs_id(): BelongsTo
    {
        return $this->belongsTo(Docs::class, 'docs_id', 'id');
    }

    public function request_by(): BelongsTo
    {
        return $this->belongsTo(Civilian::class, 'request_by', 'id');
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
