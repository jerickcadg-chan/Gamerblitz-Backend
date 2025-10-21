<?php

namespace App\Http\Controllers;

use App\Models\BannedIp;
use Illuminate\Http\Request;

class BannedIpController extends Controller
{
    public function index()
    {
        $query = BannedIp::query();

        if (request('ip')) {
            $query->where('ip_address', 'like', '%' . request('ip') . '%');
        }

        $bannedIps = $query->paginate();
        return view('banned-ip.index', compact('bannedIps'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
            'reason' => 'required|string|max:255',
        ]);

        if (BannedIp::where('ip_address', $request->ip_address)->exists()) {
            toast('IP is already banned.', 'error');
            return redirect()->back();
        }

        BannedIp::create([
            'ip_address' => $request->ip_address,
            'ban_reason' => $request->reason,
            'banned_at' => now(),
        ]);

        toast('IP banned successfully.', 'success');
        return redirect()->back();
    }

    public function destroy($id)
    {
        $bannedIp = BannedIp::findOrFail($id);
        $bannedIp->delete();

        toast('IP unbanned successfully.', 'success');
        return redirect()->back();
    }
}
