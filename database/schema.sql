-- Database Schema untuk Sistem Manajemen Wisuda
-- Buat database terlebih dahulu

CREATE DATABASE IF NOT EXISTS wisuda_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE wisuda_db;

-- Tabel Users (Admin, LO, dll)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'lo') NOT NULL DEFAULT 'lo',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Periode Wisuda
CREATE TABLE IF NOT EXISTS periode_wisuda (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_periode VARCHAR(100) NOT NULL,
    tahun YEAR NOT NULL,
    periode_ke TINYINT NOT NULL,
    status ENUM('aktif', 'selesai', 'draft') DEFAULT 'draft',
    table_prefix VARCHAR(50) NOT NULL UNIQUE,
    keterangan TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_tahun (tahun),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Sesi Wisuda
CREATE TABLE IF NOT EXISTS sesi_wisuda (
    id INT AUTO_INCREMENT PRIMARY KEY,
    periode_id INT NOT NULL,
    nama_sesi VARCHAR(100) NOT NULL,
    tanggal DATE NOT NULL,
    waktu_mulai TIME NOT NULL,
    waktu_selesai TIME NOT NULL,
    lokasi VARCHAR(255) NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    kapasitas INT DEFAULT 0,
    informasi_tambahan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (periode_id) REFERENCES periode_wisuda(id) ON DELETE CASCADE,
    INDEX idx_periode (periode_id),
    INDEX idx_tanggal (tanggal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Template Denah Kursi
CREATE TABLE IF NOT EXISTS denah_kursi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sesi_id INT NOT NULL,
    nomor_kursi VARCHAR(20) NOT NULL,
    baris VARCHAR(10) NOT NULL,
    kolom VARCHAR(10) NOT NULL,
    zona VARCHAR(50),
    status ENUM('tersedia', 'terisi', 'reserved') DEFAULT 'tersedia',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sesi_id) REFERENCES sesi_wisuda(id) ON DELETE CASCADE,
    UNIQUE KEY unique_kursi (sesi_id, nomor_kursi),
    INDEX idx_sesi (sesi_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Informasi Undangan (Custom Fields)
CREATE TABLE IF NOT EXISTS informasi_undangan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    periode_id INT NOT NULL,
    judul VARCHAR(100) NOT NULL,
    konten TEXT NOT NULL,
    urutan INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (periode_id) REFERENCES periode_wisuda(id) ON DELETE CASCADE,
    INDEX idx_periode (periode_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Setting Kupon
CREATE TABLE IF NOT EXISTS setting_kupon (
    id INT AUTO_INCREMENT PRIMARY KEY,
    periode_id INT NOT NULL,
    template_header TEXT,
    template_body TEXT,
    template_footer TEXT,
    ukuran_kertas ENUM('A4', 'A5', 'Letter') DEFAULT 'A4',
    orientasi ENUM('portrait', 'landscape') DEFAULT 'portrait',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (periode_id) REFERENCES periode_wisuda(id) ON DELETE CASCADE,
    INDEX idx_periode (periode_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Log Aktivitas
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, full_name, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@wisuda.ac.id', 'admin');

-- Catatan: Tabel wisudawan akan dibuat secara dinamis per periode
-- Format: {tahun}_{periode}_t_wisudawan
-- Contoh: 2025_3_t_wisudawan

-- Template struktur tabel wisudawan (akan dibuat dinamis):
/*
CREATE TABLE IF NOT EXISTS {table_prefix}_t_wisudawan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    periode_id INT NOT NULL,
    sesi_id INT NOT NULL,
    kode_unik VARCHAR(4) UNIQUE NOT NULL,
    nim VARCHAR(20) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    program_studi VARCHAR(100),
    fakultas VARCHAR(100),
    ipk DECIMAL(3,2),
    predikat VARCHAR(50),
    email VARCHAR(100),
    no_hp VARCHAR(20),
    ukuran_toga ENUM('S', 'M', 'L', 'XL', 'XXL'),
    nomor_kursi VARCHAR(20),
    
    -- RSVP
    status_rsvp ENUM('pending', 'confirmed', 'declined') DEFAULT 'pending',
    rsvp_at TIMESTAMP NULL,
    
    -- Presensi
    presensi_toga_at TIMESTAMP NULL,
    presensi_toga_by INT,
    ttd_toga TEXT,
    keterangan_toga TEXT,
    
    presensi_gladi_at TIMESTAMP NULL,
    presensi_gladi_by INT,
    
    presensi_hadir_at TIMESTAMP NULL,
    presensi_hadir_by INT,
    ttd_hadir TEXT,
    keterangan_hadir TEXT,
    
    presensi_konsumsi_at TIMESTAMP NULL,
    presensi_konsumsi_by INT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (periode_id) REFERENCES periode_wisuda(id) ON DELETE CASCADE,
    FOREIGN KEY (sesi_id) REFERENCES sesi_wisuda(id) ON DELETE CASCADE,
    INDEX idx_kode_unik (kode_unik),
    INDEX idx_nim (nim),
    INDEX idx_sesi (sesi_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
*/
