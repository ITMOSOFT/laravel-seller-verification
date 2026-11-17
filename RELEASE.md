# Release instructions

## Repository

- Repository: `ITMOSOFT/laravel-seller-verification`
- Composer package: `itmosoft/laravel-seller-verification`
- Description: `Seller verification workflows for Laravel applications, with polymorphic seller ownership, document metadata, review decisions, and expiry tracking.`

## Validate locally

```bash
composer validate --strict
composer install
composer test
```

Do not tag a release until local PHPUnit tests pass and the GitHub Actions workflow is green.

## User-run Git commands

Run these from the existing, already-connected repository. These commands are provided for you to execute; no repository changes are performed by the assistant.

```bash
git add .
git commit -m "feat: add seller verification workflows"
git push origin main
```

After tests and CI pass:

```bash
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0
```

## GitHub Release

Title: `v1.0.0 - Seller Verification`

Description:

```markdown
## Features
- Polymorphic seller verification records.
- Verification submission, review, approval, rejection, cancellation, and resubmission lifecycle.
- Polymorphic reviewer assignment.
- Rejection reasons and approval expiry dates.
- Document metadata relationship with private-storage path references.
- Seller model concern for creating and retrieving verification records.

## Requirements
- PHP 8.3+
- Laravel 12 or 13

## Installation
composer require itmosoft/laravel-seller-verification
php artisan migrate

## Notes
This package manages workflow state and document metadata. File upload, private-file authorization, KYC/AML checks, sanctions screening, notifications, and reviewer permissions remain application responsibilities.
```

## Packagist

1. Open Packagist and sign in.
2. Submit `https://github.com/ITMOSOFT/laravel-seller-verification`.
3. Confirm the package name is `itmosoft/laravel-seller-verification`.
4. Configure the GitHub service hook if prompted.
5. Verify Packagist detects the `v1.0.0` tag and that `composer require itmosoft/laravel-seller-verification` resolves.
