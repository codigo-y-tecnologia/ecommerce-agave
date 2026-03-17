<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Artisan;

class MonitoringController extends Controller
{
    public function index()
    {
        return view('superadmin.monitoring.index');
    }

    /**
     * Endpoint JSON que el frontend consulta cada 30s.
     * Devuelve todas las métricas del sistema.
     */
    public function metrics()
    {
        return response()->json([
            'server'   => $this->getServerMetrics(),
            'app'      => $this->getAppMetrics(),
            'database' => $this->getDatabaseMetrics(),
            'queues'   => $this->getQueueMetrics(),
            'cache'    => $this->getCacheMetrics(),
            'updated'  => now()->format('H:i:s'),
        ]);
    }

    // ══════════════════════════════════════════════════════
    // SERVIDOR
    // ══════════════════════════════════════════════════════
    private function getServerMetrics(): array
    {
        $metrics = [
            'os'          => PHP_OS_FAMILY,
            'php_version' => PHP_VERSION,
            'uptime'      => $this->getUptime(),
            'cpu_usage'   => null,
            'memory'      => $this->getMemoryUsage(),
            'disk'        => $this->getDiskUsage(),
        ];

        // CPU — solo disponible en Linux
        if (PHP_OS_FAMILY === 'Linux') {
            $metrics['cpu_usage'] = $this->getCpuUsage();
        }

        return $metrics;
    }

    private function getCpuUsage(): ?float
    {
        try {
            // Lee /proc/stat dos veces con 100ms de diferencia para calcular %
            $stat1 = file_get_contents('/proc/stat');
            usleep(100000);
            $stat2 = file_get_contents('/proc/stat');

            $cpu1 = array_slice(explode(' ', preg_replace('/\s+/', ' ', explode("\n", $stat1)[0])), 1);
            $cpu2 = array_slice(explode(' ', preg_replace('/\s+/', ' ', explode("\n", $stat2)[0])), 1);

            $total1 = array_sum($cpu1);
            $total2 = array_sum($cpu2);
            $idle1  = $cpu1[3] ?? 0;
            $idle2  = $cpu2[3] ?? 0;

            $totalDiff = $total2 - $total1;
            $idleDiff  = $idle2  - $idle1;

            if ($totalDiff === 0) return 0.0;

            return round((($totalDiff - $idleDiff) / $totalDiff) * 100, 1);
        } catch (\Throwable) {
            return null;
        }
    }

    private function getMemoryUsage(): array
    {
        if (PHP_OS_FAMILY === 'Linux') {
            try {
                $meminfo = file_get_contents('/proc/meminfo');
                preg_match('/MemTotal:\s+(\d+)/',     $meminfo, $total);
                preg_match('/MemAvailable:\s+(\d+)/', $meminfo, $available);

                $totalKb     = (int)($total[1]     ?? 0);
                $availableKb = (int)($available[1] ?? 0);
                $usedKb      = $totalKb - $availableKb;

                return [
                    'total_mb'  => round($totalKb / 1024),
                    'used_mb'   => round($usedKb  / 1024),
                    'free_mb'   => round($availableKb / 1024),
                    'percent'   => $totalKb > 0 ? round(($usedKb / $totalKb) * 100, 1) : 0,
                    'php_used'  => round(memory_get_usage(true) / 1024 / 1024, 1),
                    'php_peak'  => round(memory_get_peak_usage(true) / 1024 / 1024, 1),
                ];
            } catch (\Throwable) {
            }
        }

        // Fallback: solo memoria de PHP
        return [
            'total_mb'  => null,
            'used_mb'   => null,
            'free_mb'   => null,
            'percent'   => null,
            'php_used'  => round(memory_get_usage(true) / 1024 / 1024, 1),
            'php_peak'  => round(memory_get_peak_usage(true) / 1024 / 1024, 1),
        ];
    }

    private function getDiskUsage(): array
    {
        $path = base_path();
        return [
            'total_gb' => round(disk_total_space($path) / 1024 / 1024 / 1024, 1),
            'free_gb'  => round(disk_free_space($path)  / 1024 / 1024 / 1024, 1),
            'used_gb'  => round((disk_total_space($path) - disk_free_space($path)) / 1024 / 1024 / 1024, 1),
            'percent'  => round(((disk_total_space($path) - disk_free_space($path)) / disk_total_space($path)) * 100, 1),
        ];
    }

    private function getUptime(): ?string
    {
        if (PHP_OS_FAMILY === 'Linux') {
            try {
                $uptime  = (float) file_get_contents('/proc/uptime');
                $days    = floor($uptime / 86400);
                $hours   = floor(($uptime % 86400) / 3600);
                $minutes = floor(($uptime % 3600) / 60);
                return "{$days}d {$hours}h {$minutes}m";
            } catch (\Throwable) {
            }
        }
        return null;
    }

    // ══════════════════════════════════════════════════════
    // APLICACIÓN
    // ══════════════════════════════════════════════════════
    private function getAppMetrics(): array
    {
        return [
            'environment'    => app()->environment(),
            'debug_mode'     => config('app.debug'),
            'laravel_version' => app()->version(),
            'timezone'       => config('app.timezone'),
            'log_errors_24h' => $this->countRecentLogErrors(),
            'storage_writable' => is_writable(storage_path()),
            'cache_writable'   => is_writable(storage_path('framework/cache')),
        ];
    }

