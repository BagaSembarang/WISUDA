# Sistem Manajemen Wisuda

Aplikasi manajemen acara wisuda universitas berbasis PHP native dengan arsitektur MVC.

## Fitur Utama

1. **Manajemen Periode Wisuda**
   - Buat periode wisuda baru
   - Auto-generate tabel wisudawan per periode
   - Kelola status periode (draft, aktif, selesai)

2. **Manajemen Sesi Wisuda**
   - Tambah sesi per periode
   - Atur waktu, lokasi, dan kapasitas
   - Embedded Google Maps

3. **Manajemen Wisudawan**
   - Upload data dari Excel
   - Generate kode unik 4 digit otomatis
   - Kirim undangan via WhatsApp

4. **Presensi QR Code**
   - Presensi pengambilan toga (dengan TTD)
   - Presensi gladi bersih
   - Presensi hari-H (dengan TTD ijazah)
   - Presensi pengambilan konsumsi

5. **Undangan Digital**
   - Akses via kode unik
   - QR Code undangan
   - RSVP konfirmasi kehadiran
   - Denah kursi

6. **Dashboard LO**
   - Monitoring denah kehadiran
   - Scan undangan untuk cek lokasi kursi

7. **Laporan**
   - Laporan presensi
   - Laporan tanda tangan
   - Export PDF
   - Cetak kupon

## Teknologi

- PHP 7.4+
- MySQL 5.7+
- Bootstrap 5
- DataTables.js
- jQuery
- SweetAlert2
- Signature Pad
- HTML5 QR Code Scanner

## Instalasi

1. Clone/download repository ke folder `htdocs` atau `www`

2. Import database:
```sql
mysql -u root -p < database/schema.sql
```

3. Konfigurasi database di `app/config/database.php`

4. Install dependencies (manual):
   - Download SimpleXLSX dari https://github.com/shuchkin/simplexlsx
   - Download Endroid QR Code dari https://github.com/endroid/qr-code
   - Download FPDF dari http://www.fpdf.org/
   - Letakkan di folder `vendor/`

5. Akses aplikasi:
```
http://localhost/WISUDA/
```

6. Login default:
   - Username: `admin`
   - Password: `admin123`

## Struktur Folder

```
WISUDA/
├── app/
│   ├── config/          # Konfigurasi
│   ├── controllers/     # Controllers
│   ├── core/           # Core classes (Database, Model, Controller)
│   ├── helpers/        # Helper functions
│   ├── models/         # Models
│   └── views/          # Views
├── database/           # SQL schema
├── public/            # Assets (CSS, JS, images)
├── uploads/           # Upload files
├── vendor/            # Third-party libraries
├── .htaccess          # URL rewriting
└── index.php          # Entry point
```

## Catatan Penting

1. **Dynamic Tables**: Setiap periode wisuda akan membuat tabel baru dengan format `{tahun}_{periode}_t_wisudawan`

2. **QR Code**: Gunakan kode unik 4 digit untuk scan presensi

3. **Tanda Tangan Digital**: Menggunakan Signature Pad untuk TTD pengambilan toga dan ijazah

4. **Role System**:
   - Admin: Full access
   - LO: Monitoring denah dan scan undangan

## Pengembangan Lebih Lanjut

- Install library yang diperlukan (SimpleXLSX, Endroid QR Code, FPDF)
- Sesuaikan template undangan
- Tambah fitur export Excel
- Integrasi WhatsApp API untuk auto-send
- Tambah fitur feedback wisudawan

## Lisensi

MIT License

## Support

Untuk pertanyaan dan dukungan, silakan hubungi tim pengembang.

Login :
username : admin
Password : password

akses ke LO :
namafolder/index.php?url=lo/dashboard
