<?php

require_once 'vendor/autoload.php';

// Test direct controller execution
try {
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    $controller = new App\Http\Controllers\Export\ExportController();
    $response = $controller->index();
    
    echo "Controller executed successfully\n";
    echo "Response type: " . get_class($response) . "\n";
    
    if ($response instanceof Illuminate\View\View) {
        $data = $response->getData();
        echo "Export data received:\n";
        if (isset($data['exportData'])) {
            foreach ($data['exportData'] as $key => $value) {
                echo "  $key: $value\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
