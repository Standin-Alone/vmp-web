<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherGenModel extends Model
{
    use HasFactory;    
    protected $table = "voucher";
    public $timestamps = false;
    protected $fillable = ['amount'];
}
