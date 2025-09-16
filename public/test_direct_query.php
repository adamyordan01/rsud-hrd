<?php
// Test direct database connection and query
require __DIR__ . '/../vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

use Illuminate\Support\Facades\DB;

try {
    echo "<h2>Direct Database Query Test</h2>\n";
    
    // Test database connection with a simple query
    try {
        $testQuery = DB::select('SELECT 1 as test');
        echo "<p>✅ Database connected successfully</p>\n";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>\n";
        return;
    }
    
    // Test basic count
    $totalCount = DB::table('hrd_karyawan')->count();
    echo "<p>Total records: {$totalCount}</p>\n";
    
    // Test active count
    $activeCount = DB::table('hrd_karyawan')->where('status_peg', 1)->count();
    echo "<p>Active records: {$activeCount}</p>\n";
    
    // Test specific status_kerja counts
    $statusKerjaList = [1, 2, 3, 4]; // Using numeric codes
    
    echo "<h3>Status Kerja Breakdown:</h3>\n";
    foreach ($statusKerjaList as $status) {
        $count = DB::table('hrd_karyawan')
            ->where('status_peg', 1)
            ->where('kd_status_kerja', $status)
            ->count();
        echo "<p>Status Kerja {$status}: {$count}</p>\n";
    }
    
    // Test some export categories directly
    echo "<h3>Export Categories Test:</h3>\n";
    
    // Test PNS export (KD_STATUS_KERJA = 1)
    $pnsCount = DB::table('hrd_karyawan')
        ->where('status_peg', 1)
        ->where('kd_status_kerja', 1)
        ->count();
    echo "<p>PNS export count: {$pnsCount}</p>\n";
    
    // Test all active
    $allActiveCount = DB::table('hrd_karyawan')->where('status_peg', 1)->count();
    echo "<p>All active export count: {$allActiveCount}</p>\n";
    
    // Test with sample data
    $sampleData = DB::table('hrd_karyawan')
        ->where('status_peg', 1)
        ->select('nip', 'nm_karyawan', 'kd_status_kerja')
        ->limit(5)
        ->get();
    
    echo "<h3>Sample Data (First 5 records):</h3>\n";
    echo "<table border='1'>\n";
    echo "<tr><th>NIP</th><th>Nama</th><th>Status Kerja</th></tr>\n";
    foreach ($sampleData as $row) {
        echo "<tr><td>{$row->nip}</td><td>{$row->nm_karyawan}</td><td>{$row->kd_status_kerja}</td></tr>\n";
    }
    echo "</table>\n";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>\n";
    echo "<pre>" . $e->getTraceAsString() . "</pre>\n";
}
?>
