<?php

namespace Snayvik\Translation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SnvkLocale extends Model
{
    use HasFactory;

    protected $fillable = ['locale','name'];
}
