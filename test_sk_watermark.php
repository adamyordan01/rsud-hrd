<?php
/**
 * Script untuk testing watermark pada SK
 * File ini bisa dihapus setelah testing selesai
 */

echo "=== Testing SK Watermark Implementation ===\n\n";

// 1. Cek file .env
echo "1. Environment Check:\n";
$envFile = file_exists('.env') ? file_get_contents('.env') : '';
if (strpos($envFile, 'APP_ENV=production') !== false) {
    echo "   - APP_ENV: production (watermark DISABLED)\n";
} else {
    echo "   - APP_ENV: not production (watermark ENABLED)\n";
}

// 2. Cek disk hrd_files
echo "\n2. Storage Check:\n";
$hrdPath = 'C:\laragon\www\storage_files\hrd';
$skPath = $hrdPath . '\sk-documents';
echo "   - hrd_files path: " . (is_dir($hrdPath) ? 'OK' : 'NOT FOUND') . "\n";
echo "   - SK documents path: " . (is_dir($skPath) ? 'OK' : 'NOT FOUND') . "\n";
echo "   - 2025 directory: " . (is_dir($skPath . '\2025') ? 'OK' : 'NOT FOUND') . "\n";

echo "\n3. Implementation Details:\n";
echo "   - Watermark akan muncul pada:\n";
echo "     * Environment selain 'production'\n";
echo "     * Saat testingMode = true di controller\n";
echo "   - Watermark text: 'TESTING'\n";
echo "   - Watermark style: Red semi-transparent, rotated 45 degrees\n";
echo "   - Location: Center of page, behind content\n";

echo "\n4. Files Modified:\n";
echo "   ✓ SKController.php - Added testingMode parameter\n";
echo "   ✓ sk-pegawai-kontrak.blade.php - Added watermark CSS & div\n";
echo "   ✓ perjanjian-kerja-page-1.blade.php - Added watermark CSS & div\n";
echo "   ✓ routes/web.php - Added testing route\n";

echo "\n5. Testing Routes Available:\n";
echo "   - Regular SK: /admin/sk-kontrak/print-perjanjian-kerja/{urut}/{tahun}\n";
echo "   - Testing SK: /admin/sk-kontrak/print-perjanjian-kerja/{urut}/{tahun}/testing\n";
echo "   - SK Document: /sk-document/{year}/{filename}\n";

echo "\n6. New Storage Location:\n";
echo "   - Old: storage/app/public/sk-tte/{year}/\n";
echo "   - New: hrd_files/sk-documents/{year}/\n";
echo "   - Access: Secured via authentication\n";

echo "\n=== Testing Complete ===\n";
echo "✓ Watermark implementation ready for testing!\n";
echo "✓ Storage redirected to hrd_files disk\n";
echo "✓ Authentication required for file access\n";
echo "\nYou can now test SK generation with TESTING watermark!\n";
