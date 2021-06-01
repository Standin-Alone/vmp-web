<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommodityModel extends Model
{
    use HasFactory;

    protected $table = 'tbl_commodity';
    public $timestamps = false;
    protected $fillable = ['commodity','quantity','unit','amount','REFERENCE_NO','RSBSA_CTRL_NO','SUPPLIER_CODE','SUPPLIER_GROUP','DISTRIBUTOR_ID'];

    
}
