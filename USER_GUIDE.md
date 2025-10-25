# Panduan Penggunaan Sistem Manajemen Wisuda

## Untuk Admin

### 1. Login
- Akses: `http://localhost/WISUDA/`
- Username: `admin`
- Password: `admin123`

### 2. Membuat Periode Wisuda Baru

1. Klik menu **Periode Wisuda**
2. Klik tombol **Tambah Periode**
3. Isi form:
   - Nama Periode: "Wisuda Periode III Tahun 2025"
   - Tahun: 2025
   - Periode Ke: 3
   - Keterangan: (opsional)
4. Klik **Simpan**
5. Sistem akan otomatis membuat tabel `2025_3_t_wisudawan`

### 3. Menambah Sesi Wisuda

1. Klik **Detail** pada periode yang sudah dibuat
2. Klik tombol **Tambah Sesi**
3. Isi form:
   - Nama Sesi: "Sesi 1 - Fakultas Teknik"
   - Tanggal: pilih tanggal wisuda
   - Waktu Mulai: 08:00
   - Waktu Selesai: 12:00
   - Lokasi: "Gedung Auditorium Universitas"
   - Latitude & Longitude: (opsional, untuk maps)
   - Kapasitas: 500
4. Klik **Simpan**

### 4. Upload Data Wisudawan

1. Dari detail periode, klik **Data Wisudawan** pada sesi
2. Klik **Upload Excel**
3. Download template Excel jika belum punya
4. Isi data wisudawan sesuai format:
   - Kolom 1: NIM
   - Kolom 2: Nama Lengkap
   - Kolom 3: Program Studi
   - Kolom 4: Fakultas
   - Kolom 5: IPK
   - Kolom 6: Predikat
   - Kolom 7: Email
   - Kolom 8: No HP (format: 08xxx atau 628xxx)
   - Kolom 9: Ukuran Toga (S/M/L/XL/XXL)
   - Kolom 10: Nomor Kursi
5. Upload file Excel
6. Sistem akan generate kode unik 4 digit untuk setiap wisudawan

### 5. Mengirim Undangan via WhatsApp

1. Dari daftar wisudawan, klik icon WhatsApp
2. Browser akan membuka WhatsApp Web
3. Pesan otomatis sudah terisi dengan:
   - Nama wisudawan
   - Kode unik
   - Link undangan digital
4. Klik Send

### 6. Melakukan Presensi

#### Presensi Pengambilan Toga:
1. Klik menu **Presensi**
2. Pilih periode
3. Pilih jenis: **Pengambilan Toga**
4. Klik **Lanjutkan ke Scanner**
5. Klik **Mulai Scan** atau masukkan kode manual
6. Setelah data muncul:
   - Wisudawan tanda tangan di signature pad
   - Isi keterangan (opsional)
   - Klik **Submit Presensi**

#### Presensi Gladi Bersih:
1. Sama seperti di atas, pilih **Gladi Bersih**
2. Tidak perlu tanda tangan, langsung submit

#### Presensi Hari-H (Ijazah):
1. Pilih **Hari-H (Ijazah)**
2. Wisudawan tanda tangan untuk bukti terima ijazah
3. Submit presensi

#### Presensi Konsumsi:
1. Pilih **Konsumsi**
2. Langsung submit tanpa tanda tangan

### 7. Melihat Laporan

1. Klik menu **Laporan**
2. Pilih periode dan sesi
3. Lihat statistik presensi
4. Klik **Export PDF** untuk download laporan
5. Pilih jenis laporan:
   - Laporan Presensi
   - Laporan Tanda Tangan

### 8. Cetak Kupon

1. Dari menu Laporan
2. Klik **Print Kupon**
3. PDF kupon akan tergenerate untuk semua wisudawan
4. Kupon berisi:
   - Data wisudawan
   - QR Code
   - Nomor kursi

## Untuk LO (Liaison Officer)

### 1. Login
- Username: (dibuat oleh admin)
- Password: (dibuat oleh admin)

