<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\IncidentController;
use App\Models\Area;
use App\Models\Incident;
use App\Models\IncidentStatusHistory;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

$pass = 0;
$fail = 0;

function check(string $label, bool $ok, string $detail = ''): void
{
    global $pass, $fail;
    if ($ok) { $pass++; echo "  [OK]   $label" . ($detail ? " — $detail" : '') . PHP_EOL; }
    else     { $fail++; echo "  [FAIL] $label" . ($detail ? " — $detail" : '') . PHP_EOL; }
}

DB::beginTransaction();

try {
    echo "== Persiapan data uji ==\n";
    $role = Role::firstOrCreate(['role_name' => 'Security Guard'], ['role_name' => 'Security Guard']);
    $user = User::create([
        'role_id' => $role->id, 'name' => 'Petugas Tes Inc',
        'username' => 'tesinc_' . uniqid(), 'email' => 'inc' . uniqid() . '@t.local',
        'password' => bcrypt('x12345678'), 'status' => 'active',
    ]);
    $area = Area::firstOrCreate(['area_name' => 'Area Tes Inc'], ['area_code' => 'INC', 'description' => 'x']);

    $i1 = Incident::create([
        'user_id' => $user->id, 'area_id' => $area->id, 'title' => 'Percikan Api Panel',
        'category' => 'kebakaran', 'priority' => 'critical', 'status' => 'new',
        'description' => 'Tes deskripsi', 'latitude' => -6.2, 'longitude' => 106.8,
        'reported_at' => now(),
    ]);
    $i2 = Incident::create([
        'user_id' => $user->id, 'area_id' => $area->id, 'title' => 'Kebocoran Pipa',
        'category' => 'kebocoran', 'priority' => 'medium', 'status' => 'in_review',
        'reported_at' => now()->subHour(),
    ]);
    IncidentStatusHistory::create([
        'incident_id' => $i1->id, 'status' => 'new', 'changed_by' => $user->id, 'changed_at' => now(),
    ]);

    echo "== 1. Index tanpa filter ==\n";
    $view = (new IncidentController)->index(Request::create('/incidents', 'GET'));
    $data = $view->getData();
    check('Stats total = 2', $data['stats']['total'] === 2);
    check('Stats new = 1', $data['stats']['new'] === 1);
    check('Stats in_review = 1', $data['stats']['in_review'] === 1);
    check('Semua insiden tampil (2)', $data['incidents']->count() === 2);

    echo "== 2. Filter status 'new' ==\n";
    $viewNew = (new IncidentController)->index(Request::create('/incidents', 'GET', ['status' => 'new']));
    check('Hanya 1 insiden new', $viewNew->getData()['incidents']->count() === 1);

    echo "== 3. Filter search judul ==\n";
    $viewSearch = (new IncidentController)->index(Request::create('/incidents', 'GET', ['search' => 'Pipa']));
    check('Search "Pipa" → 1 hasil', $viewSearch->getData()['incidents']->count() === 1);

    echo "== 4. Render view ==\n";
    $html = $view->render();
    check('Render OK', strlen($html) > 1000, 'panjang=' . strlen($html));
    check('HTML memuat #INC-001', str_contains($html, '#INC-001'));
    check('HTML memuat judul insiden', str_contains($html, 'Percikan Api Panel'));
    check('HTML memuat CRITICAL', str_contains($html, 'CRITICAL'));
    check('HTML memuat JSON Alpine (incidentPage)', str_contains($html, 'incidentPage()'));
    check('JSON modal memuat deskripsi', str_contains($html, 'Tes deskripsi'));

    echo "\nHasil: $pass OK, $fail FAIL\n";
} catch (Throwable $e) {
    $fail++;
    echo '  [ERROR] ' . $e->getMessage() . PHP_EOL;
    echo '  ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL;
} finally {
    DB::rollBack();
    echo "Data uji di-rollback.\n";
}

exit($fail === 0 ? 0 : 1);
