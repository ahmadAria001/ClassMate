<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Civilian extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'civilian';
    protected $fillable = ['nik', 'fullName', 'birthplace', 'birthdate', 'residentstatus', 'family_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by', 'nkk', 'isFamilyHead', 'rt_id', 'address', 'status', 'phone', 'religion', 'job'];

    protected $dateFormat = 'U';

    public function rt_id(): BelongsTo
    {
        return $this->belongsTo(RT::class, 'rt_id', 'id');
    }

    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function fa(): HasManyThrough
    {
        return $this->hasManyThrough(FinancialAssistance::class, User::class, 'civilian_id', 'request_by');
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
