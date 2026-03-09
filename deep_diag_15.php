<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\DB;

$import = Import::find(15);
if (!$import) {
    die("Import ID 15 NOT FOUND.");
}

echo "Column Map: " . json_encode($import->column_map) . "\n\n";

$rows = DB::table('failed_import_rows')->where('import_id', 15)->get();
foreach ($rows as $index => $row) {
    echo "Row #$index:\n";
    echo "  Data: " . $row->data . "\n";
    echo "  Validator Error: " . ($row->validation_error ?? 'NULL') . "\n";
    echo "--------------------------\n";
}
