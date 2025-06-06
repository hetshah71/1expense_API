<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $guarded = [];
    


    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2'
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
}
