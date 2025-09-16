<?php

require_once 'vendor/autoload.php';

// Test autoloading ExportController
try {
    $reflection = new ReflectionClass('App\Http\Controllers\Export\ExportController');
    echo "✅ Class found: " . $reflection->getName() . "\n";
    echo "📁 File: " . $reflection->getFileName() . "\n";
    
    $methods = $reflection->getMethods();
    echo "📋 Methods available: " . count($methods) . "\n";
    
    foreach($methods as $method) {
        if ($method->isPublic() && !$method->isConstructor()) {
            echo "   - " . $method->getName() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
