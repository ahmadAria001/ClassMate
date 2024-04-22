<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class RT extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'rt';
    protected $fillable = ['leader_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by'];

    protected $dateFormat = 'U';

    public function leader_id(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id', 'id');
    }

    public function family(): HasMany
    {
        return $this->hasMany(Family::class, 'rt_id', 'id');
    }

    public function civilian(): HasManyThrough
    {
        return $this->hasManyThrough(Civilian::class, Family::class);
    }

    public function civils(): HasManyThrough
    {
        return $this->hasManyThrough(Civilian::class, Family::class, 'rt_id', 'family_id', 'id', 'id');
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
