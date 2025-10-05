<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BackupController extends Controller
{
    protected $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
        $this->middleware('auth');
    }

    /**
     * Display backup dashboard
     */
    public function index()
    {
        try {
            // Get backup requirement info
            $requirement = $this->backupService->getBackupRequirement();
            
            // Get backup history
            $history = $this->backupService->getBackupHistory(10);
            
            // Check user permissions
            $canBackup = $this->backupService->canPerformBackup();
            
            return view('admin.backup.index', compact('requirement', 'history', 'canBackup'));
            
        } catch (\Exception $e) {
            Log::error('Error loading backup dashboard: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat dashboard backup: ' . $e->getMessage());
        }
    }

    /**
     * Perform backup operation
     */
    public function performBackup(Request $request)
    {
        try {
            // Check permissions
            if (!$this->backupService->canPerformBackup()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk melakukan backup'
                ], 403);
            }

            // Validate request
            $request->validate([
                'month' => 'nullable|integer|min:1|max:12',
                'year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
                'force' => 'nullable|boolean'
            ]);

            $month = $request->input('month');
            $year = $request->input('year');
            $force = $request->input('force', false);

            // Check if backup already exists (if not forced)
            if (!$force && $month && $year) {
                $monthFormatted = sprintf("%02d", $month);
                $exists = $this->backupService->checkBackupExists($monthFormatted, $year);
                
                if ($exists > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "Backup untuk periode {$monthFormatted}-{$year} sudah ada ({$exists} records). Gunakan force=true untuk backup ulang.",
                        'exists' => true,
                        'existing_records' => $exists
                    ]);
                }
            }

            // Perform backup
            $result = $this->backupService->backupMonthlyData($month, $year);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => [
                        'backup_count' => $result['backup_count'],
                        'backup_time' => $result['backup_time'],
                        'total_original' => $result['total_original'] ?? null
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'error' => $result['error'] ?? null
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Backup operation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Backup gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get backup status for current period
     */
    public function getBackupStatus()
    {
        try {
            $requirement = $this->backupService->getBackupRequirement();
            
            return response()->json([
                'success' => true,
                'data' => $requirement
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting backup status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil status backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get backup history
     */
    public function getBackupHistory(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $limit = min(max($limit, 5), 50); // Between 5 and 50
            
            $history = $this->backupService->getBackupHistory($limit);
            
            return response()->json($history);
            
        } catch (\Exception $e) {
            Log::error('Error getting backup history: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get backup data for specific period
     */
    public function getBackupData(Request $request)
    {
        try {
            $request->validate([
                'month' => 'required|string|size:2',
                'year' => 'required|integer|min:2000',
                'limit' => 'nullable|integer|min:10|max:500',
                'offset' => 'nullable|integer|min:0'
            ]);

            $month = $request->input('month');
            $year = $request->input('year');
            $limit = $request->input('limit', 100);
            $offset = $request->input('offset', 0);

            $result = $this->backupService->getBackupData($month, $year, $limit, $offset);
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error('Error getting backup data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if backup exists for specific period
     */
    public function checkBackupExists(Request $request)
    {
        try {
            $request->validate([
                'month' => 'required|string|size:2',
                'year' => 'required|integer|min:2000'
            ]);

            $month = $request->input('month');
            $year = $request->input('year');

            $count = $this->backupService->checkBackupExists($month, $year);
            
            return response()->json([
                'success' => true,
                'exists' => $count > 0,
                'count' => $count
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error checking backup existence: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengecek backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show backup dashboard with requirement warning if needed
     */
    public function dashboard()
    {
        try {
            $requirement = $this->backupService->getBackupRequirement();
            $canBackup = $this->backupService->canPerformBackup();
            
            // If backup is required and user can perform backup, show modal
            $showBackupModal = $requirement['is_required'] && $canBackup;
            
            return view('admin.dashboard', compact('requirement', 'canBackup', 'showBackupModal'));
            
        } catch (\Exception $e) {
            Log::error('Error loading dashboard with backup info: ' . $e->getMessage());
            return view('admin.dashboard')->with('error', 'Gagal memuat informasi backup');
        }
    }
}