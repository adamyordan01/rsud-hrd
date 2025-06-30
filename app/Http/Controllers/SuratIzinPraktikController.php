<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Models\SuratIzinPraktik;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\EmployeeProfileService;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class SuratIzinPraktikController extends Controller
{
    protected $employeeProfileService;

    public function __construct(EmployeeProfileService $employeeProfileService)
    {
        $this->employeeProfileService = $employeeProfileService;
    }
    
    public function index($id)
    {
        $data = $this->employeeProfileService->getEmployeeProfile($id);
        return view('karyawan.surat-izin-praktik.index', $data);
    }

    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'tgl_sip' => 'required|date',
            'no_sip' => 'required|string|max:255',
            'ket' => 'nullable|string',
            'tgl_kadaluarsa' => 'nullable|date|after:tgl_sip',
            'sc_berkas' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'tgl_sip.required' => 'Kolom Tanggal SIP wajib diisi.',
            'tgl_sip.date' => 'Kolom Tanggal SIP harus berupa tanggal.',
            'no_sip.required' => 'Kolom No. SIP wajib diisi.',
            'no_sip.string' => 'Kolom No. SIP harus berupa teks.',
            'no_sip.max' => 'Kolom No. SIP maksimal 255 karakter.',
            'ket.string' => 'Kolom Keterangan harus berupa teks.',
            'tgl_kadaluarsa.date' => 'Kolom Tanggal Kadaluarsa harus berupa tanggal.',
            'tgl_kadaluarsa.after' => 'Kolom Tanggal Kadaluarsa harus setelah Tanggal SIP.',
            'sc_berkas.required' => 'Kolom Berkas SIP wajib diisi.',
            'sc_berkas.file' => 'Kolom Berkas SIP harus berupa file.',
            'sc_berkas.mimes' => 'Kolom Berkas SIP harus berupa file: pdf, jpg, jpeg, png.',
            'sc_berkas.max' => 'Kolom Berkas SIP maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat kesalahan dalam pengunggahan SIP.',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        // Ambil data karyawan untuk nama file
        $karyawan = Karyawan::where('kd_karyawan', $id)
            ->select('kd_karyawan', 'nama')
            ->first();

        // Format nama file sesuai dengan STR
        $namaKaryawan = str_replace(' ', '_', $karyawan->nama ?? 'Unknown');
        $noSip = str_replace('/', '-', $request->no_sip);

        $fileName = "Surat_Izin_Praktik-{$id}-{$namaKaryawan}-{$noSip}";
        $extension = $request->file('sc_berkas')->getClientOriginalExtension();
        $fullFileName = "{$fileName}.{$extension}";

        // Simpan file ke storage hrd_files
        $request->file('sc_berkas')->storeAs('sip_files', $fullFileName, 'hrd_files');

        // Get urut_sip berikutnya
        $urutSip = SuratIzinPraktik::where('kd_karyawan', $id)->max('urut_sip') + 1;

        // Insert manual menggunakan query builder untuk konsistensi
        DB::connection('sqlsrv')
            ->table('hrd_r_sip')
            ->insert([
                'kd_karyawan' => $id,
                'urut_sip' => $urutSip,
                'no_sip' => $request->no_sip,
                'tgl_sip' => Carbon::parse($request->tgl_sip)->format('Y-m-d'),
                'tgl_kadaluarsa' => $request->input('tidak_ada_masa_berlaku') ? null : ($request->tgl_kadaluarsa ? Carbon::parse($request->tgl_kadaluarsa)->format('Y-m-d') : null),
                'ket' => $request->ket,
                'sc_berkas' => $fullFileName,
                'status' => 1, // Default status pending
                'uploaded_by' => $request->user()->kd_karyawan,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'SIP berhasil diunggah.',
            'code' => 200,
        ]);
    }

    public function getSipData($id)
    {
        $sips = SuratIzinPraktik::where('kd_karyawan', $id);

        return DataTables::of($sips)
            ->addIndexColumn()
            ->editColumn('tgl_sip', function ($sip) {
                return Carbon::parse($sip->tgl_sip)->translatedFormat('d F Y');
            })
            ->editColumn('tgl_kadaluarsa', function ($sip) {
                return $sip->tgl_kadaluarsa ? 
                    Carbon::parse($sip->tgl_kadaluarsa)->translatedFormat('d F Y') 
                    : 'Seumur Hidup';
            })
            ->editColumn('status', function ($sip) {
                return view('karyawan.surat-izin-praktik._status', compact('sip'));
            })
            ->addColumn('action', function ($sip) {
                return view('karyawan.surat-izin-praktik._actions', compact('sip'));
            })
            ->addColumn('file', function ($sip) {
                return view('karyawan.surat-izin-praktik._file', compact('sip'));
            })
            ->rawColumns(['status', 'action', 'file'])
            ->make(true);
    }

    public function edit($id, $urut)
    {
        $sip = SuratIzinPraktik::where('kd_karyawan', $id)
            ->where('urut_sip', $urut)
            ->firstOrFail();

        $data = [
            'kd_karyawan' => $sip->kd_karyawan,
            'urut_sip' => $sip->urut_sip,
            'no_sip' => $sip->no_sip,
            'tgl_sip' => Carbon::parse($sip->tgl_sip)->format('Y-m-d'),
            'tgl_kadaluarsa' => $sip->tgl_kadaluarsa ? 
                Carbon::parse($sip->tgl_kadaluarsa)->format('Y-m-d') : null,
            'ket' => $sip->ket,
            'sc_berkas' => $sip->sc_berkas,
            // Menggunakan route untuk download file yang aman
            'url_file' => $sip->sc_berkas ? route('admin.karyawan.sip.download', ['id' => $id, 'urut' => $urut]) : null,
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
            'code' => 200,
        ]);
    }

    public function update(Request $request, $id, $urut)
    {
        $sip = SuratIzinPraktik::where('kd_karyawan', $id)
            ->where('urut_sip', $urut)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'tgl_sip' => 'required|date',
            'no_sip' => 'required|string|max:255',
            'ket' => 'nullable|string',
            'tgl_kadaluarsa' => 'nullable|date|after:tgl_sip',
            'sc_berkas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'tgl_sip.required' => 'Kolom Tanggal SIP wajib diisi.',
            'tgl_sip.date' => 'Kolom Tanggal SIP harus berupa tanggal.',
            'no_sip.required' => 'Kolom No. SIP wajib diisi.',
            'no_sip.string' => 'Kolom No. SIP harus berupa teks.',
            'no_sip.max' => 'Kolom No. SIP maksimal 255 karakter.',
            'ket.string' => 'Kolom Keterangan harus berupa teks.',
            'tgl_kadaluarsa.date' => 'Kolom Tanggal Kadaluarsa harus berupa tanggal.',
            'tgl_kadaluarsa.after' => 'Kolom Tanggal Kadaluarsa harus setelah Tanggal SIP.',
            'sc_berkas.file' => 'Kolom Berkas SIP harus berupa file.',
            'sc_berkas.mimes' => 'Kolom Berkas SIP harus berupa file: pdf, jpg, jpeg, png.',
            'sc_berkas.max' => 'Kolom Berkas SIP maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat kesalahan dalam pengunggahan SIP.',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        $karyawan = Karyawan::where('kd_karyawan', $id)
            ->select('kd_karyawan', 'nama')
            ->first();

        // Jika ada file baru yang diupload
        $fileName = $sip->sc_berkas; // Default ke file lama
        if ($request->hasFile('sc_berkas')) {
            // Hapus file lama jika ada menggunakan disk hrd_files
            if ($sip->sc_berkas && Storage::disk('hrd_files')->exists('sip_files/' . $sip->sc_berkas)) {
                Storage::disk('hrd_files')->delete('sip_files/' . $sip->sc_berkas);
            }

            // Format nama file baru
            $namaKaryawan = str_replace(' ', '_', $karyawan->nama ?? 'Unknown');
            $noSip = str_replace('/', '-', $request->no_sip);

            $fileName = "Surat_Izin_Praktik-{$id}-{$namaKaryawan}-{$noSip}";
            $extension = $request->file('sc_berkas')->getClientOriginalExtension();
            $fullFileName = "{$fileName}.{$extension}";

            // Simpan file baru ke disk hrd_files
            $request->file('sc_berkas')->storeAs('sip_files', $fullFileName, 'hrd_files');
            $fileName = $fullFileName;
        }

        // Update manual menggunakan query builder
        DB::connection('sqlsrv')
            ->table('hrd_r_sip')
            ->where('kd_karyawan', $id)
            ->where('urut_sip', $urut)
            ->update([
                'no_sip' => $request->no_sip,
                'tgl_sip' => Carbon::parse($request->tgl_sip)->format('Y-m-d'),
                'tgl_kadaluarsa' => $request->input('tidak_ada_masa_berlaku') ? null : ($request->tgl_kadaluarsa ? Carbon::parse($request->tgl_kadaluarsa)->format('Y-m-d') : null),
                'ket' => $request->ket,
                'sc_berkas' => $fileName,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'SIP berhasil diperbarui.',
            'code' => 200,
        ]);
    }

    public function destroy($id, $urut)
    {
        try {
            $sip = SuratIzinPraktik::where('kd_karyawan', $id)
                ->where('urut_sip', $urut)
                ->firstOrFail();

            // Hapus file jika ada
            if ($sip->sc_berkas && Storage::disk('hrd_files')->exists('sip_files/' . $sip->sc_berkas)) {
                Storage::disk('hrd_files')->delete('sip_files/' . $sip->sc_berkas);
            }

            // Hapus data dari database menggunakan query builder
            DB::connection('sqlsrv')
                ->table('hrd_r_sip')
                ->where('kd_karyawan', $id)
                ->where('urut_sip', $urut)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'SIP berhasil dihapus.',
                'code' => 200,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus SIP: ' . $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    // Method baru untuk download file dengan keamanan
    public function downloadFile($id, $urut)
    {
        $sip = SuratIzinPraktik::where('kd_karyawan', $id)
            ->where('urut_sip', $urut)
            ->firstOrFail();

        if (!$sip->sc_berkas) {
            abort(404, 'File tidak ditemukan');
        }

        $filePath = 'sip_files/' . $sip->sc_berkas;
        
        // Cek apakah file ada di disk hrd_files
        if (!Storage::disk('hrd_files')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        // Download file dengan nama yang sesuai
        $fileName = $sip->sc_berkas;
        $fileContent = Storage::disk('hrd_files')->get($filePath);
        $mimeType = Storage::disk('hrd_files')->mimeType($filePath);

        return response($fileContent)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $fileName . '"');
    }
}