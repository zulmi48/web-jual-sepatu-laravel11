<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductTransaction extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'phone',
        'email',
        'booking_trx_id',
        'city',
        'post_code',
        'proof',
        'shoe_size',
        'address',
        'quantity',
        'sub_total_amount',
        'grand_total_amount',
        'discount_amount',
        'is_paid',
        'shoe_id',
        'promo_code_id'
    ];

    public function shoe(): BelongsTo
    {
        return $this->belongsTo(Shoe::class);
    }

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function generateUniqueTrxId(): void
    {
        do {
            $trxId = 'TRX-' . random_int(100000, 9999999);
        } while (self::where('booking_trx_id', $trxId)->exists());

        $this->booking_trx_id = $trxId;
    }
}
