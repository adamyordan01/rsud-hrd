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
            'tgl_kadaluarsa' => 'nullable|date|after:tgl_str',
            'sc_berkas' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'tgl_sip.required' => 'Tanggal SIP tidak boleh kosong',
            'tgl_sip.date' => 'Format tanggal SIP tidak valid',
            'no_sip.required' => 'Nomor SIP tidak boleh kosong',
            'no_sip.string' => 'Nomor SIP harus berupa string',
            'no_sip.max' => 'Nomor SIP tidak boleh lebih dari 255 karakter',
            'ket.string' => 'Keterangan harus berupa string',
            'tgl_kadaluarsa.date' => 'Format tanggal kadaluarsa tidak valid',
            'tgl_kadaluarsa.after' => 'Tanggal kadaluarsa harus setelah tanggal SIP',
            'sc_berkas.required' => 'File berkas tidak boleh kosong',
            'sc_berkas.file' => 'File berkas tidak valid',
            'sc_berkas.mimes' => 'File berkas harus berupa pdf, jpg, jpeg, atau png',
            'sc_berkas.max' => 'Ukuran file berkas tidak boleh lebih dari 2MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat kesalahan dalan pengunggahan Surat Izin Praktik',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        $user = auth()->user();
        $isKepegawaian = $user->hasRole('kepegawaian');

        $karyawan = Karyawan::where('kd_karyawan', $id)
            ->select('kd_karyawan', 'nama')
            ->first();

        $namaKaryawan = str_replace(' ', '_', $karyawan->nama);
        $noSip = str_replace('/', '-', $request->no_sip);

        // Format nama file
        $fileName = "SIP-{$karyawan->kd_karyawan}-{$namaKaryawan}-{$noSip}";
        $extension = $request->file('sc_berkas')->getClientOriginalExtension();
        $fullFileName = "{$fileName}.{$extension}";

        // Simpan file ke storage
        $filePath = $request->file('sc_berkas')->storeAs('sip_files', $fullFileName, 'public');

        // Tentukan status berdasarkan uploader, 2 = approved, 1 = pending
        $status = $isKepegawaian ? 2 : 1;

        $urutSip = SuratIzinPraktik::where('kd_karyawan', $id)->max('urut_sip') + 1;
        $tglSip = Carbon::parse($request->tgl_sip)->format('Y-m-d');
        $tglKadaluarsa = $request->tgl_kadaluarsa ? 
            Carbon::parse($request->tgl_kadaluarsa)->format('Y-m-d') : null;

        // Simpan data SIP (hanya nama file tanpa path)
        $sip = SuratIzinPraktik::create([
            'kd_karyawan' => $id,
            'urut_sip' => $urutSip,
            'tgl_sip' => $tglSip,
            'no_sip' => $request->no_sip,
            'ket' => $request->ket,
            'tgl_kadaluarsa' => $tglKadaluarsa,
            'sc_berkas' => $fullFileName,
            'status' => $status,
            'uploaded_by' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => $status === 2 ?
                'Berhasil mengunggah SIP' :
                'Berhasil mengunggah SIP, menunggu persetujuan',
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
            ->first();

        $data = [
            'kd_karyawan' => $sip->kd_karyawan,
            'urut_sip' => $sip->urut_sip,
            'no_sip' => $sip->no_sip,
            'tgl_sip' => Carbon::parse($sip->tgl_sip)->format('Y-m-d'),
            'tgl_kadaluarsa' => $sip->tgl_kadaluarsa ? 
                Carbon::parse($sip->tgl_kadaluarsa)->format('Y-m-d') : null,
            'ket' => $sip->ket,
            'sc_berkas' => $sip->sc_berkas,
            'url_file' => $sip->sc_berkas ?
                Storage::url('sip_files/' . $sip->sc_berkas) : null,
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
            'code' => 200,
        ]);
    }

    // Update SIP Menggunaan Query Builder
    public function update(Request $request, $id, $urut)
    {
        $sip = SuratIzinPraktik::where('kd_karyawan', $id)
            ->where('urut_sip', $urut)
            ->first();

        $validator = Validator::make($request->all(), [
            'tgl_sip' => 'required|date',
            'no_sip' => 'required|string|max:255',
            'ket' => 'nullable|string',
            'tgl_kadaluarsa' => 'nullable|date|after:tgl_str',
            'sc_berkas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'tgl_sip.required' => 'Tanggal SIP tidak boleh kosong',
            'tgl_sip.date' => 'Format tanggal SIP tidak valid',
            'no_sip.required' => 'Nomor SIP tidak boleh kosong',
            'no_sip.string' => 'Nomor SIP harus berupa string',
            'no_sip.max' => 'Nomor SIP tidak boleh lebih dari 255 karakter',
            'ket.string' => 'Keterangan harus berupa string',
            'tgl_kadaluarsa.date' => 'Format tanggal kadaluarsa tidak valid',
            'tgl_kadaluarsa.after' => 'Tanggal kadaluarsa harus setelah tanggal SIP',
            'sc_berkas.file' => 'File berkas tidak valid',
            'sc_berkas.mimes' => 'File berkas harus berupa pdf, jpg, jpeg, atau png',
            'sc_berkas.max' => 'Ukuran file berkas tidak boleh lebih dari 2MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat kesalahan dalam pengunggahan Surat Izin Praktik',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        $karyawan = Karyawan::where('kd_karyawan', $id)
            ->select('kd_karyawan', 'nama')
            ->first();

        // Jika ada file baru yang diunggah
        $fileName = $sip->sc_berkas;
        if ($request->hasFile('sc_berkas')) {
            // Hapus file lama jika ada
            if ($sip->sc_berkas && Storage::disk('public')->exists('sip_files/' . $sip->sc_berkas)) {
                Storage::disk('public')->delete('sip_files/' . $sip->sc_berkas);
            }

            // Format nama file baru
            $namaKaryawan = str_replace(' ', '_', $karyawan->nama ?? 'Unknown');
            $noSip = str_replace('/', '-', $request->no_sip);

            $fileName = "SIP-{$id}-{$namaKaryawan}-{$noSip}";
            $extension = $request->file('sc_berkas')->getClientOriginalExtension();
            $fullFileName = "{$fileName}.{$extension}";

            // Simpan file baru
            $request->file('sc_berkas')->storeAs('sip_files', $fullFileName, 'public');
            $fileName = $fullFileName;
        }

        // Update data SIP
        DB::connection('sqlsrv')
            ->table('hrd_r_sip')
            ->where('kd_karyawan', $id)
            ->where('urut_sip', $urut)
            ->update([
                'no_sip' => $request->no_sip,
                'tgl_sip' => Carbon::parse($request->tgl_sip)->format('Y-m-d'),
                'tgl_kadaluarsa' => $request->input('tidak_ada_masa_berlaku') ? 
                    null 
                    : ($request->tgl_kadaluarsa ? Carbon::parse($request->tgl_kadaluarsa)->format('Y-m-d') : null),
                'ket' => $request->ket,
                'sc_berkas' => $fileName,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'SIP berhasil diperbarui.',
            'code' => 200,
        ]);
    }
}
