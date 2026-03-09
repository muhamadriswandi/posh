<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\DB;

$import = Import::latest()->first();
if (!$import) {
    die("No imports found.");
}

printf("Latest Import Status: ID=%d, Success=%d, Processed=%d, Failures=%d\n", 
    $import->id, 
    $import->successful_rows, 
    $import->processed_rows,
    $import->getFailedRowsCount());

if ($import->getFailedRowsCount() > 0) {
    echo "--- FAILED ROWS (ID: {$import->id}) ---\n";
    $rows = DB::table('failed_import_rows')->where('import_id', $import->id)->take(1)->get();
    foreach ($rows as $row) {
        file_put_contents('l:/proyek filamen/posh/row_error.json', json_encode($row, JSON_PRETTY_PRINT));
    }
}
echo "Row error info written to row_error.json\n";
