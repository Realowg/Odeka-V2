# Database Migration and Seeder Documentation

This document explains the Laravel migrations and seeders that have been generated from the `odeka_new.sql` MySQL database export.

## What Has Been Created

### 1. Laravel Migrations (62 files)
All database tables from the SQL file have been converted to Laravel migration files located in `database/migrations/`:

- **Core Tables**: `admin_settings`, `users`, `categories`, `countries`
- **Content Tables**: `updates`, `media`, `comments`, `likes`, `bookmarks`
- **E-commerce**: `products`, `purchases`, `transactions`, `shop_categories`
- **Social Features**: `messages`, `subscriptions`, `stories`, `notifications`
- **Live Streaming**: `live_streamings`, `live_comments`, `live_likes`
- **System Tables**: `sessions`, `jobs`, `failed_jobs`, `password_resets`
- **And many more...**

### 2. Laravel Seeders (48 files)
Database seeders have been created for tables that contained INSERT statements in the original SQL file:

- `CategoriesSeeder` - Content categories
- `CountriesSeeder` - Country list
- `LanguagesSeeder` - Available languages
- `PaymentGatewaysSeeder` - Payment gateway configurations
- `UsersSeeder` - Initial users (including admin)
- `And 43 more seeders...`

### 3. Foreign Key Constraints
A special migration (`2024_01_01_999999_add_foreign_key_constraints.php`) adds proper foreign key relationships between tables for data integrity.

## Key Features

### ✅ Clean Laravel Migrations
- Proper column types (string, integer, decimal, enum, etc.)
- Appropriate indexes for performance
- Nullable fields where appropriate
- Default values preserved
- Timestamps() for tables with created_at/updated_at

### ✅ Comprehensive Seeders
- All original data preserved
- Proper PHP array format
- Efficient bulk inserts
- DatabaseSeeder configured to call all seeders

### ✅ Foreign Key Relationships
- Referential integrity maintained
- Cascade deletions where appropriate
- Proper constraint naming

## How to Use

### 1. Running Migrations

```bash
# Run all migrations (creates all tables)
php artisan migrate

# Check migration status
php artisan migrate:status

# Rollback if needed
php artisan migrate:rollback
```

### 2. Running Seeders

```bash
# Run all seeders (populates initial data)
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=CategoriesSeeder

# Fresh migration with seeding
php artisan migrate:fresh --seed
```

### 3. Development Workflow

```bash
# For development - fresh database with all data
php artisan migrate:fresh --seed

# For production - just migrations
php artisan migrate --force
```

## Important Notes

### 🔧 Pre-Migration Steps
1. Ensure your `.env` database configuration is correct
2. Create the database if it doesn't exist
3. Install Laravel dependencies: `composer install`

### ⚠️ Data Considerations
- The original users include admin accounts with passwords
- Payment gateway configurations are included
- Consider reviewing seeded data for production use
- Some seeders contain development/test data

### 🔐 Security Notes
- Admin user passwords are hashed but should be changed
- Review user permissions in the seeded data
- Update payment gateway credentials for production

## Migration Files Structure

```
database/migrations/
├── 2024_01_01_000001_create_admin_settings_table.php
├── 2024_01_01_000002_create_advertisings_table.php
├── 2024_01_01_000003_create_ad_click_impressions_table.php
├── 2024_01_01_000004_create_users_table.php
├── 2024_01_01_000005_create_countries_table.php
├── 2024_01_01_000006_create_categories_table.php
├── 2024_01_01_000007_create_updates_table.php
├── ... (55 more migration files)
└── 2024_01_01_999999_add_foreign_key_constraints.php
```

## Seeder Files Structure

```
database/seeders/
├── CategoriesSeeder.php
├── CountriesSeeder.php
├── LanguagesSeeder.php
├── UsersSeeder.php
├── PaymentGatewaysSeeder.php
├── ... (11 more seeder files)
└── DatabaseSeeder.php
```

## Troubleshooting

### Common Issues

1. **Foreign Key Constraint Errors**
   ```bash
   # Disable foreign key checks temporarily
   DB::statement('SET FOREIGN_KEY_CHECKS=0;');
   # Your migration code
   DB::statement('SET FOREIGN_KEY_CHECKS=1;');
   ```

2. **Large Seeder Data**
   - Seeders are chunked to handle large datasets
   - Increase memory limit if needed: `ini_set('memory_limit', '256M')`

3. **Column Type Mismatches**
   - Some enum values might need adjustment for your use case
   - Decimal precision can be modified in migrations

### Migration Rollback Order
Due to foreign key constraints, rollbacks must happen in reverse order. The foreign key constraint migration handles this automatically.

## Customization

### Adding New Columns
```php
// Create a new migration
php artisan make:migration add_new_column_to_users_table

// In the migration file
Schema::table('users', function (Blueprint $table) {
    $table->string('new_column')->nullable();
});
```

### Modifying Existing Columns
```php
// Install doctrine/dbal for column modifications
composer require doctrine/dbal

// Create migration
php artisan make:migration modify_users_table

// In the migration file
Schema::table('users', function (Blueprint $table) {
    $table->string('name', 200)->change(); // Modify existing column
});
```

## Production Deployment

1. **Backup existing database** (if any)
2. **Run migrations**: `php artisan migrate --force`
3. **Skip seeders** in production or run selectively
4. **Update seeded credentials** (admin passwords, API keys)
5. **Test thoroughly**

## Support

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Enable query logging for debugging
3. Use `php artisan migrate --pretend` to see SQL without executing
4. Review the original `odeka_new.sql` for comparison

---

**Generated on**: $(date)  
**Laravel Version**: 10.x  
**PHP Version**: 8.1+  
**Total Tables**: 62 (excluding migrations table)  
**Total Seeders**: 48