### 2. Melihat Denah Kehadiran

1. Dari dashboard, pilih periode
2. Pilih sesi yang ingin dipantau
3. Klik **Denah**
4. Lihat denah kursi dengan status:
   - Hijau: Sudah hadir
   - Abu-abu: Belum hadir
5. Data update real-time

### 3. Scan Undangan (Cek Lokasi Kursi)

1. Klik menu **Scan Undangan**
2. Pilih periode
3. Scan QR Code undangan wisudawan
4. Sistem akan menampilkan:
   - Nama wisudawan
   - Nomor kursi
   - Lokasi sesi
   - Status RSVP
   - Status kehadiran

## Untuk Wisudawan

### 1. Akses Undangan Digital

1. Buka link yang dikirim via WhatsApp
   Format: `http://localhost/WISUDA/undangan/view/XXXX`
   (XXXX = kode unik 4 digit)
2. Lihat informasi wisuda:
   - Tanggal dan waktu
   - Lokasi (dengan maps)
   - Nomor kursi
   - Informasi tambahan

### 2. Konfirmasi Kehadiran (RSVP)

1. Scroll ke bawah halaman undangan
2. Klik **Saya Hadir** atau **Tidak Hadir**
3. Konfirmasi akan tersimpan

### 3. QR Code untuk Presensi

1. Screenshot atau simpan QR Code dari undangan
2. Tunjukkan saat presensi:
   - Pengambilan toga
   - Gladi bersih
   - Hari-H
   - Pengambilan konsumsi

## Tips & Trik

### Untuk Admin:

1. **Backup Database Berkala**
   ```bash
   mysqldump -u root -p wisuda_db > backup_$(date +%Y%m%d).sql
   ```

2. **Ubah Password Default**
   - Segera ubah password admin setelah instalasi

3. **Monitoring Activity Log**
   - Cek activity log secara berkala di dashboard

4. **Test Sebelum Hari-H**
   - Test semua fitur dengan data dummy
   - Test scanner QR code
   - Test signature pad

### Untuk LO:

1. **Refresh Denah Berkala**
   - Refresh browser untuk update data terbaru

2. **Siapkan Backup Scanner**
   - Siapkan beberapa device untuk scan

### Untuk Wisudawan:

1. **Simpan Kode Unik**
   - Catat kode unik 4 digit
   - Screenshot undangan

2. **Konfirmasi Segera**
   - Lakukan RSVP sesegera mungkin

3. **Datang Tepat Waktu**
   - Cek jadwal di undangan digital

## Troubleshooting

### QR Code Tidak Terbaca
- Pastikan kamera berfungsi
- Pastikan pencahayaan cukup
- Gunakan input manual jika perlu

### Undangan Tidak Muncul
- Cek kode unik (4 digit)
- Pastikan periode masih aktif
- Hubungi panitia

### Tanda Tangan Tidak Bisa
- Pastikan browser support HTML5 Canvas
- Gunakan stylus atau jari
- Klik Reset TTD jika salah

### Data Tidak Tersimpan
- Cek koneksi internet
- Cek session timeout
- Login ulang jika perlu

## FAQ

**Q: Bagaimana cara mengubah data wisudawan?**
A: Saat ini harus manual via database atau hapus dan upload ulang.

**Q: Bisa import dari format selain Excel?**
A: Saat ini hanya support Excel (.xls, .xlsx).

**Q: Apakah bisa kirim undangan via email?**
A: Fitur email belum tersedia, gunakan WhatsApp.

**Q: Bagaimana jika wisudawan lupa kode unik?**
A: Admin bisa cek di daftar wisudawan dan kirim ulang via WhatsApp.

**Q: Apakah data aman?**
A: Ya, menggunakan password hashing dan prepared statements.

## Kontak Support

Jika ada pertanyaan lebih lanjut, hubungi:
- Email: support@wisuda.ac.id
- WhatsApp: 08xxx-xxxx-xxxx
