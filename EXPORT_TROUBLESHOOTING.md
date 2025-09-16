# Export Feature - Laravel dengan PhpSpreadsheet

## Corrected Implementation 

### ⚠️ **PENTING: Perubahan dari Maatwebsite/Excel ke PhpSpreadsheet**

Pada awalnya, implementation menggunakan `Maatwebsite\Excel` yang tidak terinstall. Sekarang telah diperbaiki untuk menggunakan **PhpSpreadsheet** secara langsung yang sudah tersedia di `composer.json`.

### 1. Tech Stack yang Benar
```php
// BEFORE (SALAH):
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PegawaiExport;

// AFTER (BENAR):
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
```

### 2. Implementasi Export yang Benar

#### Native PhpSpreadsheet Implementation
```php
private function generateExcel($data, $title)
{
    // Create new Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set headers, styling, dan data
    // ... (implementation details in controller)
    
    // Create writer dan output
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
```

### 3. Benefits dari PhpSpreadsheet Langsung

#### Advantages:
- ✅ **No Extra Dependencies**: Sudah tersedia di project
- ✅ **Full Control**: Kontrol penuh atas styling dan formatting
- ✅ **Better Performance**: Tidak ada wrapper overhead
- ✅ **Memory Efficient**: Direct memory management
- ✅ **Proper Headers**: Native Excel MIME types

#### Enhanced Features:
```php
// Professional styling
$headerStyle = [
    'font' => ['bold' => true, 'size' => 14],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FFE0E0E0']
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
    ]
];
```

### 4. File Structure (Updated)

```
app/Http/Controllers/Export/
└── ExportController.php        # Pure PhpSpreadsheet implementation

app/Http/Middleware/
└── ExportMiddleware.php        # Performance optimization

resources/views/exports/
└── index.blade.php            # Enhanced JavaScript with Fetch API

REMOVED:
├── app/Exports/               # No longer needed
├── config/excel.php          # Maatwebsite config removed
```

## Performance Optimizations

### 1. Memory & Time Management
```php
// In ExportController methods
set_time_limit(300);           // 5 minutes
ini_set('memory_limit', '1024M'); // 1GB
```

### 2. Enhanced JavaScript (Fetch API)
```javascript
// Better error handling dan file validation
fetch(exportUrl, {
    method: 'GET',
    headers: {
        'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'X-Requested-With': 'XMLHttpRequest'
    }
})
.then(response => {
    // Validate content type
    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('spreadsheet')) {
        throw new Error('Response bukan file Excel yang valid');
    }
    return response.blob();
})
```

### 3. Database Query Optimization
```php
// Comprehensive JOIN untuk data lengkap
$query = DB::connection('sqlsrv')
    ->table('hrd_karyawan as k')
    ->leftJoin('hrd_ruangan as r', 'k.kd_ruangan', '=', 'r.kd_ruangan')
    ->leftJoin('hrd_status_kerja as sk', 'k.kd_status_kerja', '=', 'sk.kd_status_kerja')
    // ... more joins for complete data
    ->select([
        'k.nip as nrk', 'k.nik as nrik', 'k.nama',
        // ... 27 columns total
    ]);
```

## Testing the Corrected Implementation

### 1. Verify Routes Work
```bash
php artisan route:list --name=export
```

### 2. Test Export Functionality
1. Navigate to `/admin/export`
2. Click any export card
3. Verify:
   - ✅ No JavaScript errors
   - ✅ Downloads `.xlsx` file
   - ✅ Opens in Excel correctly
   - ✅ Professional formatting applied

### 3. Monitor Performance
```bash
# Check logs
tail -f storage/logs/laravel.log

# Expected log entries:
# [timestamp] INFO: Exporting Total_Pegawai_Aktif: 1250 records
```

## Troubleshooting Common Issues

### Issue 1: "Target class does not exist"
**Solution:**
```bash
composer dump-autoload
php artisan config:clear
php artisan route:clear
```

### Issue 2: Memory Limit Exceeded
**Solution:**
- Increase PHP memory_limit in server config
- Use ExportMiddleware (auto-applied)
- Consider chunking very large datasets

### Issue 3: Wrong File Format
**Solution:**
- PhpSpreadsheet handles proper MIME types
- Verify headers in generateExcel() method
- Check browser developer tools

## Export Categories Available

### Status Kerja Based:
- **Total Aktif**: Semua pegawai aktif
- **DUK**: PNS (kd_status_kerja = 1)  
- **Honor**: Honor (kd_status_kerja = 2)
- **Kontrak BLUD**: Kontrak BLUD (kd_status_kerja = 3, kd_jenis_peg = 2)
- **Kontrak Pemko**: Kontrak Pemko (kd_status_kerja = 3, kd_jenis_peg = 1)
- **Part Time**: Part Time (kd_status_kerja = 4)
- **PPPK**: PPPK (kd_status_kerja = 7)
- **THL**: THL (kd_status_kerja = 6)

### Jenis Tenaga Based:
- **Tenaga Medis**: kd_jenis_tenaga = 1
- **Perawat/Bidan**: kd_jenis_tenaga = 2  
- **Penunjang Medis**: kd_jenis_tenaga = 3
- **Non Kesehatan**: kd_jenis_tenaga = 4

### Special Categories:
- **Pegawai Keluar**: status_peg = 0
- **Pegawai Pensiun**: status_peg = 0, kd_alasan_keluar = 1
- **Pegawai Tubel**: status_tubel = 1
- **BNI Syariah Kontrak**: Multiple status_kerja
- **BNI Syariah PNS**: PNS + PPPK

## Migration Notes

### From Maatwebsite/Excel to PhpSpreadsheet:
1. ✅ Removed dependency on maatwebsite/laravel-excel
2. ✅ Direct PhpSpreadsheet implementation  
3. ✅ Enhanced performance and memory management
4. ✅ Better error handling and logging
5. ✅ Professional Excel formatting maintained
6. ✅ All 17 export categories functional
7. ✅ **Database Schema Corrections Applied**

### Critical Database Schema Fixes:
- **Column Mapping Corrected**: Fixed `kd_golongan` → `kd_gol_sekarang` 
- **JOIN Relationships**: Updated `hrd_golongan` join to use correct `kd_gol` field
- **Pendidikan Table**: Fixed `kd_jenjang_pendidikan` → `kd_pendidikan_terakhir`
- **Field Selection**: Updated to use actual table structure (nip_baru, no_ktp, tgl_lahir, etc.)
- **Agama Table**: Corrected to use `agama` table instead of `hrd_agama`

### Result:
- **More Reliable**: No external package dependencies
- **Better Performance**: Direct library usage
- **Easier Maintenance**: Simple, native implementation
- **Proper Excel Format**: Guaranteed .xlsx output
- **Database Accurate**: Schema-compliant queries
- **✅ FULLY FUNCTIONAL**: All exports working without SQL errors
