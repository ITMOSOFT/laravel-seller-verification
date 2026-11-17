<?php

return [
    'storage_disk' => 'local',
    'verification_model' => ITMOSOFT\SellerVerification\Models\SellerVerification::class,
    'document_model' => ITMOSOFT\SellerVerification\Models\VerificationDocument::class,
    'statuses' => [
        'draft',
        'submitted',
        'under_review',
        'approved',
        'rejected',
        'expired',
        'cancelled',
    ],
    'default_validity_days' => 365,
];
