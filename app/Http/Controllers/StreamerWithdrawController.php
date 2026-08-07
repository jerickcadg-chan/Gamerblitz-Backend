<?php

namespace App\Http\Controllers;

use App\Models\Streamer;
use App\Models\StreamerWithdraw;
use App\Models\StreamerHistory;
use Illuminate\Http\Request;

class StreamerWithdrawController extends Controller
{
    public function index(Request $request)
    {
        $query = StreamerWithdraw::query()->with('streamer');
        
        if ($request->search) {
            $query->whereHas('streamer', function ($q) use ($request) {
                $q->where('code', 'like', "%{$request->search}%")
                  ->orWhere('channel_name', 'like', "%{$request->search}%");
            });
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        $withdraws = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('streamer-withdraw.index', [
            'title' => 'Streamer Withdrawals',
            'withdraws' => $withdraws,
        ]);
    }
    
    public function approve(StreamerWithdraw $withdraw)
    {
        if ($withdraw->status !== 'pending') {
            return redirect()->back()->with('error', 'Withdrawal is not pending');
        }
        
        $withdraw->update([
            'status' => 'approved',
            'processed_at' => now(),
        ]);
                
        return redirect()->back()->with('success', 'Withdrawal approved successfully');
    }
    
    public function reject(Request $request, StreamerWithdraw $withdraw)
    {
        if ($withdraw->status !== 'pending') {
            return redirect()->back()->with('error', 'Withdrawal is not pending');
        }
        
        // Refund balance to streamer
        $streamer = $withdraw->streamer;
        $streamer->balance += $withdraw->amount;
        $streamer->save();
        
        $withdraw->update([
            'status' => 'rejected',
            'reject_reason' => $request->reject_reason,
            'processed_at' => now(),
        ]);
        
        
        return redirect()->back()->with('success', 'Withdrawal rejected and balance refunded');
    }
}