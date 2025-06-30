<?php

namespace App\Http\Controllers\Riwayat;

use Carbon\Carbon;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Services\EmployeeProfileService;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class RiwayatKerjaController extends Controller
{
    protected $employeeProfileService;

    public function __construct(EmployeeProfileService $employeeProfileService)
    {
        $this->employeeProfileService = $employeeProfileService;
    }
    
    public function index($id)
    {
        $data = $this->employeeProfileService->getEmployeeProfile($id);
        return view('karyawan.riwayat-kerja.index', $data);
    }

    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'pejabat' => 'required|string|max:255',
            'tgl_sk' => 'required|date',
            'no_sk' => 'required|string|max:255',
            'tmt' => 'required|date',
            'perusahaan' => 'required|string|max:255',
            'ket' => 'nullable|string',
            'sc_berkas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'pejabat.required' => 'Kolom Pejabat wajib diisi.',
            'pejabat.string' => 'Kolom Pejabat harus berupa teks.',
            'pejabat.max' => 'Kolom Pejabat maksimal 255 karakter.',
            'tgl_sk.required' => 'Kolom Tanggal SK wajib diisi.',
            'tgl_sk.date' => 'Kolom Tanggal SK harus berupa tanggal.',
            'no_sk.required' => 'Kolom No. SK wajib diisi.',
            'no_sk.string' => 'Kolom No. SK harus berupa teks.',
            'no_sk.max' => 'Kolom No. SK maksimal 255 karakter.',
            'tmt.required' => 'Kolom TMT wajib diisi.',
            'tmt.date' => 'Kolom TMT harus berupa tanggal.',
            'perusahaan.required' => 'Kolom Perusahaan wajib diisi.',
            'perusahaan.string' => 'Kolom Perusahaan harus berupa teks.',
            'perusahaan.max' => 'Kolom Perusahaan maksimal 255 karakter.',
            'ket.string' => 'Kolom Keterangan harus berupa teks.',
            'sc_berkas.file' => 'Kolom Berkas harus berupa file.',
            'sc_berkas.mimes' => 'Kolom Berkas harus berupa file: pdf, jpg, jpeg, png.',
            'sc_berkas.max' => 'Kolom Berkas maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat kesalahan dalam pengunggahan Riwayat Kerja.',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        // Get urut_kerja berikutnya
        $urutKerja = DB::connection('sqlsrv')
            ->table('hrd_r_kerja')
            ->where('kd_karyawan', $id)
            ->max('urut_kerja') + 1;

        $fileName = null;
        
        // Handle file upload jika ada
        if ($request->hasFile('sc_berkas')) {
            // Ambil data karyawan untuk nama file
            $karyawan = Karyawan::where('kd_karyawan', $id)
                ->select('kd_karyawan', 'nama')
                ->first();

            $namaKaryawan = str_replace(' ', '_', $karyawan->nama ?? 'Unknown');
            $noSk = str_replace(['/', ' '], '-', $request->no_sk);

            $fileNameBase = "Riwayat_Kerja-{$id}-{$namaKaryawan}-{$noSk}";
            $extension = $request->file('sc_berkas')->getClientOriginalExtension();
            $fileName = "{$fileNameBase}.{$extension}";

            // Simpan file ke storage hrd_files
            $request->file('sc_berkas')->storeAs('riwayat_kerja_files', $fileName, 'hrd_files');
        }

        // Insert manual menggunakan query builder
        DB::connection('sqlsrv')
            ->table('hrd_r_kerja')
            ->insert([
                'kd_karyawan' => $id,
                'urut_kerja' => $urutKerja,
                'pejabat' => $request->pejabat,
                'tgl_sk' => Carbon::parse($request->tgl_sk)->format('Y-m-d'),
                'no_sk' => $request->no_sk,
                'tmt' => Carbon::parse($request->tmt)->format('Y-m-d'),
                'perusahaan' => $request->perusahaan,
                'ket' => $request->ket,
                'sc_berkas' => $fileName,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Riwayat Kerja berhasil ditambahkan.',
            'code' => 200,
        ]);
    }

    public function getRiwayatKerjaData($id)
    {
        // Menggunakan Collection untuk menghindari masalah ORDER BY di SQL Server
        $riwayatKerja = DB::connection('sqlsrv')
            ->table('hrd_r_kerja')
            ->where('kd_karyawan', $id)
            ->get()
            ->sortByDesc('tmt')
            ->sortByDesc('tgl_sk');

        return DataTables::of($riwayatKerja)
            ->addIndexColumn()
            ->editColumn('tgl_sk', function ($row) {
                return Carbon::parse($row->tgl_sk)->translatedFormat('d M Y');
            })
            ->editColumn('tmt', function ($row) {
                return Carbon::parse($row->tmt)->translatedFormat('d M Y');
            })
            ->editColumn('pejabat_perusahaan', function ($row) {
                return $row->pejabat . '<br><small class="text-muted">' . $row->perusahaan . '</small>';
            })
            ->editColumn('sk_tmt', function ($row) {
                return $row->no_sk . '<br><small class="text-muted">TMT: ' . Carbon::parse($row->tmt)->translatedFormat('d M Y') . '</small>';
            })
            ->addColumn('file', function ($row) {
                return view('karyawan.riwayat-kerja._file', ['riwayatKerja' => $row]);
            })
            ->addColumn('action', function ($row) {
                return view('karyawan.riwayat-kerja._actions', ['riwayatKerja' => $row]);
            })
            ->rawColumns(['pejabat_perusahaan', 'sk_tmt', 'file', 'action'])
            ->make(true);
    }

    public function edit($id, $urut)
    {
        $riwayatKerja = DB::connection('sqlsrv')
            ->table('hrd_r_kerja')
            ->where('kd_karyawan', $id)
            ->where('urut_kerja', $urut)
            ->first();

        if (!$riwayatKerja) {
            return response()->json([
                'success' => false,
                'message' => 'Data riwayat kerja tidak ditemukan.',
                'code' => 404,
            ], 404);
        }

        $data = [
            'kd_karyawan' => $riwayatKerja->kd_karyawan,
            'urut_kerja' => $riwayatKerja->urut_kerja,
            'pejabat' => $riwayatKerja->pejabat,
            'tgl_sk' => Carbon::parse($riwayatKerja->tgl_sk)->format('Y-m-d'),
            'no_sk' => $riwayatKerja->no_sk,
            'tmt' => Carbon::parse($riwayatKerja->tmt)->format('Y-m-d'),
            'perusahaan' => $riwayatKerja->perusahaan,
            'ket' => $riwayatKerja->ket,
            'sc_berkas' => $riwayatKerja->sc_berkas,
            'url_file' => $riwayatKerja->sc_berkas ? route('admin.karyawan.riwayat-kerja.download', ['id' => $id, 'urut' => $urut]) : null,
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
            'code' => 200,
        ]);
    }

    public function update(Request $request, $id, $urut)
    {
        $riwayatKerja = DB::connection('sqlsrv')
            ->table('hrd_r_kerja')
            ->where('kd_karyawan', $id)
            ->where('urut_kerja', $urut)
            ->first();

        if (!$riwayatKerja) {
            return response()->json([
                'success' => false,
                'message' => 'Data riwayat kerja tidak ditemukan.',
                'code' => 404,
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'pejabat' => 'required|string|max:255',
            'tgl_sk' => 'required|date',
            'no_sk' => 'required|string|max:255',
            'tmt' => 'required|date',
            'perusahaan' => 'required|string|max:255',
            'ket' => 'nullable|string',
            'sc_berkas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'pejabat.required' => 'Kolom Pejabat wajib diisi.',
            'pejabat.string' => 'Kolom Pejabat harus berupa teks.',
            'pejabat.max' => 'Kolom Pejabat maksimal 255 karakter.',
            'tgl_sk.required' => 'Kolom Tanggal SK wajib diisi.',
            'tgl_sk.date' => 'Kolom Tanggal SK harus berupa tanggal.',
            'no_sk.required' => 'Kolom No. SK wajib diisi.',
            'no_sk.string' => 'Kolom No. SK harus berupa teks.',
            'no_sk.max' => 'Kolom No. SK maksimal 255 karakter.',
            'tmt.required' => 'Kolom TMT wajib diisi.',
            'tmt.date' => 'Kolom TMT harus berupa tanggal.',
            'perusahaan.required' => 'Kolom Perusahaan wajib diisi.',
            'perusahaan.string' => 'Kolom Perusahaan harus berupa teks.',
            'perusahaan.max' => 'Kolom Perusahaan maksimal 255 karakter.',
            'ket.string' => 'Kolom Keterangan harus berupa teks.',
            'sc_berkas.file' => 'Kolom Berkas harus berupa file.',
            'sc_berkas.mimes' => 'Kolom Berkas harus berupa file: pdf, jpg, jpeg, png.',
            'sc_berkas.max' => 'Kolom Berkas maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat kesalahan dalam pengunggahan Riwayat Kerja.',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        // Handle file upload jika ada file baru
        $fileName = $riwayatKerja->sc_berkas; // Default ke file lama
        if ($request->hasFile('sc_berkas')) {
            // Hapus file lama jika ada
            if ($riwayatKerja->sc_berkas && Storage::disk('hrd_files')->exists('riwayat_kerja_files/' . $riwayatKerja->sc_berkas)) {
                Storage::disk('hrd_files')->delete('riwayat_kerja_files/' . $riwayatKerja->sc_berkas);
            }

            // Ambil data karyawan untuk nama file
            $karyawan = Karyawan::where('kd_karyawan', $id)
                ->select('kd_karyawan', 'nama')
                ->first();

            $namaKaryawan = str_replace(' ', '_', $karyawan->nama ?? 'Unknown');
            $noSk = str_replace(['/', ' '], '-', $request->no_sk);

            $fileNameBase = "Riwayat_Kerja-{$id}-{$namaKaryawan}-{$noSk}";
            $extension = $request->file('sc_berkas')->getClientOriginalExtension();
            $fileName = "{$fileNameBase}.{$extension}";

            // Simpan file baru
            $request->file('sc_berkas')->storeAs('riwayat_kerja_files', $fileName, 'hrd_files');
        }

        // Update manual menggunakan query builder
        DB::connection('sqlsrv')
            ->table('hrd_r_kerja')
            ->where('kd_karyawan', $id)
            ->where('urut_kerja', $urut)
            ->update([
                'pejabat' => $request->pejabat,
                'tgl_sk' => Carbon::parse($request->tgl_sk)->format('Y-m-d'),
                'no_sk' => $request->no_sk,
                'tmt' => Carbon::parse($request->tmt)->format('Y-m-d'),
                'perusahaan' => $request->perusahaan,
                'ket' => $request->ket,
                'sc_berkas' => $fileName,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Riwayat Kerja berhasil diperbarui.',
            'code' => 200,
        ]);
    }

    public function destroy($id, $urut)
    {
        try {
            $riwayatKerja = DB::connection('sqlsrv')
                ->table('hrd_r_kerja')
                ->where('kd_karyawan', $id)
                ->where('urut_kerja', $urut)
                ->first();

            if (!$riwayatKerja) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data riwayat kerja tidak ditemukan.',
                    'code' => 404,
                ], 404);
            }

            // Hapus file jika ada
            if ($riwayatKerja->sc_berkas && Storage::disk('hrd_files')->exists('riwayat_kerja_files/' . $riwayatKerja->sc_berkas)) {
                Storage::disk('hrd_files')->delete('riwayat_kerja_files/' . $riwayatKerja->sc_berkas);
            }

            // Hapus data dari database
            DB::connection('sqlsrv')
                ->table('hrd_r_kerja')
                ->where('kd_karyawan', $id)
                ->where('urut_kerja', $urut)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Riwayat Kerja berhasil dihapus.',
                'code' => 200,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus Riwayat Kerja: ' . $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    // Method untuk download file dengan keamanan
    public function downloadFile($id, $urut)
    {
        $riwayatKerja = DB::connection('sqlsrv')
            ->table('hrd_r_kerja')
            ->where('kd_karyawan', $id)
            ->where('urut_kerja', $urut)
            ->first();

        if (!$riwayatKerja || !$riwayatKerja->sc_berkas) {
            abort(404, 'File tidak ditemukan');
        }

        $filePath = 'riwayat_kerja_files/' . $riwayatKerja->sc_berkas;
        
        // Cek apakah file ada di disk hrd_files
        if (!Storage::disk('hrd_files')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        // Download file dengan nama yang sesuai
        $fileName = $riwayatKerja->sc_berkas;
        $fileContent = Storage::disk('hrd_files')->get($filePath);
        $mimeType = Storage::disk('hrd_files')->mimeType($filePath);

        return response($fileContent)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $fileName . '"');
    }
}
