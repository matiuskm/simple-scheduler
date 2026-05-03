<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model {
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'address',
        'code'
    ];

    public function schedules(): HasMany {
        return $this->hasMany(Schedule::class);
    }
}
