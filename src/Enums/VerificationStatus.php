<?php

namespace ITMOSOFT\SellerVerification\Enums;

enum VerificationStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case UnderReview = 'under_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function isFinal(): bool
    {
        return in_array($this, [self::Approved, self::Rejected, self::Expired, self::Cancelled], true);
    }
}
