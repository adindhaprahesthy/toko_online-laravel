<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    public $timestamps = false;
    protected $primaryKey = "id_transaksi";
    protected $fillable = ['id_transaksi','id_pelanggan', 'id_petugas', 'tgl_transaksi', 'subtotal'];
    use HasFactory;
}
