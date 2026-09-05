<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$schedules = App\Models\PatrolSchedule::with('checkpoints')->get();
foreach ($schedules as $s) {
    echo "ID: " . $s->id . "\n";
    echo "Checkpoints Count: " . $s->checkpoints()->count() . "\n";
    echo "Start Time (RAW): " . $s->start_time . "\n";
    echo "End Time (RAW): " . $s->end_time . "\n";
    echo "Date: " . $s->patrol_date->format('Y-m-d') . "\n";
    echo "User ID: " . $s->user_id . "\n\n";
}
