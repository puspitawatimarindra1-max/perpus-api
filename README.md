# Perpustakaan API

REST API Laravel untuk registrasi dan login token Sanctum, CRUD buku, serta dokumentasi interaktif OpenAPI melalui L5-Swagger.

## Persyaratan

- PHP 8.3 atau lebih baru (Laravel di project ini mendukung PHP 8.2+)
- Composer 2
- Ekstensi PHP `pdo_sqlite`, `mbstring`, dan `openssl`

## Menjalankan secara lokal

Jalankan dari folder project:

```powershell
composer install
if (!(Test-Path .env)) { Copy-Item .env.example .env }
# Jalankan hanya pada setup pertama atau jika APP_KEY masih kosong.
php artisan key:generate
if (!(Test-Path database\database.sqlite)) { New-Item -ItemType File database\database.sqlite }
php artisan migrate --seed
php artisan l5-swagger:generate
php artisan serve
```

Jika file `.env` sudah tersedia, jangan menyalinnya kembali. Pastikan `DB_CONNECTION=sqlite` dan `DB_DATABASE` menunjuk ke file `database/database.sqlite` di project ini. Secara default aplikasi memakai SQLite. Seeder membuat akun demo `admin@example.com` dengan password `password` dan 20 buku contoh. Kredensial tersebut hanya untuk development lokal.

API tersedia di `http://localhost:8000/api`; Swagger UI di `http://localhost:8000/api/documentation`. Untuk mengakses endpoint terlindungi, login dan kirim token sebagai `Authorization: Bearer <token>`. Atur `L5_SWAGGER_CONST_HOST` di `.env` jika host atau port aplikasi berbeda.

## Endpoint

| Method | Endpoint | Akses | Fungsi |
| --- | --- | --- | --- |
| POST | `/api/register` | Publik | Daftar akun dan terbitkan token |
| POST | `/api/login` | Publik | Login (maksimal 6 percobaan per menit) |
| GET | `/api/me` | Token | Profil pengguna |
| POST | `/api/logout` | Token | Cabut token aktif |
| GET | `/api/books` | Token | Daftar buku, paginasi, dan pencarian `search` |
| POST | `/api/books` | Token | Tambah buku |
| GET | `/api/books/{book}` | Token | Detail buku |
| PUT/PATCH | `/api/books/{book}` | Token | Ubah buku |
| DELETE | `/api/books/{book}` | Token | Hapus buku |

`per_page` menerima nilai 1–100 (default 10). Pencarian mencocokkan judul dan penulis. ISBN harus unik; `published_year` tidak boleh melebihi tahun berjalan dan `stock` tidak boleh negatif.

## Pengujian dan dokumentasi

```powershell
php artisan test
php artisan l5-swagger:generate
```

Test menggunakan SQLite in-memory agar tidak mengubah database lokal. Dokumentasi JSON yang dihasilkan tersimpan di `storage/api-docs/api-docs.json`.
