<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\WhoisRecord;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $domains = Domain::withCount('whoisRecords')
            ->latest()
            ->paginate(15);

        $whoisRecords = WhoisRecord::with('domain')
            ->latest()
            ->paginate(15);

        return Inertia::render('Dashboard', [
            'domains' => $domains,
            'whoisRecords' => $whoisRecords,
        ]);
    }

    public function domains()
    {
        $domains = Domain::withCount('whoisRecords')
            ->latest()
            ->paginate(20);

        return Inertia::render('Domains', [
            'domains' => $domains,
        ]);
    }

    public function whoisRecords()
    {
        $whoisRecords = WhoisRecord::with('domain')
            ->latest()
            ->paginate(20);

        return Inertia::render('WhoisRecords', [
            'whoisRecords' => $whoisRecords,
        ]);
    }
}

