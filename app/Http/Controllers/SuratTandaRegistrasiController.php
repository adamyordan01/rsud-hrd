<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SuratTandaRegistrasi;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Services\EmployeeProfileService;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class SuratTandaRegistrasiController extends Controller
{
    protected $employeeProfileService;

    public function __construct(EmployeeProfileService $employeeProfileService)
    {
        $this->employeeProfileService = $employeeProfileService;
    }
    
    public function index($id)
    {
        $data = $this->employeeProfileService->getEmployeeProfile($id);
        return view('karyawan.surat-tanda-registrasi.index', $data);
    }

    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'tgl_str' => 'required|date',
            'no_str' => 'required|string|max:255',
            'ket' => 'nullable|string',
            'tgl_kadaluarsa' => 'nullable|date|after:tgl_str',
            'sc_berkas' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'tgl_str.required' => 'Kolom Tanggal STR wajib diisi.',
            'tgl_str.date' => 'Kolom Tanggal STR harus berupa tanggal.',
            'no_str.required' => 'Kolom No. STR wajib diisi.',
            'no_str.string' => 'Kolom No. STR harus berupa teks.',
            'no_str.max' => 'Kolom No. STR maksimal 255 karakter.',
            'ket.string' => 'Kolom Keterangan harus berupa teks.',
            'tgl_kadaluarsa.date' => 'Kolom Tanggal Kadaluarsa harus berupa tanggal.',
            'tgl_kadaluarsa.after' => 'Kolom Tanggal Kadaluarsa harus setelah Tanggal STR.',
            'sc_berkas.required' => 'Kolom Berkas STR wajib diisi.',
            'sc_berkas.file' => 'Kolom Berkas STR harus berupa file.',
            'sc_berkas.mimes' => 'Kolom Berkas STR harus berupa file: pdf, jpg, jpeg, png.',
            'sc_berkas.max' => 'Kolom Berkas STR maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat kesalahan dalam pengunggahan STR.',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        // Ambil data karyawan untuk nama file
        $karyawan = Karyawan::where('kd_karyawan', $id)
            ->select('kd_karyawan', 'nama')
            ->first();

        // Format nama file sesuai dengan method update
        $namaKaryawan = str_replace(' ', '_', $karyawan->nama ?? 'Unknown');
        $noStr = str_replace('/', '-', $request->no_str);

        $fileName = "Surat_Tanda_Registrasi-{$id}-{$namaKaryawan}-{$noStr}";
        $extension = $request->file('sc_berkas')->getClientOriginalExtension();
        $fullFileName = "{$fileName}.{$extension}";

        // Simpan file ke storage hrd_files
        $request->file('sc_berkas')->storeAs('str_files', $fullFileName, 'hrd_files');

        // Get urut_str berikutnya
        $urutStr = SuratTandaRegistrasi::where('kd_karyawan', $id)->max('urut_str') + 1;

        // Insert manual menggunakan query builder untuk konsistensi dengan update
        DB::connection('sqlsrv')
            ->table('hrd_r_str')
            ->insert([
                'kd_karyawan' => $id,
                'urut_str' => $urutStr,
                'no_str' => $request->no_str,
                'tgl_str' => Carbon::parse($request->tgl_str)->format('Y-m-d'),
                'tgl_kadaluarsa' => $request->input('tidak_ada_masa_berlaku') ? null : ($request->tgl_kadaluarsa ? Carbon::parse($request->tgl_kadaluarsa)->format('Y-m-d') : null),
                'ket' => $request->ket,
                'sc_berkas' => $fullFileName,
                'status' => 1, // Default status pending
                'uploaded_by' => $request->user()->kd_karyawan,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'STR berhasil diunggah.',
            'code' => 200,
        ]);
    }

    public function getStrData($id)
    {
        $strs = SuratTandaRegistrasi::where('kd_karyawan', $id);

        return DataTables::of($strs)
            ->addIndexColumn()
            ->editColumn('tgl_str', function ($str) {
                return Carbon::parse($str->tgl_str)->translatedFormat('d F Y');
            })
            ->editColumn('tgl_kadaluarsa', function ($str) {
                return $str->tgl_kadaluarsa ? 
                    Carbon::parse($str->tgl_kadaluarsa)->translatedFormat('d F Y') 
                    : 'Seumur Hidup';
            })
            ->editColumn('status', function ($str) {
                return view('karyawan.surat-tanda-registrasi._status', compact('str'));
            })
            ->addColumn('action', function ($str) {
                return view('karyawan.surat-tanda-registrasi._actions', compact('str'));
            })
            ->addColumn('file', function ($str) {
                return view('karyawan.surat-tanda-registrasi._file', compact('str'));
            })
            ->rawColumns(['status', 'action', 'file'])
            ->make(true);
    }

    public function edit($id, $urut)
    {
        $str = SuratTandaRegistrasi::where('kd_karyawan', $id)
            ->where('urut_str', $urut)
            ->firstOrFail();

        $data = [
            'kd_karyawan' => $str->kd_karyawan,
            'urut_str' => $str->urut_str,
            'no_str' => $str->no_str,
            'tgl_str' => Carbon::parse($str->tgl_str)->format('Y-m-d'),
            'tgl_kadaluarsa' => $str->tgl_kadaluarsa ? Carbon::parse($str->tgl_kadaluarsa)->format('Y-m-d') : null,
            'ket' => $str->ket,
            'sc_berkas' => $str->sc_berkas,
            // Menggunakan route untuk download file yang aman
            'url_file' => $str->sc_berkas ? route('admin.karyawan.str.download', ['id' => $id, 'urut' => $urut]) : null,
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
            'code' => 200,
        ]);
    }

    public function update(Request $request, $id, $urut)
    {
        $str = SuratTandaRegistrasi::where('kd_karyawan', $id)
            ->where('urut_str', $urut)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'tgl_str' => 'required|date',
            'no_str' => 'required|string|max:255',
            'ket' => 'nullable|string',
            'tgl_kadaluarsa' => 'nullable|date|after:tgl_str',
            'sc_berkas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'tgl_str.required' => 'Kolom Tanggal STR wajib diisi.',
            'tgl_str.date' => 'Kolom Tanggal STR harus berupa tanggal.',
            'no_str.required' => 'Kolom No. STR wajib diisi.',
            'no_str.string' => 'Kolom No. STR harus berupa teks.',
            'no_str.max' => 'Kolom No. STR maksimal 255 karakter.',
            'ket.string' => 'Kolom Keterangan harus berupa teks.',
            'tgl_kadaluarsa.date' => 'Kolom Tanggal Kadaluarsa harus berupa tanggal.',
            'tgl_kadaluarsa.after' => 'Kolom Tanggal Kadaluarsa harus setelah Tanggal STR.',
            'sc_berkas.file'=> 'Kolom Berkas STR harus berupa file.',
            'sc_berkas.mimes' => 'Kolom Berkas STR harus berupa file: pdf, jpg, jpeg, png.',
            'sc_berkas.max' => 'Kolom Berkas STR maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat kesalahan dalam pengunggahan STR.',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        $karyawan = Karyawan::where('kd_karyawan', $id)
            ->select('kd_karyawan', 'nama')
            ->first();

        // Jika ada file baru yang diupload
        $fileName = $str->sc_berkas; // Default ke file lama
        if ($request->hasFile('sc_berkas')) {
            // Hapus file lama jika ada menggunakan disk hrd_files
            if ($str->sc_berkas && Storage::disk('hrd_files')->exists('str_files/' . $str->sc_berkas)) {
                Storage::disk('hrd_files')->delete('str_files/' . $str->sc_berkas);
            }

            // Format nama file baru
            $namaKaryawan = str_replace(' ', '_', $karyawan->nama ?? 'Unknown');
            $noStr = str_replace('/', '-', $request->no_str);

            $fileName = "Surat_Tanda_Registrasi-{$id}-{$namaKaryawan}-{$noStr}";
            $extension = $request->file('sc_berkas')->getClientOriginalExtension();
            $fullFileName = "{$fileName}.{$extension}";

            // Simpan file baru ke disk hrd_files
            $request->file('sc_berkas')->storeAs('str_files', $fullFileName, 'hrd_files');
            $fileName = $fullFileName;
        }

        // Update manual menggunakan query builder
        DB::connection('sqlsrv')
            ->table('hrd_r_str')
            ->where('kd_karyawan', $id)
            ->where('urut_str', $urut)
            ->update([
                'no_str' => $request->no_str,
                'tgl_str' => Carbon::parse($request->tgl_str)->format('Y-m-d'),
                'tgl_kadaluarsa' => $request->input('tidak_ada_masa_berlaku') ? null : ($request->tgl_kadaluarsa ? Carbon::parse($request->tgl_kadaluarsa)->format('Y-m-d') : null),
                'ket' => $request->ket,
                'sc_berkas' => $fileName,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'STR berhasil diperbarui.',
            'code' => 200,
        ]);
    }

    public function destroy($id, $urut)
    {
        try {
            $str = SuratTandaRegistrasi::where('kd_karyawan', $id)
                ->where('urut_str', $urut)
                ->firstOrFail();

            // Hapus file dari disk hrd_files jika ada
            if ($str->sc_berkas && Storage::disk('hrd_files')->exists('str_files/' . $str->sc_berkas)) {
                Storage::disk('hrd_files')->delete('str_files/' . $str->sc_berkas);
            }

            // Hapus data dari database
            DB::connection('sqlsrv')
                ->table('hrd_r_str')
                ->where('kd_karyawan', $id)
                ->where('urut_str', $urut)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'STR berhasil dihapus.',
                'code' => 200,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus STR: ' . $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    public function downloadFile($id, $urut)
    {
        $str = SuratTandaRegistrasi::where('kd_karyawan', $id)
            ->where('urut_str', $urut)
            ->firstOrFail();

        if (!$str->sc_berkas) {
            abort(404, 'File tidak ditemukan');
        }

        $filePath = 'str_files/' . $str->sc_berkas;
        
        // Cek apakah file ada di disk hrd_files
        if (!Storage::disk('hrd_files')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        // Download file dengan nama yang sesuai
        $fileName = $str->sc_berkas;
        $fileContent = Storage::disk('hrd_files')->get($filePath);
        $mimeType = Storage::disk('hrd_files')->mimeType($filePath);

        return response($fileContent)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $fileName . '"');
    }
}
