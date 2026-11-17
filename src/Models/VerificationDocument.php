<?php

namespace ITMOSOFT\SellerVerification\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class VerificationDocument extends Model
{
    protected $table = 'itmosoft_verification_documents';

    protected $attributes = [
        'status' => 'pending',
        'disk' => 'local',
    ];

    protected $fillable = [
        'verification_type', 'verification_id', 'type', 'disk', 'path',
        'original_name', 'mime_type', 'size', 'status', 'review_notes', 'metadata',
    ];

    protected $casts = [
        'size' => 'integer',
        'metadata' => 'array',
    ];

    public function verification(): MorphTo
    {
        return $this->morphTo();
    }
}
