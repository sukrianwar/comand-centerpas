<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncidentController extends Controller
{
    /**
     * Halaman daftar laporan insiden dengan filter, search, dan pagination.
     */
    public function index(Request $request): View
    {
        $query = Incident::with(['user', 'area', 'statusHistory.user'])
            ->orderByDesc('reported_at');

        // ── Pencarian (judul, nama pelapor) ─────────────────────────────────
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        // ── Filter status ───────────────────────────────────────────────────
        if ($request->filled('status') && array_key_exists($request->status, Incident::STATUSES)) {
            $query->where('status', $request->status);
        }

        // ── Filter kategori ─────────────────────────────────────────────────
        if ($request->filled('category') && array_key_exists($request->category, Incident::CATEGORIES)) {
            $query->where('category', $request->category);
        }

        // ── Filter prioritas ────────────────────────────────────────────────
        if ($request->filled('priority') && array_key_exists($request->priority, Incident::PRIORITIES)) {
            $query->where('priority', $request->priority);
        }

        // ── Filter tanggal lapor ────────────────────────────────────────────
        if ($request->filled('date')) {
            $query->whereDate('reported_at', $request->date);
        }

        $incidents = $query->paginate(10)->withQueryString();

        // ── Statistik card (query terpisah agar tidak terpengaruh filter) ───
        $stats = [
            'total'     => Incident::count(),
            'new'       => Incident::where('status', 'new')->count(),
            'in_review' => Incident::where('status', 'in_review')->count(),
            'follow_up' => Incident::where('status', 'follow_up_required')->count(),
            'closed'    => Incident::where('status', 'closed')->count(),
        ];

        return view('incidents.index', compact('incidents', 'stats'));
    }
}
