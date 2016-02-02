<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchases extends Model {

	protected $fillable = ['date_created', 'date_delivered', 'number', 'supplier_id', 'invoice_id', 'status'];

}
