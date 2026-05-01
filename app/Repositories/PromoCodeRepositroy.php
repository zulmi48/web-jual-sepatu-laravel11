<?php

namespace App\Repositories;

use App\Models\PromoCode;

class PromoCodeRepositroy implements Contracts\PromoCodeRepositoryInterface
{
    public function getAllPromoCode()
    {
        return PromoCode::latest()->get();
    }

    public function findByCode(string $code)
    {
        return PromoCode::where('code', $code)->first();
    }
}
