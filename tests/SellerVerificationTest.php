<?php

namespace ITMOSOFT\SellerVerification\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use ITMOSOFT\SellerVerification\Enums\VerificationStatus;
use ITMOSOFT\SellerVerification\Exceptions\InvalidVerificationTransition;
use ITMOSOFT\SellerVerification\Models\SellerVerification;
use ITMOSOFT\SellerVerification\Tests\Fixtures\Reviewer;
use ITMOSOFT\SellerVerification\Tests\Fixtures\Seller;

class SellerVerificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('test_sellers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::create('test_reviewers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function test_seller_can_create_a_draft_verification(): void
    {
        $seller = Seller::query()->create(['name' => 'Acme']);
        $verification = $seller->createSellerVerification([
            'reference' => 'SV-1001',
            'business_name' => 'Acme Limited',
        ]);

        $this->assertSame(VerificationStatus::Draft, $verification->status);
        $this->assertSame($seller->id, $verification->seller_id);
        $this->assertSame('Acme Limited', $verification->business_name);
    }

    public function test_submission_and_review_lifecycle_can_be_completed(): void
    {
        $seller = Seller::query()->create(['name' => 'Acme']);
        $reviewer = Reviewer::query()->create(['name' => 'Reviewer']);
        $verification = $seller->createSellerVerification(['reference' => 'SV-1002']);

        $verification->submit()->startReview()->approve($reviewer, 30);

        $verification->refresh();
        $this->assertSame(VerificationStatus::Approved, $verification->status);
        $this->assertSame($reviewer->id, $verification->reviewer_id);
        $this->assertNotNull($verification->submitted_at);
        $this->assertNotNull($verification->reviewed_at);
        $this->assertNotNull($verification->expires_at);
    }

    public function test_rejection_requires_a_reason_and_can_be_resubmitted(): void
    {
        $seller = Seller::query()->create(['name' => 'Acme']);
        $verification = $seller->createSellerVerification(['reference' => 'SV-1003']);
        $verification->submit()->startReview();

        try {
            $verification->reject('  ');
            $this->fail('Expected invalid argument exception.');
        } catch (\InvalidArgumentException) {
            $this->assertTrue(true);
        }

        $verification->reject('Document is unreadable');
        $verification->submit();

        $this->assertSame(VerificationStatus::Submitted, $verification->status);
        $this->assertNull($verification->rejection_reason);
    }

    public function test_invalid_transition_is_rejected(): void
    {
        $seller = Seller::query()->create(['name' => 'Acme']);
        $verification = $seller->createSellerVerification(['reference' => 'SV-1004']);

        $this->expectException(InvalidVerificationTransition::class);
        $verification->approve();
    }

    public function test_approved_verification_can_be_marked_expired(): void
    {
        $seller = Seller::query()->create(['name' => 'Acme']);
        $verification = $seller->createSellerVerification(['reference' => 'SV-1005']);
        $verification->submit()->startReview()->approve(null, 0)->markExpired();

        $this->assertSame(VerificationStatus::Expired, $verification->fresh()->status);
    }

    public function test_verification_can_have_document_metadata(): void
    {
        $seller = Seller::query()->create(['name' => 'Acme']);
        $verification = $seller->createSellerVerification(['reference' => 'SV-1006']);
        $document = $verification->documents()->create([
            'type' => 'business_registration',
            'disk' => 'private',
            'path' => 'seller-documents/acme/registration.pdf',
            'original_name' => 'registration.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1024,
            'metadata' => ['country' => 'PK'],
        ]);

        $this->assertSame('business_registration', $document->type);
        $this->assertSame(['country' => 'PK'], $document->metadata);
        $this->assertCount(1, $verification->documents);
    }

    public function test_latest_verification_returns_most_recent_record(): void
    {
        $seller = Seller::query()->create(['name' => 'Acme']);
        $seller->createSellerVerification(['reference' => 'SV-1007']);
        $latest = $seller->createSellerVerification(['reference' => 'SV-1008']);

        $this->assertSame($latest->id, $seller->latestSellerVerification()->id);
    }

    public function test_verification_can_be_cancelled_before_review(): void
    {
        $seller = Seller::query()->create(['name' => 'Acme']);
        $verification = $seller->createSellerVerification(['reference' => 'SV-1009']);
        $verification->cancel();

        $this->assertSame(VerificationStatus::Cancelled, $verification->fresh()->status);
    }
}
