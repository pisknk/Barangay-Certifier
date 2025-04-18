# Tenant Status Codes

## Overview

The `is_active` field in the Tenant model can have several values that indicate different states of the tenant. This document explains what each status code means and how it affects tenant functionality.

## Status Codes

| Code | Constant | Description |
|------|----------|-------------|
| 0 | `INACTIVE` | Tenant has not been activated. This is the initial state after tenant creation. |
| 1 | `ACTIVE` | Tenant is active and has a valid subscription. This is the only state where tenants can access their domain. |
| 2 | `DEACTIVATED` | Tenant has been manually deactivated by an administrator. This could be due to policy violations or other administrative reasons. |
| 3 | `EXPIRED` | Tenant's subscription has expired. This happens automatically when the `valid_until` date is reached. |

## Status Transitions

- `0 (INACTIVE) → 1 (ACTIVE)`: Occurs when a tenant is activated, typically after payment confirmation.
- `1 (ACTIVE) → 2 (DEACTIVATED)`: Occurs when an administrator manually deactivates a tenant.
- `1 (ACTIVE) → 3 (EXPIRED)`: Occurs automatically when the tenant's subscription expiration date is reached.
- `2 (DEACTIVATED) → 1 (ACTIVE)`: Occurs when an administrator reactivates a deactivated tenant.
- `3 (EXPIRED) → 1 (ACTIVE)`: Occurs when a tenant renews their subscription after expiration.

## Domain Access

Only tenants with a status of `1 (ACTIVE)` can access their domain. All other statuses will redirect to appropriate error pages:

- `0 (INACTIVE)`: Redirects to the payment required page
- `2 (DEACTIVATED)`: Redirects to the account disabled page
- `3 (EXPIRED)`: Redirects to the subscription expired page

## Subscription Expiration Behavior

When a tenant is marked as expired (status 3) or manually deactivated (status 2), their `valid_until` date is preserved in the database. This allows for:

1. Easy reactivation without losing the subscription history
2. Ability to see when a tenant's subscription expired (for reporting)
3. Option to provide grace periods based on the original expiration date

When reactivating an expired tenant, the system checks their subscription plan and extends the `valid_until` date accordingly from the current date.

## Command Line Management

The following commands are available to manage tenant statuses:

- `php artisan tenants:mark-expired`: Automatically checks for and marks expired subscriptions (runs daily via scheduler)
- `php artisan tenancy:activate {tenant_id}`: Activates a tenant
- `php artisan tenancy:deactivate {tenant_id}`: Deactivates a tenant
- `php artisan tenancy:test-active-status {tenant_id}`: Displays detailed information about a tenant's status 