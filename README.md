# ITMOSOFT Laravel Seller Verification

A reusable seller/business verification workflow for Laravel applications. It supports polymorphic sellers and reviewers, document metadata, review decisions, resubmission, and expiry tracking.

## Requirements

- PHP 8.3+
- Laravel 12 or 13
- SQLite/MySQL/PostgreSQL supported by Laravel migrations

## Installation

```bash
composer require itmosoft/laravel-seller-verification
php artisan vendor:publish --tag=seller-verification-config
php artisan migrate
```

Laravel package discovery registers the service provider automatically.

## Attach verification to a seller model

```php
use ITMOSOFT\SellerVerification\Concerns\HasSellerVerifications;

class Vendor extends Model
{
    use HasSellerVerifications;
}
```

Create a verification:

```php
$verification = $vendor->createSellerVerification([
    'reference' => (string) Str::uuid(),
    'country_code' => 'PK',
    'business_name' => 'Example Traders',
    'registration_number' => 'REG-123',
    'metadata' => ['source' => 'vendor-onboarding'],
]);
```

The `reference` field is required and unique. Generate it in your application (UUID/ULID or your own business reference).

## Submit and review

```php
$verification->submit();
$verification->startReview();
$verification->approve($admin, validityDays: 365);

// Or reject with a reason:
$verification->reject('Please provide a clearer registration certificate.', $admin);
```

Rejected and expired verifications can be resubmitted. Invalid lifecycle transitions throw `InvalidVerificationTransition`.

`approve()` uses `default_validity_days` when no explicit validity is supplied. Pass `0` to create an approval without an expiry date.

## Add document metadata

```php
$verification->documents()->create([
    'type' => 'business_registration',
    'disk' => 'private',
    'path' => 'seller-verification/123/registration.pdf',
    'original_name' => 'registration.pdf',
    'mime_type' => 'application/pdf',
    'size' => 245000,
]);
```

This package stores document metadata and a storage path. It does **not** upload files, validate MIME types, scan files for malware, or authorize access. Upload files to a private disk and enforce authorization/download policies in your application. Do not store sensitive document contents in the JSON metadata column.

## Querying

```php
$latest = $vendor->latestSellerVerification();

$pending = SellerVerification::query()
    ->where('status', 'submitted')
    ->oldest('submitted_at')
    ->get();
```

Statuses: `draft`, `submitted`, `under_review`, `approved`, `rejected`, `expired`, `cancelled`.

## Configuration

Publish `itmosoft-seller-verification.php` to configure the storage disk, model classes, and default approval validity. `storage_disk` is a convention for your application; the package does not perform uploads.

## Important boundaries

This package is a workflow primitive, not a complete KYC/AML or compliance solution. It does not provide identity checks, sanctions screening, OCR, regulatory rules, automatic expiry scheduling, notifications, reviewer authorization, immutable audit logs, or cryptographic document integrity. Protect reviewer actions with your application's authorization policies and log consequential decisions using your audit system.

## Testing

```bash
composer install
composer test
```

## License

MIT. See [LICENSE](LICENSE).
