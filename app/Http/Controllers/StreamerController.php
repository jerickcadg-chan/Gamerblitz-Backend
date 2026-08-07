<?php

namespace App\Http\Controllers;

use App\Models\Streamer;
use App\Models\StreamerHistory;
use App\Models\User;
use Illuminate\Http\Request;

class StreamerController extends Controller
{
    public function index(Request $request)
    {
        $query = Streamer::query();
        
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'like', "%{$request->search}%")
                  ->orWhere('channel_name', 'like', "%{$request->search}%");
            });
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        $streamers = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('streamer.index', [
            'title' => 'Streamers',
            'streamers' => $streamers,
        ]);
    }
    
    public function create()
    {
        return view('streamer.create', [
            'title' => 'Create Streamer',
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:streamers,code',
            'channel_name' => 'required|string',
            'platform' => 'required|string',
            'channel_url' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'user_email' => 'nullable|email|exists:users,email',
            'commission_rate' => 'nullable|numeric|min:0|max:5',
	        'discount_rate' => 'nullable|numeric|min:0|max:5',
        ]);

        // Find user by email if provided
        $userId = null;
        if ($request->user_email) {
            $user = User::where('email', $request->user_email)->first();
            if ($user) {
                // Check if another streamer is already linked to this user
                $existingStreamer = Streamer::where('user_id', $user->id)->first();
                if ($existingStreamer) {
                    return back()->withErrors(['user_email' => 'This user is already linked to another streamer: ' . $existingStreamer->code])->withInput();
                }
                $userId = $user->id;
            }
        }

        // Auto-fetch avatar based on platform
        $avatarUrl = null;
        if ($request->channel_url) {
            $avatarUrl = $this->fetchAvatarFromPlatform($request->platform, $request->channel_url);
        }

        Streamer::create([
            'code' => strtoupper($request->code),
            'channel_name' => $request->channel_name,
            'platform' => $request->platform,
            'channel_url' => $request->channel_url,
            'status' => $request->status,
            'balance' => 0,
            'total_earnings' => 0,
            'user_id' => $userId,
            'commission_rate' => $request->commission_rate ?? 1,
            'discount_rate' => $request->discount_rate ?? 0.5,
            'avatar_url' => $avatarUrl,
        ]);

        return redirect()->route('streamer.index')->with('success', 'Streamer created successfully');
    }

    /**
     * Fetch avatar URL from platform service
     */
    private function fetchAvatarFromPlatform(string $platform, string $channelUrl): ?string
    {
        // Avatar fetching services not available in this environment
        return null;
    }
    
    public function edit(Streamer $streamer)
    {
        return view('streamer.edit', [
            'title' => 'Edit Streamer',
            'streamer' => $streamer,
        ]);
    }
    
public function update(Request $request, Streamer $streamer)
{
    $request->validate([
        'code' => 'required|string|unique:streamers,code,' . $streamer->id,
        'channel_name' => 'required|string',
        'platform' => 'required|string',
        'channel_url' => 'nullable|string',
        'status' => 'required|in:active,inactive',
        'user_email' => 'nullable|email|exists:users,email',
        'commission_rate' => 'nullable|numeric|min:0|max:5',
        'discount_rate' => 'nullable|numeric|min:0|max:5',
    ]);

    // Find user by email if provided
    $userId = null;
    if ($request->user_email) {
        $user = User::where('email', $request->user_email)->first();
        if ($user) {
            // Check if another streamer is already linked to this user
            $existingStreamer = Streamer::where('user_id', $user->id)
                ->where('id', '!=', $streamer->id)
                ->first();
            if ($existingStreamer) {
                return back()->withErrors([
                    'user_email' => 'This user is already linked to another streamer: ' . $existingStreamer->code
                ])->withInput();
            }
            $userId = $user->id;
        }
    }

    $streamer->update([
        'code' => strtoupper($request->code),
        'channel_name' => $request->channel_name,
        'platform' => $request->platform,
        'channel_url' => $request->channel_url,
        'status' => $request->status,
        'user_id' => $userId,
        'commission_rate' => $request->commission_rate ?? 1,
        'discount_rate' => $request->discount_rate ?? 0.5,
    ]);

    return redirect()->route('streamer.index')->with('success', 'Streamer updated successfully');
}
    
    public function show(Streamer $streamer)
    {
        $histories = StreamerHistory::where('streamer_id', $streamer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('streamer.show', [
            'title' => 'Streamer Details',
            'streamer' => $streamer,
            'histories' => $histories,
        ]);
    }
    
    public function destroy(Streamer $streamer)
    {
        $streamer->delete();
        return redirect()->route('streamer.index')->with('success', 'Streamer deleted successfully');
    }
}