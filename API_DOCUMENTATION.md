# API Documentation - Wello_BE

> Backend REST API untuk Platform E-commerce Makanan Sehat

---

## Daftar Isi

1. [Overview](#1-overview)
2. [Response Format Standard](#2-response-format-standard)
3. [Authentication](#3-authentication)
4. [Categories](#4-categories)
5. [Products](#5-products)
6. [Addresses](#6-addresses)
7. [Cart](#7-cart)
8. [Orders](#8-orders)
9. [Order Tracking](#9-order-tracking)
10. [Payments](#10-payments)
11. [Couriers](#11-couriers)
12. [Error Codes Reference](#12-error-codes-reference)
13. [Database Schema](#13-database-schema)
14. [Flow Diagrams](#14-flow-diagrams)
15. [Pagination & Sorting Guide](#15-pagination--sorting-guide)
16. [File Upload Guide](#16-file-upload-guide)

---

## 1. Overview

### Base URL

```
http://localhost:8000/api
```

### Authentication

| Type | Header |
|------|--------|
| Bearer Token | `Authorization: Bearer {token}` |

Menggunakan **Laravel Sanctum** untuk token-based authentication.

### Content Type

```
Content-Type: application/json
```

Untuk file upload:
```
Content-Type: multipart/form-data
```

### Rate Limiting

| Limit | Duration |
|-------|----------|
| General | 60 requests / minute |
| Auth | 10 requests / minute |

---

## 2. Response Format Standard

### Success Response

**Status: 200 OK**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": {
    "id": 1,
    "name": "Product Name"
  }
}
```

**Status: 201 Created**
```json
{
  "success": true,
  "message": "Data berhasil dibuat",
  "data": {
    "id": 1,
    "name": "Product Name"
  }
}
```

### Success Response with Pagination

```json
{
  "success": true,
  "message": "Berhasil",
  "data": [
    { "id": 1, "name": "Product 1" },
    { "id": 2, "name": "Product 2" }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 12,
    "total": 60
  }
}
```

### Error Response

**Status: 400 Bad Request**
```json
{
  "success": false,
  "message": "Permintaan tidak valid"
}
```

**Status: 401 Unauthenticated**
```json
{
  "success": false,
  "message": "Unauthenticated"
}
```

**Status: 403 Forbidden**
```json
{
  "success": false,
  "message": "Forbidden"
}
```

**Status: 404 Not Found**
```json
{
  "success": false,
  "message": "Data tidak ditemukan"
}
```

**Status: 422 Validation Error**
```json
{
  "success": false,
  "message": "Validasi gagal",
  "errors": {
    "email": ["Email sudah terdaftar"],
    "password": ["Password minimal 8 karakter"]
  }
}
```

**Status: 500 Server Error**
```json
{
  "success": false,
  "message": "Terjadi kesalahan sistem"
}
```

---

## 3. Authentication

### 3.1 Register

**URL:** `/api/auth/register`  
**Method:** `POST`  
**Auth:** No

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| name | string | Yes | Nama lengkap (3-255 karakter) |
| email | string | Yes | Email valid & unik |
| password | string | Yes | Password (min 8 karakter) |
| password_confirmation | string | Yes | Konfirmasi password |

**Request Example:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "secret123",
  "password_confirmation": "secret123"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Register berhasil",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "customer",
    "created_at": "2026-09-01T10:00:00.000000Z"
  },
  "access_token": "1|abc123..."
}
```

**Error (422):**
```json
{
  "success": false,
  "message": "Validasi gagal",
  "errors": {
    "email": ["Email sudah terdaftar"]
  }
}
```

---

### 3.2 Login

**URL:** `/api/auth/login`  
**Method:** `POST`  
**Auth:** No

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| email | string | Yes | Email terdaftar |
| password | string | Yes | Password |

**Request Example:**
```json
{
  "email": "john@example.com",
  "password": "secret123"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "customer"
  },
  "access_token": "1|abc123..."
}
```

**Error (401):**
```json
{
  "success": false,
  "message": "Email atau password salah"
}
```

---

### 3.3 Logout

**URL:** `/api/auth/logout`  
**Method:** `POST`  
**Auth:** Yes  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {token}
```

**Request Body:** None

**Response (200):**
```json
{
  "success": true,
  "message": "Logout berhasil"
}
```

---

### 3.4 Get Profile

**URL:** `/api/user`  
**Method:** `GET`  
**Auth:** Yes  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "customer",
    "email_verified_at": "2026-09-01T10:00:00.000000Z",
    "created_at": "2026-09-01T10:00:00.000000Z",
    "updated_at": "2026-09-01T10:00:00.000000Z"
  }
}
```

---

### 3.5 Update Profile

**URL:** `/api/user`  
**Method:** `PUT`  
**Auth:** Yes  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {token}
```

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| name | string | No | Nama baru (3-255 karakter) |
| email | string | No | Email baru (valid & unik) |
| password | string | No | Password baru (min 8 karakter) |
| password_confirmation | string | No | Konfirmasi password baru |

**Request Example:**
```json
{
  "name": "John Updated",
  "email": "john.updated@example.com"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Profil berhasil diperbarui",
  "data": {
    "id": 1,
    "name": "John Updated",
    "email": "john.updated@example.com",
    "role": "customer",
    "updated_at": "2026-09-01T12:00:00.000000Z"
  }
}
```

---

## 4. Categories

### 4.1 Get All Categories (Public)

**URL:** `/api/categories`  
**Method:** `GET`  
**Auth:** No

**Query Parameters:** None

**Response (200):**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": [
    {
      "id": 1,
      "name": "Sayuran Segar",
      "slug": "sayuran-segar",
      "products_count": 15,
      "created_at": "2026-09-01T10:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "Buah-buahan",
      "slug": "buah-buahan",
      "products_count": 20,
      "created_at": "2026-09-01T10:00:00.000000Z"
    }
  ]
}
```

---

### 4.2 Get Category by Slug (Public)

**URL:** `/api/categories/{slug}`  
**Method:** `GET`  
**Auth:** No

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| slug | string | Slug kategori |

**Response (200):**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": {
    "id": 1,
    "name": "Sayuran Segar",
    "slug": "sayuran-segar",
    "products": [
      {
        "id": 1,
        "name": "Bayam Organik",
        "slug": "bayam-organik",
        "price": 15000,
        "image": "/storage/products/bayam.jpg",
        "healthy_score": 95
      }
    ]
  }
}
```

**Error (404):**
```json
{
  "success": false,
  "message": "Kategori tidak ditemukan"
}
```

---

### 4.3 Create Category (Admin)

**URL:** `/api/admin/categories`  
**Method:** `POST`  
**Auth:** Yes (Admin)  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {admin_token}
```

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| name | string | Yes | Nama kategori (3-255 karakter) |

**Request Example:**
```json
{
  "name": "Sayuran Segar"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Kategori berhasil dibuat",
  "data": {
    "id": 1,
    "name": "Sayuran Segar",
    "slug": "sayuran-segar",
    "created_at": "2026-09-01T10:00:00.000000Z"
  }
}
```

**Error (403):**
```json
{
  "success": false,
  "message": "Forbidden"
}
```

---

### 4.4 Update Category (Admin)

**URL:** `/api/admin/categories/{id}`  
**Method:** `PUT`  
**Auth:** Yes (Admin)  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {admin_token}
```

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | ID kategori |

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| name | string | Yes | Nama kategori baru |

**Request Example:**
```json
{
  "name": "Sayuran Organik"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Kategori berhasil diperbarui",
  "data": {
    "id": 1,
    "name": "Sayuran Organik",
    "slug": "sayuran-organik",
    "updated_at": "2026-09-01T12:00:00.000000Z"
  }
}
```

---

### 4.5 Delete Category (Admin)

**URL:** `/api/admin/categories/{id}`  
**Method:** `DELETE`  
**Auth:** Yes (Admin)  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {admin_token}
```

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | ID kategori |

**Response (200):**
```json
{
  "success": true,
  "message": "Kategori berhasil dihapus"
}
```

**Error (422):**
```json
{
  "success": false,
  "message": "Tidak dapat menghapus kategori yang masih memiliki produk"
}
```

---

## 5. Products

### 5.1 Get All Products (Public)

**URL:** `/api/products`  
**Method:** `GET`  
**Auth:** No

**Query Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| page | integer | No | Nomor halaman (default: 1) |
| per_page | integer | No | Item per halaman (default: 12) |
| category_id | integer | No | Filter by kategori |
| min_price | integer | No | Harga minimum |
| max_price | integer | No | Harga maksimum |
| min_healthy_score | integer | No | Skor sehat minimum (0-100) |
| max_healthy_score | integer | No | Skor sehat maksimum (0-100) |
| status | boolean | No | Status produk (true/false) |
| search | string | No | Pencarian nama/deskripsi |
| sort_by | string | No | Field sorting (name, price, healthy_score, created_at) |
| sort_order | string | No | ASC atau DESC (default: DESC) |

**Request Example:**
```
GET /api/products?category_id=1&min_price=10000&max_price=50000&sort_by=price&sort_order=asc&page=1&per_page=12
```

**Response (200):**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": [
    {
      "id": 1,
      "category_id": 1,
      "name": "Bayam Organik",
      "slug": "bayam-organik",
      "description": "Bayam segar organik dari petani lokal",
      "nutrition": "Vitamin A, C, K, Iron, Calcium",
      "price": 15000,
      "stock": 100,
      "weight": 250,
      "image": "/storage/products/bayam.jpg",
      "healthy_score": 95,
      "status": true,
      "category": {
        "id": 1,
        "name": "Sayuran Segar",
        "slug": "sayuran-segar"
      },
      "created_at": "2026-09-01T10:00:00.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 12,
    "total": 60
  }
}
```

---

### 5.2 Get Product by Slug (Public)

**URL:** `/api/products/{slug}`  
**Method:** `GET`  
**Auth:** No

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| slug | string | Slug produk |

**Response (200):**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": {
    "id": 1,
    "category_id": 1,
    "name": "Bayam Organik",
    "slug": "bayam-organik",
    "description": "Bayam segar organik dari petani lokal. Kaya akan vitamin dan mineral yang baik untuk kesehatan.",
    "nutrition": "Vitamin A, C, K, Iron, Calcium, Fiber",
    "price": 15000,
    "stock": 100,
    "weight": 250,
    "image": "/storage/products/bayam.jpg",
    "healthy_score": 95,
    "status": true,
    "category": {
      "id": 1,
      "name": "Sayuran Segar",
      "slug": "sayuran-segar"
    },
    "related_products": [
      {
        "id": 2,
        "name": "Kangkung Segar",
        "slug": "kangkung-segar",
        "price": 12000,
        "image": "/storage/products/kangkung.jpg",
        "healthy_score": 90
      }
    ],
    "created_at": "2026-09-01T10:00:00.000000Z"
  }
}
```

**Error (404):**
```json
{
  "success": false,
  "message": "Produk tidak ditemukan"
}
```

---

### 5.3 Create Product (Admin)

**URL:** `/api/admin/products`  
**Method:** `POST`  
**Auth:** Yes (Admin)  
**Middleware:** `auth:sanctum`  
**Content-Type:** `multipart/form-data`

**Request Header:**
```
Authorization: Bearer {admin_token}
```

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| category_id | integer | Yes | ID kategori |
| name | string | Yes | Nama produk (3-255 karakter) |
| description | string | Yes | Deskripsi produk |
| nutrition | string | No | Info nutrisi |
| price | integer | Yes | Harga (minimal 0) |
| stock | integer | Yes | Stok (minimal 0) |
| weight | integer | Yes | Berat dalam gram |
| image | file | Yes | Gambar produk (jpg/png, max 2MB) |
| healthy_score | integer | No | Skor sehat 0-100 (default: 80) |
| status | boolean | No | Status aktif (default: true) |

**Request Example (form-data):**
```
category_id: 1
name: Bayam Organik
description: Bayam segar organik
nutrition: Vitamin A, C, K
price: 15000
stock: 100
weight: 250
healthy_score: 95
status: true
image: [file]
```

**Response (201):**
```json
{
  "success": true,
  "message": "Produk berhasil dibuat",
  "data": {
    "id": 1,
    "category_id": 1,
    "name": "Bayam Organik",
    "slug": "bayam-organik",
    "description": "Bayam segar organik",
    "nutrition": "Vitamin A, C, K",
    "price": 15000,
    "stock": 100,
    "weight": 250,
    "image": "/storage/products/bayam.jpg",
    "healthy_score": 95,
    "status": true,
    "created_at": "2026-09-01T10:00:00.000000Z"
  }
}
```

---

### 5.4 Update Product (Admin)

**URL:** `/api/admin/products/{id}`  
**Method:** `PUT`  
**Auth:** Yes (Admin)  
**Middleware:** `auth:sanctum`  
**Content-Type:** `multipart/form-data`

**Request Header:**
```
Authorization: Bearer {admin_token}
```

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | ID produk |

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| category_id | integer | No | ID kategori |
| name | string | No | Nama produk |
| description | string | No | Deskripsi produk |
| nutrition | string | No | Info nutrisi |
| price | integer | No | Harga |
| stock | integer | No | Stok |
| weight | integer | No | Berat dalam gram |
| image | file | No | Gambar baru (opsional) |
| healthy_score | integer | No | Skor sehat 0-100 |
| status | boolean | No | Status aktif |

**Response (200):**
```json
{
  "success": true,
  "message": "Produk berhasil diperbarui",
  "data": {
    "id": 1,
    "category_id": 1,
    "name": "Bayam Organik Premium",
    "slug": "bayam-organik-premium",
    "price": 18000,
    "updated_at": "2026-09-01T12:00:00.000000Z"
  }
}
```

---

### 5.5 Delete Product (Admin)

**URL:** `/api/admin/products/{id}`  
**Method:** `DELETE`  
**Auth:** Yes (Admin)  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {admin_token}
```

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | ID produk |

**Response (200):**
```json
{
  "success": true,
  "message": "Produk berhasil dihapus"
}
```

---

## 6. Addresses

### 6.1 Get All Addresses (User)

**URL:** `/api/addresses`  
**Method:** `GET`  
**Auth:** Yes  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "receiver_name": "John Doe",
      "phone": "081234567890",
      "province": "DKI Jakarta",
      "city": "Jakarta Selatan",
      "district": "Kebayoran Baru",
      "village": "Melawai",
      "postal_code": "12160",
      "full_address": "Jl. Melawai Raya No. 10",
      "is_default": true,
      "created_at": "2026-09-01T10:00:00.000000Z"
    }
  ]
}
```

---

### 6.2 Get Address by ID (User)

**URL:** `/api/addresses/{id}`  
**Method:** `GET`  
**Auth:** Yes  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {token}
```

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | ID alamat |

**Response (200):**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": {
    "id": 1,
    "user_id": 1,
    "receiver_name": "John Doe",
    "phone": "081234567890",
    "province": "DKI Jakarta",
    "city": "Jakarta Selatan",
    "district": "Kebayoran Baru",
    "village": "Melawai",
    "postal_code": "12160",
    "full_address": "Jl. Melawai Raya No. 10",
    "is_default": true
  }
}
```

---

### 6.3 Create Address (User)

**URL:** `/api/addresses`  
**Method:** `POST`  
**Auth:** Yes  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {token}
```

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| receiver_name | string | Yes | Nama penerima |
| phone | string | Yes | Nomor telepon (max 20) |
| province | string | Yes | Provinsi (max 100) |
| city | string | Yes | Kota (max 100) |
| district | string | Yes | Kecamatan (max 100) |
| village | string | Yes | Kelurahan (max 100) |
| postal_code | string | Yes | Kode pos (max 10) |
| full_address | string | Yes | Alamat lengkap |
| is_default | boolean | No | Alamat default (default: false) |

**Request Example:**
```json
{
  "receiver_name": "John Doe",
  "phone": "081234567890",
  "province": "DKI Jakarta",
  "city": "Jakarta Selatan",
  "district": "Kebayoran Baru",
  "village": "Melawai",
  "postal_code": "12160",
  "full_address": "Jl. Melawai Raya No. 10",
  "is_default": true
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Alamat berhasil dibuat",
  "data": {
    "id": 1,
    "user_id": 1,
    "receiver_name": "John Doe",
    "phone": "081234567890",
    "province": "DKI Jakarta",
    "city": "Jakarta Selatan",
    "district": "Kebayoran Baru",
    "village": "Melawai",
    "postal_code": "12160",
    "full_address": "Jl. Melawai Raya No. 10",
    "is_default": true,
    "created_at": "2026-09-01T10:00:00.000000Z"
  }
}
```

---

### 6.4 Update Address (User)

**URL:** `/api/addresses/{id}`  
**Method:** `PUT`  
**Auth:** Yes  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {token}
```

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | ID alamat |

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| receiver_name | string | No | Nama penerima |
| phone | string | No | Nomor telepon |
| province | string | No | Provinsi |
| city | string | No | Kota |
| district | string | No | Kecamatan |
| village | string | No | Kelurahan |
| postal_code | string | No | Kode pos |
| full_address | string | No | Alamat lengkap |
| is_default | boolean | No | Alamat default |

**Response (200):**
```json
{
  "success": true,
  "message": "Alamat berhasil diperbarui",
  "data": {
    "id": 1,
    "receiver_name": "John Doe Updated",
    "phone": "081234567891",
    "updated_at": "2026-09-01T12:00:00.000000Z"
  }
}
```

---

### 6.5 Delete Address (User)

**URL:** `/api/addresses/{id}`  
**Method:** `DELETE`  
**Auth:** Yes  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {token}
```

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | ID alamat |

**Response (200):**
```json
{
  "success": true,
  "message": "Alamat berhasil dihapus"
}
```

---

## 7. Cart

### 7.1 Get Cart (User)

**URL:** `/api/cart`  
**Method:** `GET`  
**Auth:** Yes  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": {
    "id": 1,
    "user_id": 1,
    "items": [
      {
        "id": 1,
        "product_id": 1,
        "quantity": 2,
        "product": {
          "id": 1,
          "name": "Bayam Organik",
          "slug": "bayam-organik",
          "price": 15000,
          "image": "/storage/products/bayam.jpg",
          "stock": 100,
          "weight": 250
        },
        "subtotal": 30000
      }
    ],
    "total_items": 2,
    "total_price": 30000,
    "total_weight": 500
  }
}
```

---

### 7.2 Add Item to Cart (User)

**URL:** `/api/cart/items`  
**Method:** `POST`  
**Auth:** Yes  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {token}
```

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| product_id | integer | Yes | ID produk |
| quantity | integer | Yes | Jumlah (minimal 1) |

**Request Example:**
```json
{
  "product_id": 1,
  "quantity": 2
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Produk berhasil ditambahkan ke keranjang",
  "data": {
    "id": 1,
    "product_id": 1,
    "quantity": 2,
    "product": {
      "id": 1,
      "name": "Bayam Organik",
      "price": 15000
    },
    "subtotal": 30000
  }
}
```

**Error (422):**
```json
{
  "success": false,
  "message": "Stok tidak mencukupi"
}
```

---

### 7.3 Update Cart Item (User)

**URL:** `/api/cart/items/{id}`  
**Method:** `PUT`  
**Auth:** Yes  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {token}
```

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | ID cart item |

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| quantity | integer | Yes | Jumlah baru (minimal 1) |

**Request Example:**
```json
{
  "quantity": 5
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Keranjang berhasil diperbarui",
  "data": {
    "id": 1,
    "product_id": 1,
    "quantity": 5,
    "subtotal": 75000
  }
}
```

---

### 7.4 Remove Cart Item (User)

**URL:** `/api/cart/items/{id}`  
**Method:** `DELETE`  
**Auth:** Yes  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {token}
```

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | ID cart item |

**Response (200):**
```json
{
  "success": true,
  "message": "Produk berhasil dihapus dari keranjang"
}
```

---

## 8. Orders

### 8.1 Get All Orders (User)

**URL:** `/api/orders`  
**Method:** `GET`  
**Auth:** Yes  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {token}
```

**Query Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| page | integer | No | Nomor halaman (default: 1) |
| per_page | integer | No | Item per halaman (default: 12) |
| status | string | No | Filter status (waiting_payment, processed, packing, shipping, completed, cancelled) |
| sort_by | string | No | Field sorting (created_at, total) |
| sort_order | string | No | ASC atau DESC (default: DESC) |

**Response (200):**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": [
    {
      "id": 1,
      "order_number": "ORD-20260901-0001",
      "subtotal": 30000,
      "shipping_cost": 10000,
      "discount": 0,
      "total": 40000,
      "payment_method": "bank_transfer",
      "status": "waiting_payment",
      "delivery_schedule": "2026-09-02T10:00:00.000000Z",
      "items_count": 2,
      "created_at": "2026-09-01T10:00:00.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 12,
    "total": 30
  }
}
```

---

### 8.2 Get Order by ID (User)

**URL:** `/api/orders/{id}`  
**Method:** `GET`  
**Auth:** Yes  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {token}
```

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | ID order |

**Response (200):**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": {
    "id": 1,
    "order_number": "ORD-20260901-0001",
    "user_id": 1,
    "address_id": 1,
    "courier_id": 1,
    "subtotal": 30000,
    "shipping_cost": 10000,
    "discount": 0,
    "total": 40000,
    "payment_method": "bank_transfer",
    "delivery_schedule": "2026-09-02T10:00:00.000000Z",
    "status": "waiting_payment",
    "address": {
      "id": 1,
      "receiver_name": "John Doe",
      "phone": "081234567890",
      "province": "DKI Jakarta",
      "city": "Jakarta Selatan",
      "district": "Kebayoran Baru",
      "village": "Melawai",
      "postal_code": "12160",
      "full_address": "Jl. Melawai Raya No. 10"
    },
    "courier": {
      "id": 1,
      "name": "JNE",
      "service": "Reguler"
    },
    "items": [
      {
        "id": 1,
        "product_id": 1,
        "price": 15000,
        "quantity": 2,
        "subtotal": 30000,
        "product": {
          "id": 1,
          "name": "Bayam Organik",
          "image": "/storage/products/bayam.jpg"
        }
      }
    ],
    "payment": {
      "id": 1,
      "payment_method": "bank_transfer",
      "amount": 40000,
      "payment_status": "pending",
      "paid_at": null
    },
    "trackings": [
      {
        "id": 1,
        "status": "waiting_payment",
        "note": "Menunggu pembayaran",
        "created_at": "2026-09-01T10:00:00.000000Z"
      }
    ],
    "created_at": "2026-09-01T10:00:00.000000Z"
  }
}
```

---

### 8.3 Create Order (User)

**URL:** `/api/orders`  
**Method:** `POST`  
**Auth:** Yes  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {token}
```

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| address_id | integer | Yes | ID alamat pengiriman |
| courier_id | integer | Yes | ID kurir |
| payment_method | string | Yes | Metode bayar (bank_transfer, e_wallet, cod) |
| delivery_schedule | string | Yes | Jadwal pengiriman (Y-m-d H:i:s) |
| voucher_code | string | No | Kode voucher (opsional) |

**Request Example:**
```json
{
  "address_id": 1,
  "courier_id": 1,
  "payment_method": "bank_transfer",
  "delivery_schedule": "2026-09-02T10:00:00",
  "voucher_code": "HEALTH10"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Order berhasil dibuat",
  "data": {
    "id": 1,
    "order_number": "ORD-20260901-0001",
    "subtotal": 30000,
    "shipping_cost": 10000,
    "discount": 3000,
    "total": 37000,
    "payment_method": "bank_transfer",
    "status": "waiting_payment",
    "delivery_schedule": "2026-09-02T10:00:00.000000Z",
    "items": [
      {
        "product_name": "Bayam Organik",
        "price": 15000,
        "quantity": 2,
        "subtotal": 30000
      }
    ],
    "payment": {
      "amount": 37000,
      "payment_status": "pending"
    },
    "created_at": "2026-09-01T10:00:00.000000Z"
  }
}
```

**Error (422):**
```json
{
  "success": false,
  "message": "Validasi gagal",
  "errors": {
    "cart": ["Keranjang kosong"],
    "stock": ["Produk X stok tidak mencukupi"]
  }
}
```

---

### 8.4 Cancel Order (User)

**URL:** `/api/orders/{id}/cancel`  
**Method:** `PUT`  
**Auth:** Yes  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {token}
```

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | ID order |

**Response (200):**
```json
{
  "success": true,
  "message": "Order berhasil dibatalkan",
  "data": {
    "id": 1,
    "order_number": "ORD-20260901-0001",
    "status": "cancelled"
  }
}
```

**Error (422):**
```json
{
  "success": false,
  "message": "Order tidak dapat dibatalkan"
}
```

---

## 9. Order Tracking

### 9.1 Get Order Tracking (User)

**URL:** `/api/orders/{id}/tracking`  
**Method:** `GET`  
**Auth:** Yes  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {token}
```

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | ID order |

**Response (200):**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": {
    "order_number": "ORD-20260901-0001",
    "current_status": "shipping",
    "trackings": [
      {
        "id": 1,
        "status": "waiting_payment",
        "note": "Menunggu pembayaran",
        "created_at": "2026-09-01T10:00:00.000000Z"
      },
      {
        "id": 2,
        "status": "processed",
        "note": "Order sedang diproses",
        "created_at": "2026-09-01T14:00:00.000000Z"
      },
      {
        "id": 3,
        "status": "packing",
        "note": "Barang sedang dikemas",
        "created_at": "2026-09-01T16:00:00.000000Z"
      },
      {
        "id": 4,
        "status": "shipping",
        "note": "Barang dalam perjalanan",
        "created_at": "2026-09-02T08:00:00.000000Z"
      }
    ]
  }
}
```

---

### 9.2 Add Order Tracking (Admin)

**URL:** `/api/admin/orders/{id}/tracking`  
**Method:** `POST`  
**Auth:** Yes (Admin)  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {admin_token}
```

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | ID order |

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| status | string | Yes | Status baru |
| note | string | No | Catatan |

**Status Options:**
- `waiting_payment` - Menunggu pembayaran
- `processed` - Sedang diproses
- `packing` - Sedang dikemas
- `shipping` - Dalam perjalanan
- `completed` - Selesai
- `cancelled` - Dibatalkan

**Request Example:**
```json
{
  "status": "shipping",
  "note": "Barang sudah dikirim dengan nomor resi JNE123456789"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Tracking berhasil ditambahkan",
  "data": {
    "id": 5,
    "order_id": 1,
    "status": "shipping",
    "note": "Barang sudah dikirim dengan nomor resi JNE123456789",
    "created_at": "2026-09-02T08:00:00.000000Z"
  }
}
```

---

## 10. Payments

### 10.1 Create Payment (User)

**URL:** `/api/payments`  
**Method:** `POST`  
**Auth:** Yes  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {token}
```

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| order_id | integer | Yes | ID order |
| payment_method | string | Yes | Metode bayar (bank_transfer, e_wallet, credit_card) |

**Request Example:**
```json
{
  "order_id": 1,
  "payment_method": "bank_transfer"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Payment berhasil dibuat",
  "data": {
    "payment_id": 1,
    "order_number": "ORD-20260901-0001",
    "amount": 37000,
    "payment_method": "bank_transfer",
    "payment_status": "pending",
    "snap_token": "mid-snap-token-abc123...",
    "redirect_url": "https://app.sandbox.midtrans.com/snap/v2/abc123/payment"
  }
}
```

**Note:** `snap_token` digunakan untuk integrasi Midtrans di frontend.

---

### 10.2 Payment Callback (Midtrans Webhook)

**URL:** `/api/payments/callback`  
**Method:** `POST`  
**Auth:** No  
**Note:** Endpoint ini dipanggil oleh server Midtrans

**Request Body (dari Midtrans):**

```json
{
  "order_id": "ORD-20260901-0001",
  "status_code": "200",
  "transaction_status": "settlement",
  "fraud_status": "accept",
  "payment_type": "bank_transfer",
  "bank": "bca",
  "va_number": "1234567890",
  "gross_amount": "37000.00"
}
```

**Transaction Status Mapping:**

| Midtrans Status | Internal Status |
|-----------------|-----------------|
| capture / settlement | paid |
| pending | pending |
| deny / expire / cancel | failed |

**Response (200):**
```json
{
  "success": true,
  "message": "Callback processed"
}
```

---

### 10.3 Get Payment Status (User)

**URL:** `/api/payments/{order_id}`  
**Method:** `GET`  
**Auth:** Yes  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {token}
```

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| order_id | integer | ID order |

**Response (200):**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": {
    "id": 1,
    "order_id": 1,
    "order_number": "ORD-20260901-0001",
    "payment_method": "bank_transfer",
    "amount": 37000,
    "payment_status": "paid",
    "paid_at": "2026-09-01T12:00:00.000000Z",
    "created_at": "2026-09-01T10:00:00.000000Z"
  }
}
```

---

## 11. Couriers

### 11.1 Get All Couriers (Admin)

**URL:** `/api/couriers`  
**Method:** `GET`  
**Auth:** Yes (Admin)  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {admin_token}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": [
    {
      "id": 1,
      "name": "JNE",
      "service": "Reguler",
      "created_at": "2026-09-01T10:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "JNE",
      "service": "YES",
      "created_at": "2026-09-01T10:00:00.000000Z"
    },
    {
      "id": 3,
      "name": "J&T",
      "service": "Reguler",
      "created_at": "2026-09-01T10:00:00.000000Z"
    }
  ]
}
```

---

### 11.2 Create Courier (Admin)

**URL:** `/api/admin/couriers`  
**Method:** `POST`  
**Auth:** Yes (Admin)  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {admin_token}
```

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| name | string | Yes | Nama kurir (max 100) |
| service | string | Yes | Layanan (max 100) |

**Request Example:**
```json
{
  "name": "JNE",
  "service": "Reguler"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Kurir berhasil dibuat",
  "data": {
    "id": 1,
    "name": "JNE",
    "service": "Reguler",
    "created_at": "2026-09-01T10:00:00.000000Z"
  }
}
```

---

### 11.3 Update Courier (Admin)

**URL:** `/api/admin/couriers/{id}`  
**Method:** `PUT`  
**Auth:** Yes (Admin)  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {admin_token}
```

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | ID kurir |

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| name | string | No | Nama kurir |
| service | string | No | Layanan |

**Response (200):**
```json
{
  "success": true,
  "message": "Kurir berhasil diperbarui",
  "data": {
    "id": 1,
    "name": "JNE",
    "service": "Express",
    "updated_at": "2026-09-01T12:00:00.000000Z"
  }
}
```

---

### 11.4 Delete Courier (Admin)

**URL:** `/api/admin/couriers/{id}`  
**Method:** `DELETE`  
**Auth:** Yes (Admin)  
**Middleware:** `auth:sanctum`

**Request Header:**
```
Authorization: Bearer {admin_token}
```

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | ID kurir |

**Response (200):**
```json
{
  "success": true,
  "message": "Kurir berhasil dihapus"
}
```

---

## 12. Error Codes Reference

| Code | Status | Description |
|------|--------|-------------|
| 400 | Bad Request | Permintaan tidak valid |
| 401 | Unauthenticated | Token tidak valid atau tidak ada |
| 403 | Forbidden | Tidak memiliki akses |
| 404 | Not Found | Data tidak ditemukan |
| 422 | Unprocessable Entity | Validasi gagal |
| 429 | Too Many Requests | Rate limit terlampaui |
| 500 | Internal Server Error | Kesalahan server |

### Common Validation Errors

| Field | Error | Description |
|-------|-------|-------------|
| email | required | Email wajib diisi |
| email | email | Format email tidak valid |
| email | unique | Email sudah terdaftar |
| password | required | Password wajib diisi |
| password | min | Password minimal 8 karakter |
| password | confirmed | Konfirmasi password tidak cocok |
| name | required | Nama wajib diisi |
| name | min | Nama minimal 3 karakter |
| price | required | Harga wajib diisi |
| price | integer | Harga harus berupa angka |
| stock | required | Stok wajib diisi |
| stock | min | Stok minimal 0 |

---

## 13. Database Schema

### ERD (Entity Relationship Diagram)

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   users     │     │ categories  │     │  couriers   │
├─────────────┤     ├─────────────┤     ├─────────────┤
│ id (PK)     │     │ id (PK)     │     │ id (PK)     │
│ name        │     │ name        │     │ name        │
│ email       │     │ slug        │     │ service     │
│ password    │     │ created_at  │     │ created_at  │
│ role        │     │ updated_at  │     │ updated_at  │
│ created_at  │     └─────────────┘     └─────────────┘
│ updated_at  │           │                    │
└─────────────┘           │                    │
      │                   │                    │
      │           ┌─────────────┐              │
      │           │  products   │              │
      │           ├─────────────┤              │
      │           │ id (PK)     │              │
      │           │ category_id │──┐           │
      │           │ name        │  │           │
      │           │ slug        │  │           │
      │           │ description │  │           │
      │           │ nutrition   │  │           │
      │           │ price       │  │           │
      │           │ stock       │  │           │
      │           │ weight      │  │           │
      │           │ image       │  │           │
      │           │ healthy_score│ │           │
      │           │ status      │  │           │
      │           │ created_at  │  │           │
      │           │ updated_at  │  │           │
      │           └─────────────┘  │           │
      │                  │         │           │
      │                  │         │           │
      │     ┌────────────┴─────────┴───────────┴─────────┐
      │     │                                            │
      │     │                                            │
┌─────┴─────────┐     ┌─────────────┐     ┌─────────────┐
│  addresses    │     │   carts     │     │   orders    │
├─────────────┤     ├─────────────┤     ├─────────────┤
│ id (PK)     │     │ id (PK)     │     │ id (PK)     │
│ user_id (FK)│     │ user_id (FK)│     │ user_id (FK)│
│ receiver_name│    │ created_at  │     │ address_id  │
│ phone       │     │ updated_at  │     │ courier_id  │
│ province    │     └─────────────┘     │ order_number│
│ city        │           │             │ subtotal    │
│ district    │           │             │ shipping_cost│
│ village     │     ┌─────────────┐     │ discount    │
│ postal_code │     │ cart_items  │     │ total       │
│ full_address│     ├─────────────┤     │ payment_method│
│ is_default  │     │ id (PK)     │     │ delivery_schedule│
│ created_at  │     │ cart_id (FK)│     │ status      │
│ updated_at  │     │ product_id  │     │ created_at  │
└─────────────┘     │ quantity    │     │ updated_at  │
                    └─────────────┘     └─────────────┘
                          │                   │
                          │                   │
                    ┌─────────────┐     ┌─────┴─────────┐
                    │order_items  │     │   payments    │
                    ├─────────────┤     ├─────────────┤
                    │ id (PK)     │     │ id (PK)     │
                    │ order_id(FK)│     │ order_id(FK)│
                    │ product_id  │     │ payment_method│
                    │ price       │     │ amount      │
                    │ quantity    │     │ payment_status│
                    │ subtotal    │     │ paid_at     │
                    └─────────────┘     │ created_at  │
                                        │ updated_at  │
                    ┌─────────────┐     └─────────────┘
                    │order_tracking│
                    ├─────────────┤
                    │ id (PK)     │
                    │ order_id(FK)│
                    │ status      │
                    │ note        │
                    │ created_at  │
                    └─────────────┘
```

### Table Relationships

| Table | Foreign Key | References | Relationship |
|-------|-------------|------------|--------------|
| addresses | user_id | users.id | belongsTo User |
| products | category_id | categories.id | belongsTo Category |
| carts | user_id | users.id | belongsTo User |
| cart_items | cart_id | carts.id | belongsTo Cart |
| cart_items | product_id | products.id | belongsTo Product |
| orders | user_id | users.id | belongsTo User |
| orders | address_id | addresses.id | belongsTo Address |
| orders | courier_id | couriers.id | belongsTo Courier |
| order_items | order_id | orders.id | belongsTo Order |
| order_items | product_id | products.id | belongsTo Product |
| payments | order_id | orders.id | belongsTo Order |
| order_tracking | order_id | orders.id | belongsTo Order |

---

## 14. Flow Diagrams

### Checkout Flow

```
┌─────────────────────────────────────────────────────────────┐
│                      CHECKOUT FLOW                         │
└─────────────────────────────────────────────────────────────┘

    ┌──────────┐
    │   User   │
    └────┬─────┘
         │
         ▼
┌────────────────┐
│  Browse Product │
└────────┬───────┘
         │
         ▼
┌────────────────┐
│  Add to Cart   │──── POST /api/cart/items
└────────┬───────┘
         │
         ▼
┌────────────────┐
│  View Cart     │──── GET /api/cart
└────────┬───────┘
         │
         ▼
┌────────────────┐
│ Select Address │──── GET /api/addresses
└────────┬───────┘
         │
         ▼
┌────────────────┐
│ Select Courier │──── GET /api/couriers
└────────┬───────┘
         │
         ▼
┌────────────────┐
│  Review Order  │
│  - Subtotal    │
│  - Shipping    │
│  - Discount    │
│  - Total       │
└────────┬───────┘
         │
         ▼
┌────────────────┐
│ Create Order   │──── POST /api/orders
└────────┬───────┘
         │
         ▼
┌────────────────┐
│  Redirect to   │
│  Payment Page  │
└────────┬───────┘
         │
         ▼
┌────────────────┐
│ Select Payment │──── POST /api/payments
│ Method         │     (get snap_token)
└────────┬───────┘
         │
         ▼
┌────────────────┐
│  Midtrans      │
│  Payment PopUp │
└────────┬───────┘
         │
         ▼
┌────────────────┐
│ Payment Success│
│ - paid_at set  │
│ - status: paid │
└────────┬───────┘
         │
         ▼
┌────────────────┐
│ Order Status   │
│: waiting_payment│
│ → processed    │
└────────────────┘
```

### Payment Flow (Midtrans Integration)

```
┌─────────────────────────────────────────────────────────────┐
│                   PAYMENT FLOW (MIDTRANS)                   │
└─────────────────────────────────────────────────────────────┘

    ┌──────────┐
    │   User   │
    └────┬─────┘
         │
         │ Click "Bayar"
         ▼
┌─────────────────────────────────────────────────────────────┐
│                   BACKEND PROCESSING                        │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  1. POST /api/payments                                      │
│     - Validate order_id & payment_method                    │
│     - Check order status (must be waiting_payment)          │
│                                                             │
│  2. Create Payment Record                                   │
│     - payment_status: pending                               │
│     - amount: order.total                                   │
│                                                             │
│  3. Call Midtrans API                                       │
│     - Create transaction                                    │
│     - Get snap_token                                        │
│                                                             │
│  4. Return snap_token to Frontend                           │
│                                                             │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                   FRONTEND PROCESSING                       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  1. Initialize Midtrans Snap                                 │
│     - window.snap.pay(snap_token)                           │
│                                                             │
│  2. Show Payment PopUp                                       │
│     - User selects payment method                            │
│     - User completes payment                                 │
│                                                             │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                   MIDTRANS CALLBACK                          │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  1. POST /api/payments/callback                              │
│     (Dipanggil oleh server Midtrans)                        │
│                                                             │
│  2. Verify Signature                                         │
│     - Validate hash signature                                │
│                                                             │
│  3. Update Payment Status                                    │
│     - settlement/capture → paid                              │
│     - pending → pending                                      │
│     - deny/expire/cancel → failed                            │
│                                                             │
│  4. Update Order Status                                      │
│     - If payment paid → order: processed                     │
│                                                             │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                   USER NOTIFICATION                          │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  1. Frontend polls GET /api/payments/{order_id}              │
│     OR use WebSocket/Laravel Echo                            │
│                                                             │
│  2. Show payment success page                                │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 15. Pagination & Sorting Guide

### Pagination

**Default Configuration:**

| Parameter | Default | Description |
|-----------|---------|-------------|
| page | 1 | Nomor halaman awal |
| per_page | 12 | Item per halaman |

**Request:**
```
GET /api/products?page=1&per_page=12
```

**Response:**
```json
{
  "success": true,
  "data": [...],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 12,
    "total": 60
  }
}
```

### Sorting

**Supported Fields:**

| Endpoint | Sortable Fields |
|----------|-----------------|
| Products | name, price, healthy_score, created_at |
| Orders | created_at, total |
| Categories | name, created_at |

**Request:**
```
GET /api/products?sort_by=price&sort_order=asc
GET /api/products?sort_by=healthy_score&sort_order=desc
```

**Parameters:**

| Parameter | Values | Default |
|-----------|--------|---------|
| sort_by | Field name | created_at |
| sort_order | asc, desc | desc |

### Filtering

**Products Filter:**

| Parameter | Type | Description |
|-----------|------|-------------|
| category_id | integer | Filter by kategori |
| min_price | integer | Harga minimum |
| max_price | integer | Harga maksimum |
| min_healthy_score | integer | Skor sehat minimum |
| max_healthy_score | integer | Skor sehat maksimum |
| status | boolean | Status aktif |
| search | string | Pencarian nama/deskripsi |

**Request:**
```
GET /api/products?category_id=1&min_price=10000&max_price=50000&status=true
GET /api/products?search=bayam&sort_by=healthy_score&sort_order=desc
```

**Orders Filter:**

| Parameter | Type | Description |
|-----------|------|-------------|
| status | string | Filter by status |

**Request:**
```
GET /api/orders?status=waiting_payment
```

---

## 16. File Upload Guide

### Image Requirements

| Property | Requirement |
|----------|-------------|
| Allowed Types | jpg, jpeg, png |
| Max Size | 2MB |
| Recommended Size | 800x800 pixels |
| Storage Path | storage/app/public/products |

### Upload Process

**Step 1: Prepare Request**
```javascript
const formData = new FormData();
formData.append('image', fileInput.files[0]);
formData.append('name', 'Bayam Organik');
formData.append('category_id', '1');
// ... other fields
```

**Step 2: Send Request**
```javascript
fetch('/api/admin/products', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
    // Note: Do NOT set Content-Type, browser will set it automatically with boundary
  },
  body: formData
});
```

**Step 3: Access Uploaded Image**
```
Image URL: http://localhost:8000/storage/products/bayam.jpg
```

**Note:** Pastikan sudah menjalankan:
```bash
php artisan storage:link
```

### Image Processing (Optional)

Jika ingin menambahkan image processing, gunakan Intervention Image:

```bash
composer require intervention/image
```

```php
use Intervention\Image\Facades\Image;

// Resize
$image = Image::make($request->file('image'));
$image->resize(800, 800);
$image->save(storage_path('app/public/products/' . $filename));
```

---

## Appendix

### A. Status Constants

**Order Status:**
```php
const STATUS_WAITING_PAYMENT = 'waiting_payment';
const STATUS_PROCESSED = 'processed';
const STATUS_PACKING = 'packing';
const STATUS_SHIPPING = 'shipping';
const STATUS_COMPLETED = 'completed';
const STATUS_CANCELLED = 'cancelled';
```

**Payment Status:**
```php
const PAYMENT_PENDING = 'pending';
const PAYMENT_PAID = 'paid';
const PAYMENT_FAILED = 'failed';
```

**User Roles:**
```php
const ROLE_CUSTOMER = 'customer';
const ROLE_ADMIN = 'admin';
```

### B. API Routes Summary

```php
// Public Routes
Route::post('/auth/register', [UserController::class, 'register']);
Route::post('/auth/login', [UserController::class, 'login']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);
Route::post('/payments/callback', [PaymentController::class, 'callback']);

// Protected Routes (Auth)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [UserController::class, 'logout']);
    Route::get('/user', [UserController::class, 'profile']);
    Route::put('/user', [UserController::class, 'update']);
    
    // Addresses
    Route::apiResource('addresses', AddressController::class);
    
    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/items', [CartItemController::class, 'store']);
    Route::put('/cart/items/{id}', [CartItemController::class, 'update']);
    Route::delete('/cart/items/{id}', [CartItemController::class, 'destroy']);
    
    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::put('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    
    // Order Tracking
    Route::get('/orders/{id}/tracking', [OrderTrackingController::class, 'index']);
    
    // Payments
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::get('/payments/{order_id}', [PaymentController::class, 'show']);
});

// Admin Routes
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('categories', AdminCategoryController::class);
    Route::apiResource('products', AdminProductController::class);
    Route::apiResource('couriers', CourierController::class);
    Route::post('/orders/{id}/tracking', [AdminOrderTrackingController::class, 'store']);
});
```

### C. Postman Collection Structure

```
Wello_BE API/
├── Auth/
│   ├── Register
│   ├── Login
│   └── Logout
├── User/
│   ├── Get Profile
│   └── Update Profile
├── Categories/
│   ├── Get All (Public)
│   ├── Get by Slug (Public)
│   ├── Create (Admin)
│   ├── Update (Admin)
│   └── Delete (Admin)
├── Products/
│   ├── Get All (Public)
│   ├── Get by Slug (Public)
│   ├── Create (Admin)
│   ├── Update (Admin)
│   └── Delete (Admin)
├── Addresses/
│   ├── Get All
│   ├── Get by ID
│   ├── Create
│   ├── Update
│   └── Delete
├── Cart/
│   ├── Get Cart
│   ├── Add Item
│   ├── Update Item
│   └── Remove Item
├── Orders/
│   ├── Get All
│   ├── Get by ID
│   ├── Create
│   └── Cancel
├── Order Tracking/
│   └── Get Tracking
├── Payments/
│   ├── Create Payment
│   └── Get Payment Status
└── Couriers (Admin)/
    ├── Get All
    ├── Create
    ├── Update
    └── Delete
```

---

**Document Version:** 1.0  
**Last Updated:** September 2026  
**Author:** Wello_BE Team
