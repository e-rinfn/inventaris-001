<?php

$license_path = "/opt/lampp/var/.lisensi-inventaris-001";

// Cek apakah file lisensi ada
if (!file_exists($license_path)) {
    http_response_code(403);
    echo "
    <div style='
        font-family: Arial, sans-serif;
        margin: 50px auto;
        max-width: 600px;
        padding: 20px;
        border: 1px solid #dc3545;
        border-radius: 10px;
        background: #fff5f5;
        color: #b30000;
    '>
        <h2 style='margin-top:0;'>🚫 Akses Ditolak</h2>
        <p>
            Sistem tidak dapat menemukan file lisensi yang diperlukan untuk menjalankan aplikasi ini.
        </p>
        <p><strong>Penyebab kemungkinan:</strong></p>
        <ul>
            <li>File lisensi tidak ditemukan.</li>
            <li>Sistem tidak memiliki izin akses ke lokasi file.</li>
            <li>Aplikasi dipindahkan ke perangkat lain tanpa lisensi.</li>
        </ul>
        <p>
            <strong>Solusi:</strong> Pastikan file lisensi tersedia.<br>
        </p>
    </div>
    ";
    exit;
}

// Ambil isi file lisensi
$valid_license = trim(file_get_contents($license_path));

// Kunci lisensi yang seharusnya
$expected_license = "LICENSE-ABC-123-XYZ";

// Cek kesesuaian lisensi
if ($valid_license !== $expected_license) {
    http_response_code(403);
    echo "
    <div style='
        font-family: Arial, sans-serif;
        margin: 50px auto;
        max-width: 600px;
        padding: 20px;
        border: 1px solid #dc3545;
        border-radius: 10px;
        background: #fff5f5;
        color: #b30000;
    '>
        <h2 style='margin-top:0;'>🚫 Lisensi Tidak Valid</h2>
        <p>
            File lisensi ditemukan, namun tidak sesuai dengan lisensi resmi.
        </p>
        <p><strong>Penyebab kemungkinan:</strong></p>
        <ul>
            <li>File lisensi tidak cocok.</li>
            <li>Aplikasi dijalankan pada perangkat yang tidak terdaftar.</li>
        </ul>
        <p>
            <strong>Solusi:</strong> Pastikan Anda menggunakan lisensi resmi.<br>
        </p>
    </div>
    ";
    exit;
}
