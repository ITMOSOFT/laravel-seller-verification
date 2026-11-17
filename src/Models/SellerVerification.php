<?php

namespace ITMOSOFT\SellerVerification\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use ITMOSOFT\SellerVerification\Enums\VerificationStatus;
use ITMOSOFT\SellerVerification\Exceptions\InvalidVerificationTransition;

class SellerVerification extends Model
{
    protected $table = 'itmosoft_seller_verifications';

    protected $attributes = [
        'status' => 'draft',
    ];

    protected $fillable = [
        'seller_type', 'seller_id', 'reference', 'status', 'country_code',
        'business_name', 'registration_number', 'tax_number', 'submitted_at',
        'reviewed_at', 'reviewer_type', 'reviewer_id', 'rejection_reason',
        'expires_at', 'metadata',
    ];

    protected $casts = [
        'status' => VerificationStatus::class,
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function seller(): MorphTo
    {
        return $this->morphTo();
    }

    public function reviewer(): MorphTo
    {
        return $this->morphTo('reviewer');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(
            config('itmosoft-seller-verification.document_model', VerificationDocument::class),
            'verification'
        );
    }

    public function submit(): self
    {
        $this->transitionTo(VerificationStatus::Submitted);
        $this->submitted_at = now();
        $this->reviewed_at = null;
        $this->rejection_reason = null;
        $this->save();

        return $this;
    }

    public function startReview(): self
    {
        $this->transitionTo(VerificationStatus::UnderReview);
        $this->save();

        return $this;
    }

    public function approve(?Model $reviewer = null, ?int $validityDays = null): self
    {
        $this->transitionTo(VerificationStatus::Approved);
        $this->setReviewer($reviewer);
        $this->reviewed_at = now();

        $days = $validityDays ?? config('itmosoft-seller-verification.default_validity_days', 365);
        $this->expires_at = $days > 0 ? now()->addDays($days) : null;
        $this->rejection_reason = null;
        $this->save();

        return $this;
    }

    public function reject(string $reason, ?Model $reviewer = null): self
    {
        if (trim($reason) === '') {
            throw new \InvalidArgumentException('A rejection reason is required.');
        }

        $this->transitionTo(VerificationStatus::Rejected);
        $this->setReviewer($reviewer);
        $this->reviewed_at = now();
        $this->rejection_reason = $reason;
        $this->save();

        return $this;
    }

    public function cancel(): self
    {
        $this->transitionTo(VerificationStatus::Cancelled);
        $this->save();

        return $this;
    }

    public function markExpired(): self
    {
        if ($this->status !== VerificationStatus::Approved) {
            throw new InvalidVerificationTransition('Only approved verifications can expire.');
        }

        $this->status = VerificationStatus::Expired;
        $this->save();

        return $this;
    }

    protected function transitionTo(VerificationStatus $target): void
    {
        $current = $this->status;

        $allowed = match ($current) {
            VerificationStatus::Draft => [VerificationStatus::Submitted, VerificationStatus::Cancelled],
            VerificationStatus::Submitted => [VerificationStatus::UnderReview, VerificationStatus::Cancelled],
            VerificationStatus::UnderReview => [
                VerificationStatus::Approved,
                VerificationStatus::Rejected,
                VerificationStatus::Cancelled,
            ],
            VerificationStatus::Rejected => [VerificationStatus::Submitted, VerificationStatus::Cancelled],
            VerificationStatus::Approved => [VerificationStatus::Expired, VerificationStatus::Cancelled],
            VerificationStatus::Expired => [VerificationStatus::Submitted, VerificationStatus::Cancelled],
            VerificationStatus::Cancelled => [],
        };

        if (! in_array($target, $allowed, true)) {
            throw new InvalidVerificationTransition(
                sprintf('Cannot transition verification from %s to %s.', $current->value, $target->value)
            );
        }

        $this->status = $target;
    }

    protected function setReviewer(?Model $reviewer): void
    {
        if ($reviewer) {
            $this->reviewer()->associate($reviewer);
        } else {
            $this->reviewer_type = null;
            $this->reviewer_id = null;
        }
    }
}
