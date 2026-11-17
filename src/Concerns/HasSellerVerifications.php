<?php

namespace ITMOSOFT\SellerVerification\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use ITMOSOFT\SellerVerification\Models\SellerVerification;

trait HasSellerVerifications
{
    public function sellerVerifications(): MorphMany
    {
        return $this->morphMany(
            config('itmosoft-seller-verification.verification_model', SellerVerification::class),
            'seller'
        );
    }

    public function latestSellerVerification(): ?SellerVerification
    {
        return $this->sellerVerifications()->latest('id')->first();
    }

    public function createSellerVerification(array $attributes = []): SellerVerification
    {
        return $this->sellerVerifications()->create($attributes);
    }
}
