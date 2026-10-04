# Inventori

Sistem inventaris dan pengelolaan aset internal yang dibangun dengan Laravel 12, Vite, Bootstrap, dan AdminLTE.

## Fitur

- Manajemen data inventaris dan aset
- Input data barang, lokasi, unit, serta karyawan
- Proses inspeksi dan monitoring
- Export data ke Excel/PDF
- Dashboard admin dan akses berbasis role
- Integrasi tampilan frontend dengan Vite dan AdminLTE

## Stack Teknologi

- PHP 8.2+
- Laravel 12
- Composer
- Vite + Node.js
- Bootstrap 5 + AdminLTE
- MySQL / database Laravel

## Persyaratan

Sebelum menjalankan aplikasi, pastikan:

- PHP 8.2 atau lebih tinggi terpasang
- Composer terpasang
- Node.js dan npm terpasang
- Database MySQL tersedia

## Instalasi

1. Clone repository.
2. Install dependency PHP:

   ```bash
   composer install
   ```

3. Install dependency frontend:

   ```bash
   npm install
   ```

4. Salin file environment:

   ```bash
   copy .env.example .env
   ```

5. Konfigurasi database pada file `.env`.

6. Jalankan migrasi database:

   ```bash
   php artisan migrate
   ```

7. Jalankan aplikasi:

   ```bash
   php artisan serve
   ```

8. Jalankan frontend build/dev:

   ```bash
   npm run dev
   ```

## Build Frontend

Untuk build production:

```bash
npm run build
```

## Struktur Umum

- `app/` : logika aplikasi, controller, model, dan service
- `resources/views/` : template Blade dan UI
- `routes/` : definisi route web dan API
- `database/migrations/` : struktur tabel database
- `public/` : aset yang dapat diakses langsung

## Catatan

Dokumen ini dibuat sebagai ringkasan awal untuk proyek Inventori. Untuk detail implementasi spesifik, lihat file konfigurasi Laravel, route, model, dan migration yang tersedia di repository.
