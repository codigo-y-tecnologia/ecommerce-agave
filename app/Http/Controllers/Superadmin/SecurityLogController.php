<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SecurityLog;
use App\Services\System\SecurityLoggerService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class SecurityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = SecurityLog::with('user')->latest();

        // Filtros
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', "%{$request->search}%")
                    ->orWhere('ip_address', 'like', "%{$request->search}%")
                    ->orWhere('event_type', 'like', "%{$request->search}%");
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(25)->withQueryString();

        // Estadísticas del encabezado
        $stats = [
            'critical_unresolved' => SecurityLog::critical()->unresolved()->count(),
            'today'               => SecurityLog::whereDate('created_at', today())->count(),
            'failed_logins_today' => SecurityLog::where('event_type', 'login_failed')
                ->whereDate('created_at', today())->count(),
            'total'               => SecurityLog::count(),
        ];

        return view('superadmin.security.index', compact('logs', 'stats'));
    }

    public function show(SecurityLog $log)
    {
        return view('superadmin.security.show', compact('log'));
    }

    public function resolve(SecurityLog $log)
    {
        $log->update([
            'is_resolved' => true,
            'resolved_at' => now(),
            'resolved_by' => Auth::id(),
        ]);

        // Log del log: registrar que se resolvió
        SecurityLoggerService::log(
            'log_resolved',
            'info',
            'admin',
            "Alerta de seguridad #{$log->id} marcada como resuelta",
            ['log_id' => $log->id, 'original_event' => $log->event_type, 'resolved_by' => Auth::id()]
        );

        return back()->with('success', 'Alerta marcada como resuelta.');
    }

    public function exportPdf(Request $request)
    {
        $query = SecurityLog::with('user')->latest();

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Para el PDF limitamos a 500 registros
        $logs  = $query->limit(500)->get();
        $stats = [
            'critical_unresolved' => SecurityLog::critical()->unresolved()->count(),
            'today'               => SecurityLog::whereDate('created_at', today())->count(),
            'total'               => SecurityLog::count(),
        ];

        $pdf = Pdf::loadView('superadmin.security.pdf', compact('logs', 'stats'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('security-logs-' . now()->format('Y-m-d') . '.pdf');
    }
}
