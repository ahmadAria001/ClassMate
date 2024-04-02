<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DuesPaymentLog extends Model
{
    use HasFactory;
    protected $table = 'duesPaymentLog';
    protected $fillable = [
        'paid_by',
        'dues_id',
        'amount_paid',
        'exchange',
        'debt',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dateFormat = 'U';

    protected function paid_by(): BelongsTo
    {
        return $this->belongsTo(Civilian::class, 'paid_by', 'id');
    }
    protected function paid_id(): BelongsTo
    {
        return $this->belongsTo(Dues::class, 'paid_id', 'id');
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
