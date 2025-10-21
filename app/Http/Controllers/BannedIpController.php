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

        BannedIp::create([
            'ip_address' => $request->ip_address,
            'ban_reason' => $request->reason,
            'banned_at' => now(),
        ]);

        return redirect()->back()->with('success', 'IP banned successfully.');
    }

    public function destroy($id)
    {
        $bannedIp = BannedIp::findOrFail($id);
        $bannedIp->delete();

        return redirect()->back()->with('success', 'IP unbanned successfully.');
    }
}
