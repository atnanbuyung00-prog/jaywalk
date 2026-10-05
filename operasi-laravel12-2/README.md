# Sistem Jadwal Operasi RS Mutiara Aini — Laravel 12

## Versi 2
Versi ini menambahkan:
- Kalender FullCalendar dengan **drag & drop** jadwal.
- Pilihan ruang operasi **OK 1 / OK 2 / OK 3**.
- Validasi bentrok otomatis selama 90 menit untuk **OK, operator, anestesi, dan tim bedah**. Jadwal berstatus Batal tidak dihitung sebagai bentrok.
- Tombol **Cetak / PDF** untuk jadwal berdasarkan tanggal; browser dapat memilih *Save as PDF*.
- Display TV tetap menggunakan layout visual RS Mutiara Aini; ditambah tombol fullscreen. Untuk benar-benar tanpa toolbar browser gunakan Chrome/Chromium kiosk mode.
- Perhitungan umur pasien otomatis dari tanggal lahir.
- Menu shift dan user/hak akses tetap tersedia.

## Login
Username: `admin`  
Password: `admin`

## Instalasi
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

URL:
- Admin: `http://127.0.0.1:8000/admin`
- Login: `http://127.0.0.1:8000/login`
- Display TV: `http://127.0.0.1:8000/display`
- Cetak/PDF: tersedia dari menu Jadwal Operasi.

## Mode TV tanpa toolbar
Chrome/Chromium di komputer TV dapat dijalankan dengan:
```bash
chromium --kiosk http://127.0.0.1:8000/display
```
Sesuaikan URL dengan IP server rumah sakit, misalnya `http://192.168.1.10:8000/display`.

## Demo UI
File `demo/admin.html` adalah demo statis halaman administrator. `demo/index.html` adalah demo display TV.
