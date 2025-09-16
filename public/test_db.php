<?php

// Test file to check export data
?>
<!DOCTYPE html>
<html>
<head>
    <title>Export Data Test</title>
</head>
<body>
    <h1>Export Data Test</h1>
    
    <?php
    try {
        // Include Laravel bootstrap
        require_once '../vendor/autoload.php';
        $app = require_once '../bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
        
        // Test database connection
        $totalRecords = \Illuminate\Support\Facades\DB::connection('sqlsrv')->table('hrd_karyawan')->count();
        echo "<p>Total records in hrd_karyawan: $totalRecords</p>";
        
        $activeRecords = \Illuminate\Support\Facades\DB::connection('sqlsrv')->table('hrd_karyawan')->where('status_peg', 1)->count();
        echo "<p>Active records (status_peg=1): $activeRecords</p>";
        
        $pnsCount = \Illuminate\Support\Facades\DB::connection('sqlsrv')->table('hrd_karyawan')->where('status_peg', 1)->where('kd_status_kerja', 1)->count();
        echo "<p>PNS count: $pnsCount</p>";
        
        $honorCount = \Illuminate\Support\Facades\DB::connection('sqlsrv')->table('hrd_karyawan')->where('status_peg', 1)->where('kd_status_kerja', 2)->count();
        echo "<p>Honor count: $honorCount</p>";
        
        echo "<h2>Success! Database is accessible and has data.</h2>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
    ?>
    
</body>
</html>
