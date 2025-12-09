<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\WhoisRecord;
use Illuminate\Http\Request;

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

        return view('dashboard', compact('domains', 'whoisRecords'));
    }

    public function domains()
    {
        $domains = Domain::withCount('whoisRecords')
            ->latest()
            ->paginate(20);

        return view('domains', compact('domains'));
    }

    public function whoisRecords()
    {
        $whoisRecords = WhoisRecord::with('domain')
            ->latest()
            ->paginate(20);

        return view('whois-records', compact('whoisRecords'));
    }
}