    private function countRecentLogErrors(): int
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            if (!file_exists($logFile)) return 0;

            // Leer últimas 5000 líneas del log (eficiente)
            $lines   = $this->tailFile($logFile, 5000);
            $cutoff  = now()->subHours(24)->format('Y-m-d');
            $count   = 0;

            foreach ($lines as $line) {
                if (str_contains($line, '.ERROR') || str_contains($line, '.CRITICAL')) {
                    // Verificar que sea de las últimas 24h
                    if (preg_match('/\[(\d{4}-\d{2}-\d{2})/', $line, $match)) {
                        if ($match[1] >= $cutoff) $count++;
                    }
                }
            }
            return $count;
        } catch (\Throwable) {
            return 0;
        }
    }

    private function tailFile(string $path, int $lines): array
    {
        $file   = new \SplFileObject($path, 'r');
        $file->seek(PHP_INT_MAX);
        $total  = $file->key();
        $start  = max(0, $total - $lines);
        $file->seek($start);
        $result = [];
        while (!$file->eof()) {
            $result[] = $file->current();
            $file->next();
        }
        return $result;
    }

    // ══════════════════════════════════════════════════════
    // 🗄️ BASE DE DATOS
    // ══════════════════════════════════════════════════════
    private function getDatabaseMetrics(): array
    {
        try {
            $start    = microtime(true);
            DB::select('SELECT 1');
            $pingMs   = round((microtime(true) - $start) * 1000, 2);

            $driver   = DB::getDriverName();
            $database = DB::getDatabaseName();
            $tables   = DB::select('SHOW TABLE STATUS');

            $totalSizeBytes = 0;
            foreach ($tables as $table) {
                $totalSizeBytes += ($table->Data_length ?? 0) + ($table->Index_length ?? 0);
            }

            return [
                'status'       => 'online',
                'driver'       => $driver,
                'database'     => $database,
                'ping_ms'      => $pingMs,
                'ping_status'  => $pingMs < 10 ? 'good' : ($pingMs < 50 ? 'warning' : 'critical'),
                'tables'       => count($tables),
                'size_mb'      => round($totalSizeBytes / 1024 / 1024, 2),
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => 'offline',
                'error'   => $e->getMessage(),
                'ping_ms' => null,
            ];
        }
    }

    // ══════════════════════════════════════════════════════
    // QUEUES
    // ══════════════════════════════════════════════════════
    private function getQueueMetrics(): array
    {
        try {
            $connection = config('queue.default');

            // Jobs fallidos (tabla failed_jobs)
            $failed = DB::table('failed_jobs')->count();

            // Jobs pendientes (tabla jobs si usas database driver)
            $pending = 0;
            if ($connection === 'database' && $this->tableExists('jobs')) {
                $pending = DB::table('jobs')->count();
            }

            // Jobs procesados en las últimas 24h (si tienes la tabla jobs_batches)
            $batches = 0;
            if ($this->tableExists('job_batches')) {
                $batches = DB::table('job_batches')
                    ->whereNotNull('finished_at')
                    ->where('finished_at', '>=', now()->subHours(24)->timestamp)
                    ->count();
            }

            return [
                'connection'   => $connection,
                'pending'      => $pending,
                'failed'       => $failed,
                'batches_24h'  => $batches,
                'status'       => $failed > 0 ? 'warning' : 'healthy',
            ];
        } catch (\Throwable $e) {
            return ['status' => 'error', 'error' => $e->getMessage()];
        }
    }

    private function tableExists(string $table): bool
    {
        try {
            DB::select("SHOW TABLES LIKE '{$table}'");
            return (bool) DB::select("SHOW TABLES LIKE '{$table}'");
        } catch (\Throwable) {
            return false;
        }
    }

    // ══════════════════════════════════════════════════════
    // ⚡ CACHÉ
    // ══════════════════════════════════════════════════════
    private function getCacheMetrics(): array
    {
        $driver = config('cache.default');

        // Test de escritura/lectura/borrado
        $testKey = '_monitoring_test_' . time();
        try {
            $start = microtime(true);
            Cache::put($testKey, 'ok', 10);
            $read  = Cache::get($testKey);
            Cache::forget($testKey);
            $pingMs = round((microtime(true) - $start) * 1000, 2);
            $working = ($read === 'ok');
        } catch (\Throwable) {
            $pingMs  = null;
            $working = false;
        }

        // Tamaño del caché en disco (si es file driver)
        $sizeKb = null;
        if ($driver === 'file') {
            $cachePath = storage_path('framework/cache/data');
            if (is_dir($cachePath)) {
                $sizeKb = round($this->directorySizeBytes($cachePath) / 1024, 1);
            }
        }

        return [
            'driver'   => $driver,
            'working'  => $working,
            'ping_ms'  => $pingMs,
            'size_kb'  => $sizeKb,
            'status'   => $working ? 'healthy' : 'error',
        ];
    }

    private function directorySizeBytes(string $path): int
    {
        $size = 0;
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $file) {
            if ($file->isFile()) $size += $file->getSize();
        }
        return $size;
    }
}
