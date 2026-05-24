<?php

namespace App\Http\Controllers;

use App\Models\AppLog;
use Illuminate\Http\Request;

class AppLogController extends Controller
{
    private string $title;

    public function __construct()
    {
        $this->title = 'App Log';

        $this->middleware(['permission:View App Log'])->only('index', 'show');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appLogs = AppLog::latest()->paginate(10);

        $title = $this->title;

        return view('app-logs.index', compact('title', 'appLogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(AppLog $appLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AppLog $appLog)
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
