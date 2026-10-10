<?php
// Runs the actual controller method without an application DB or vendor install.
namespace Illuminate\Http { class Request {} }
namespace Illuminate\Support\Facades {
    class Auth { public static $user; public static function user() { return self::$user; } }
    class DB { public static $rows = []; public static function table($table) { return new \Query(self::$rows); } }
}
namespace App\Http\Controllers { class Controller {} }
namespace App\Models {
    class avatarSpaces { public static $space; public static function find($id) { return self::$space && (string) self::$space->id === (string) $id ? self::$space : null; } }
}
namespace {
    class Query {
        private $rows; private $filters = [];
        function __construct($rows) { $this->rows = $rows; }
        function where($key, $value) { $this->filters[$key] = $value; return $this; }
        function exists() { foreach ($this->rows as $row) { foreach ($this->filters as $key => $value) { if ((string) ($row[$key] ?? '') !== (string) $value) continue 2; } return true; } return false; }
    }
    class Result {
        public $data, $status, $headers = [];
        function json($data, $status = 200) { $this->data = $data; $this->status = $status; return $this; }
        function header($key, $value) { $this->headers[$key] = $value; return $this; }
    }
    function response() { return new Result; }
    require __DIR__ . '/../app/Http/Controllers/SpaceSettingsController.php';
    $controller = new \App\Http\Controllers\SpaceSettingsController;
    \App\Models\avatarSpaces::$space = (object) ['id' => 101, 'user_id' => 108];
    \Illuminate\Support\Facades\Auth::$user = (object) ['id' => 108];
    \Illuminate\Support\Facades\DB::$rows = [['space_id' => 101, 'item_id' => 'stand', 'model_id' => 2026100702]];
    function check($condition, $name) { if (!$condition) throw new \RuntimeException($name); echo "PASS {$name}\n"; }
    function callPermission($room = 101, $item = 'stand') { global $controller; return $controller->concertPermission(new \Illuminate\Http\Request, $room, $item); }
    $result = callPermission();
    check($result->data === ['spaceId' => '101', 'validSource' => true, 'canEdit' => true], 'owner and exact placed stand');
    check($result->headers['Cache-Control'] === 'no-store', 'permission responses cannot be cached');
    \Illuminate\Support\Facades\Auth::$user = (object) ['id' => 200];
    check(callPermission()->data['canEdit'] === false, 'stranger cannot gain owner capability');
    check(callPermission(999)->status === 404, 'unknown room rejected');
    check(callPermission(101, str_repeat('x', 129))->status === 400, 'oversized item rejected');
    check(callPermission(101, 'foreign-stand')->data['validSource'] === false, 'wrong item rejected');
    \Illuminate\Support\Facades\DB::$rows[0]['space_id'] = 102;
    check(callPermission()->data['validSource'] === false, 'stand from another room rejected');
    \Illuminate\Support\Facades\DB::$rows[0]['space_id'] = 101;
    \Illuminate\Support\Facades\DB::$rows[0]['model_id'] = 12;
    check(callPermission()->data['validSource'] === false, 'wrong model rejected');
    \Illuminate\Support\Facades\Auth::$user = null;
    check(callPermission()->status === 401, 'missing authenticated user rejected');
}
