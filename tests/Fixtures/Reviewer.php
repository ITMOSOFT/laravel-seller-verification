<?php

namespace ITMOSOFT\SellerVerification\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

class Reviewer extends Model
{
    protected $table = 'test_reviewers';
    protected $guarded = [];
    public $timestamps = false;
}
