<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttachmentModel extends Model
{
    use HasFactory;
    protected $table = "fims_attach";
    public $timestamps = false;
    protected $fillable = ['att_file','requirement','filetitle','REFERENCE_NO','RSBSA_CTRL_NO','imglink','SUPPLIER_CODE','DISTRIBUTOR_ID'];
}
