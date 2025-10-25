# Changelog

## [1.0.0] - 2025-01-22

### Added
- Sistem autentikasi untuk Admin dan LO
- Manajemen periode wisuda dengan auto-generate tabel dinamis
- Manajemen sesi wisuda per periode
- Upload data wisudawan dari Excel
- Generate kode unik 4 digit otomatis
- Sistem presensi dengan QR Code:
  - Presensi pengambilan toga (dengan tanda tangan digital)
  - Presensi gladi bersih
  - Presensi hari-H (dengan tanda tangan digital untuk ijazah)
  - Presensi pengambilan konsumsi
- Undangan digital dengan QR Code
- Sistem RSVP konfirmasi kehadiran
- Denah kursi dan monitoring kehadiran real-time
- Integrasi WhatsApp untuk kirim undangan
- Laporan presensi dan tanda tangan
- Export laporan ke PDF
- Cetak kupon wisudawan
- Activity logging
- Responsive design dengan Bootstrap 5
- DataTables untuk manajemen data
- Signature Pad untuk tanda tangan digital
- HTML5 QR Code Scanner

### Features
- **Role-based Access Control**: Admin dan LO dengan hak akses berbeda
- **Dynamic Table Generation**: Tabel wisudawan dibuat otomatis per periode
- **QR Code Integration**: Generate dan scan QR code untuk presensi
- **Digital Signature**: Tanda tangan digital untuk pengambilan toga dan ijazah
- **Real-time Monitoring**: Dashboard monitoring kehadiran
- **WhatsApp Integration**: Kirim undangan via WhatsApp
- **PDF Reports**: Export laporan ke PDF
- **Responsive UI**: Tampilan responsif untuk semua device

### Technical Stack
- PHP 7.4+ (Native, no framework)
- MySQL 5.7+
- Bootstrap 5
- jQuery
- DataTables.js
- SweetAlert2
- Signature Pad
- HTML5 QR Code Scanner
- FPDF
- SimpleXLSX
- Endroid QR Code

### Database
- Auto-migration schema
- Dynamic table creation per periode
- Foreign key constraints
- Indexed columns for performance

### Security
- Password hashing with bcrypt
- SQL injection prevention with PDO prepared statements
- XSS protection with output escaping
- CSRF token validation (ready to implement)
- Session management
- Activity logging

### Notes
- Default admin credentials: admin / admin123
- Requires PHP GD extension for QR code
- Requires PDO MySQL extension
- Mod_rewrite Apache required
