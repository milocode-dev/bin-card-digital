<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = ['warehouse_id', 'name', 'code', 'unit', 'location', 'min_stock', 'stock'];

    public function warehouse() {
        return $this->belongsTo(Warehouse::class);
    }
}
