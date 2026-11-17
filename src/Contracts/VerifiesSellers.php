<?php

namespace ITMOSOFT\SellerVerification\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use ITMOSOFT\SellerVerification\Models\SellerVerification;

interface VerifiesSellers
{
    public function sellerVerifications(): MorphMany;

    public function latestSellerVerification(): ?SellerVerification;
}
