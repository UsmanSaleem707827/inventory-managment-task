<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PricingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
        'min_quantity',
        'discount',
        'start_time',
        'end_time',
        'days_of_week',
        'precedence'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
