# PHP_Laravel12_Set_Index_Using_Drag_Drop

This project is a simple Laravel application demonstrating **CRUD operations with drag-and-drop product sorting**. Products can be reordered using a draggable interface, and the order is saved automatically using AJAX.

---

## Project Overview

**Application Name:** Product Sorting App
**Framework:** Laravel
**Frontend:** Blade + Bootstrap 5
**JavaScript:** jQuery, SortableJS
**Database:** MySQL

---

## Features

* Product CRUD (Create, Read, Update, Delete)
* Drag & Drop sorting using SortableJS
* Automatic order saving via AJAX
* Order persistence after page refresh
* Active / Inactive product status
* Toast notifications on reorder
* Responsive UI using Bootstrap 5
* Seeder for demo data

---

## Requirements

* PHP 8.1 or higher
* Composer
* Node.js & NPM
* MySQL
* Laravel CLI

---

## Installation Steps

### Step 1: Create Laravel Project

```bash
laravel new product-sorting-app
cd product-sorting-app
```

### Step 2: Install UI Dependencies

```bash
composer require laravel/ui
php artisan ui bootstrap
npm install
npm run dev
```

### Step 3: Database Configuration

Create a database and update your `.env` file:

```env
DB_DATABASE=product_sorting
DB_USERNAME=root
DB_PASSWORD=
```

---

## Product Model & Migration

Create model and migration:

```bash
php artisan make:model Product -m
```

### Migration Structure

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->decimal('price', 8, 2);
    $table->integer('sort_order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### Product Model

```php
class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

Run migration:

```bash
php artisan migrate
```

---

## Controller

Create controller:

```bash
php artisan make:controller ProductController --resource
```

### Controller Responsibilities

* Display ordered product list
* Create & update products
* Delete products and re-order remaining items
* Handle AJAX sorting updates

---

## Routes

```php
Route::get('/', function () {
    return redirect()->route('products.index');
});

Route::resource('products', ProductController::class);

Route::post('/products/update-order',
    [ProductController::class, 'updateOrder']
)->name('products.update-order');
```

---

## Views Structure

```
resources/views/
├── layouts/
│   └── app.blade.php
└── products/
    ├── index.blade.php
    ├── create.blade.php
    └── edit.blade.php
```

---

## Drag & Drop Sorting

* Implemented using SortableJS
* Sorting handled via AJAX
* Updates `sort_order` column in database
* Order automatically updates on drop

---

## CSRF Protection for AJAX

Add CSRF meta tag in layout:

```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

Add AJAX setup:

```javascript
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
```

---

## Database Seeder

Create seeder:

```bash
php artisan make:seeder ProductSeeder
```

### Seeder Data Example

```php
Product::create([
    'name' => 'Laptop',
    'description' => 'High performance laptop',
    'price' => 999.99,
    'sort_order' => 1,
    'is_active' => true
]);
```

Register in `DatabaseSeeder.php`:

```php
$this->call(ProductSeeder::class);
```

Run seeder:

```bash
php artisan db:seed
```

---

## Run Application

```bash
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Visit:

```
http://localhost:8000
```
---
## Screenshot
### *Product Sorter
<img width="1624" height="952" alt="image" src="https://github.com/user-attachments/assets/2a5b1f13-ba48-48ae-bd4d-1dc12b25b010" />

---

## Implemented Features Summary

* Product CRUD operations
* Drag-and-drop row sorting
* AJAX-based order saving
* Toast notifications
* Active / Inactive status
* Bootstrap responsive UI
* Seeder with sample data

---

## Possible Enhancements

* User authentication
* Category-based sorting
* Search & filtering
* Bulk actions
* Undo reorder feature
* Activity logs
* Import / export functionality
* Database transactions for sorting
