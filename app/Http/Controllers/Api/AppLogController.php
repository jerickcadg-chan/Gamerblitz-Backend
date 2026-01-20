<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppLog;
use Illuminate\Http\Request;

class AppLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'level' => $request->input('level', 'error'),
        ])->validate([
            'level' => 'required',
            'message' => 'required',
        ]);

        AppLog::create([
            'level' => $request->input('level', 'error'),
            'message' => $request->input('message'),
            'stack_trace' => $request->input('stack_trace'),
            'url' => $request->input('url'),
            'user_agent' => $request->header('User-Agent'),
            'ip_address' => $request->ip(),
            'user_id' => auth()->id(),
            'source' => $request->input('source', 'web'),
            'context' => $request->input('context'),
            'payload' => $request->input('payload'),
        ]);

        return api_status_ok(null, 'App log created');
    }

    /**
     * Display the specified resource.
     */
    public function show(AppLog $appLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AppLog $appLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AppLog $appLog)
    {
        //
    }
}
