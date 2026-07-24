<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

use Rats\Zkteco\Lib\ZKTeco;





use Illuminate\Support\Facades\Log;

class DeviceController extends Controller
{
    
public function fetchZkAttendance(Device $device)
{
    $zk = new ZKTeco($device->ip, $device->port ?? 4370);

    if (!$zk->connect()) {
        return response()->json([
            'status' => 'failed',
            'message' => 'Cannot connect to device'
        ], 500);
    }

    // ✅ هذا هو الوحيد المدعوم
    $attendance = $zk->getAttendance();

    $zk->disconnect();

    return response()->json([
        'status' => 'success',
        'count'  => count($attendance),
        'data'   => $attendance
    ]);
}

    
    
   public function importSelectedUsers(Request $request, Device $device)
{
    $request->validate([
        'users' => 'required|array'
    ]);

    foreach ($request->users as $user) {
        ZkUser::updateOrCreate(
            [
                'device_id' => $device->id,
                'uid'       => $user['uid']
            ],
            [
                'user_id'  => $user['userid'],
                'name'     => $user['name'],
                'role'     => $user['role'] ?? null
            ]
        );
    }

    return response()->json([
        'status' => 'success',
        'count'  => count($request->users)
    ]);
}
    
    
    public function index()
    {
        $devices = Device::orderBy('id','desc')->get();
        return view('admin.devices.index', compact('devices'));
    }

    public function create()
    {
        return view('admin.devices.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'ip'   => 'required|ip',
            'port' => 'required|integer'
        ]);

        Device::create($request->all());

        return redirect()->route('devices.index')
            ->with('success', 'Device ajouté avec succès');
    }

    public function edit(Device $device)
    {
        return view('admin.devices.edit', compact('device'));
    }

    public function update(Request $request, Device $device)
    {
        $request->validate([
            'name' => 'required',
            'ip'   => 'required|ip',
            'port' => 'required|integer'
        ]);

        $device->update($request->all());

        return redirect()->route('devices.index')
            ->with('success', 'Device modifié avec succès');
    }

    public function destroy(Device $device)
    {
        $device->delete();

        return redirect()->route('devices.index')
            ->with('success', 'Device supprimé');
    }

public function connect(Device $device)
{//dd($device) ;
    try {
        if ((int)$device->is_active !== 1) {
            return response()->json(['status' => 'inactive']);
        }

        if (empty($device->ip)) {
            return response()->json([
                'status' => 'failed',
                'error' => 'IP not defined'
            ]);
        }

        $ip   = trim($device->ip);
        $port = $device->port ?: 4370;

        // ⚠️ حماية إضافية
        set_time_limit(5);

        $zk = new ZKTeco($ip, $port);

        $result = @$zk->connect(); // suppress fatal warning

        if ($result === true) {
            return response()->json(['status' => 'connected']);
        }

        return response()->json(['status' => 'failed']);

    } catch (\Throwable $e) {

        Log::error('ZK CONNECT ERROR', [
            'device_id' => $device->id,
            'message'   => $e->getMessage(),
            'trace'     => $e->getTraceAsString()
        ]);

        return response()->json([
            'status' => 'failed',
            'error'  => 'Internal error (see logs)'
        ], 500);
    }
}

     
}
