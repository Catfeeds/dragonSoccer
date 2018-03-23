<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Orderslog extends Model{
    protected $table='orders_log';
    protected $fillable=['sn','content'];
    
}