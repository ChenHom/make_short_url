<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Short extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = false;

    protected function isSecure(): Attribute
    {
        return Attribute::make(
            get: fn (string $value): bool => $value === '1',
            set: fn (string $scheme): bool => $scheme === 'https'
        );
    }
}
