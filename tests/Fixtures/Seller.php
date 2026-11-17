<?php

namespace ITMOSOFT\SellerVerification\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use ITMOSOFT\SellerVerification\Concerns\HasSellerVerifications;

class Seller extends Model
{
    use HasSellerVerifications;

    protected $table = 'test_sellers';
    protected $guarded = [];
    public $timestamps = false;
}
