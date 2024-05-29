<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DuesMember extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'dues_member';
    protected $fillable = [
        'id',
        'dues',
        'member',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dateFormat = 'U';

    public function dues(): BelongsTo
    {
        return $this->belongsTo(Dues::class, 'dues', 'id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Civilian::class, 'member', 'id');
    }
}
