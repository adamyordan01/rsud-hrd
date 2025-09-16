<?php

namespace App\Http\Controllers\Riwayat;

use Carbon\Carbon;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\EmployeeProfileService;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class SeminarController extends Controller
{
    protected $employeeProfileService;

    public function __construct(EmployeeProfileService $employeeProfileService)
    {
        $this->employeeProfileService = $employeeProfileService;
    }
    
    public function index($id)
    {
        $data = $this->employeeProfileService->getEmployeeProfile($id);
        
        // Get sumber dana options
        $sumberDana = DB::connection('sqlsrv')
            ->table('hrd_sumber_dana')
            ->select('kd_sumber_dana', 'sumber_dana')
            ->get();
        
        $data['sumber_dana'] = $sumberDana;
        
        return view('karyawan.seminar.index', $data);
    }

    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kd_sumber_dana' => 'required|integer',
            'nama_seminar' => 'required|string|max:255',
            'no_sertifikat' => 'nullable|string|max:255',
            'tgl_mulai' => 'required|date',
            'tgl_akhir' => 'required|date|after_or_equal:tgl_mulai',
            'jml_jam' => 'nullable|numeric|min:0',
            'penyelenggara' => 'required|string|max:255',
            'ket' => 'nullable|string',
            'tahun' => 'required|string|size:4',
        ], [
            'kd_sumber_dana.required' => 'Kolom Sumber Dana wajib diisi.',
            'kd_sumber_dana.integer' => 'Kolom Sumber Dana harus berupa angka.',
            'nama_seminar.required' => 'Kolom Nama Seminar wajib diisi.',
            'nama_seminar.string' => 'Kolom Nama Seminar harus berupa teks.',
            'nama_seminar.max' => 'Kolom Nama Seminar maksimal 255 karakter.',
            'no_sertifikat.string' => 'Kolom No. Sertifikat harus berupa teks.',
            'no_sertifikat.max' => 'Kolom No. Sertifikat maksimal 255 karakter.',
            'tgl_mulai.required' => 'Kolom Tanggal Mulai wajib diisi.',
            'tgl_mulai.date' => 'Kolom Tanggal Mulai harus berupa tanggal.',
            'tgl_akhir.required' => 'Kolom Tanggal Akhir wajib diisi.',
            'tgl_akhir.date' => 'Kolom Tanggal Akhir harus berupa tanggal.',
            'tgl_akhir.after_or_equal' => 'Tanggal Akhir harus sama atau setelah Tanggal Mulai.',
            'jml_jam.numeric' => 'Kolom Jumlah Jam harus berupa angka.',
            'jml_jam.min' => 'Kolom Jumlah Jam minimal 0.',
            'penyelenggara.required' => 'Kolom Penyelenggara wajib diisi.',
            'penyelenggara.string' => 'Kolom Penyelenggara harus berupa teks.',
            'penyelenggara.max' => 'Kolom Penyelenggara maksimal 255 karakter.',
            'ket.string' => 'Kolom Keterangan harus berupa teks.',
            'tahun.required' => 'Kolom Tahun wajib diisi.',
            'tahun.string' => 'Kolom Tahun harus berupa teks.',
            'tahun.size' => 'Kolom Tahun harus 4 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        // Get urut_seminar berikutnya
        $urutSeminar = DB::connection('sqlsrv')
            ->table('hrd_r_seminar')
            ->where('kd_karyawan', $id)
            ->max('urut_seminar') + 1;

        // Insert manual menggunakan query builder
        DB::connection('sqlsrv')
            ->table('hrd_r_seminar')
            ->insert([
                'urut_seminar' => $urutSeminar,
                'kd_karyawan' => $id,
                'kd_sumber_dana' => $request->kd_sumber_dana,
                'nama_seminar' => $request->nama_seminar,
                'no_sertifikat' => $request->no_sertifikat,
                'tgl_mulai' => $request->tgl_mulai,
                'tgl_akhir' => $request->tgl_akhir,
                'jml_jam' => $request->jml_jam ?: 0,
                'penyelenggara' => $request->penyelenggara,
                'ket' => $request->ket,
                'tahun' => $request->tahun,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Seminar berhasil ditambahkan.',
            'code' => 200,
        ]);
    }

    public function getSeminarData($id)
    {
        // Menggunakan Collection untuk menghindari masalah ORDER BY di SQL Server
        $seminar = DB::connection('sqlsrv')
            ->table('hrd_r_seminar as s')
            ->leftJoin('hrd_sumber_dana as sd', 's.kd_sumber_dana', '=', 'sd.kd_sumber_dana')
            ->select('s.*', 'sd.sumber_dana')
            ->where('s.kd_karyawan', $id)
            ->get()
            ->sortByDesc('tgl_mulai')
            ->sortByDesc('urut_seminar');

        return DataTables::of($seminar)
            ->addIndexColumn()
            ->editColumn('periode', function ($row) {
                $tglMulai = Carbon::parse($row->tgl_mulai)->translatedFormat('d M Y');
                $tglAkhir = Carbon::parse($row->tgl_akhir)->translatedFormat('d M Y');
                
                if ($row->tgl_mulai == $row->tgl_akhir) {
                    return $tglMulai;
                } else {
                    return $tglMulai . ' - ' . $tglAkhir;
                }
            })
            ->editColumn('nama_penyelenggara', function ($row) {
                return $row->nama_seminar . '<br><small class="text-muted">' . $row->penyelenggara . '</small>';
            })
            ->editColumn('sertifikat_dana', function ($row) {
                $noSertifikat = $row->no_sertifikat ? $row->no_sertifikat : '<em class="text-muted">No. Sertifikat tidak ada</em>';
                $sumberDana = $row->sumber_dana ? $row->sumber_dana : 'Tidak diketahui';
                return $noSertifikat . '<br><small class="text-muted">' . $sumberDana . '</small>';
            })
            ->editColumn('jam_tahun', function ($row) {
                $jam = $row->jml_jam ? $row->jml_jam . ' jam' : '0 jam';
                return $jam . '<br><small class="text-muted">Tahun ' . $row->tahun . '</small>';
            })
            ->editColumn('ket', function ($row) {
                if ($row->ket) {
                    $ketShort = strlen($row->ket) > 50 ? substr($row->ket, 0, 50) . '...' : $row->ket;
                    return '<span title="' . e($row->ket) . '">' . e($ketShort) . '</span>';
                }
                return '-';
            })
            ->addColumn('action', function ($row) {
                return view('karyawan.seminar._actions', ['seminar' => $row]);
            })
            ->rawColumns(['nama_penyelenggara', 'sertifikat_dana', 'jam_tahun', 'ket', 'action'])
            ->make(true);
    }

    public function edit($id, $urut)
    {
        $seminar = DB::connection('sqlsrv')
            ->table('hrd_r_seminar')
            ->where('kd_karyawan', $id)
            ->where('urut_seminar', $urut)
            ->first();

        if (!$seminar) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
                'code' => 404,
            ], 404);
        }

        $data = [
            'kd_karyawan' => $seminar->kd_karyawan,
            'urut_seminar' => $seminar->urut_seminar,
            'kd_sumber_dana' => $seminar->kd_sumber_dana,
            'nama_seminar' => $seminar->nama_seminar,
            'no_sertifikat' => $seminar->no_sertifikat,
            'tgl_mulai' => Carbon::parse($seminar->tgl_mulai)->format('Y-m-d'),
            'tgl_akhir' => Carbon::parse($seminar->tgl_akhir)->format('Y-m-d'),
            'jml_jam' => $seminar->jml_jam,
            'penyelenggara' => $seminar->penyelenggara,
            'ket' => $seminar->ket,
            'tahun' => $seminar->tahun,
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
            'code' => 200,
        ]);
    }

    public function update(Request $request, $id, $urut)
    {
        $seminar = DB::connection('sqlsrv')
            ->table('hrd_r_seminar')
            ->where('kd_karyawan', $id)
            ->where('urut_seminar', $urut)
            ->first();

        if (!$seminar) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
                'code' => 404,
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kd_sumber_dana' => 'required|integer',
            'nama_seminar' => 'required|string|max:255',
            'no_sertifikat' => 'nullable|string|max:255',
            'tgl_mulai' => 'required|date',
            'tgl_akhir' => 'required|date|after_or_equal:tgl_mulai',
            'jml_jam' => 'nullable|numeric|min:0',
            'penyelenggara' => 'required|string|max:255',
            'ket' => 'nullable|string',
            'tahun' => 'required|string|size:4',
        ], [
            'kd_sumber_dana.required' => 'Kolom Sumber Dana wajib diisi.',
            'kd_sumber_dana.integer' => 'Kolom Sumber Dana harus berupa angka.',
            'nama_seminar.required' => 'Kolom Nama Seminar wajib diisi.',
            'nama_seminar.string' => 'Kolom Nama Seminar harus berupa teks.',
            'nama_seminar.max' => 'Kolom Nama Seminar maksimal 255 karakter.',
            'no_sertifikat.string' => 'Kolom No. Sertifikat harus berupa teks.',
            'no_sertifikat.max' => 'Kolom No. Sertifikat maksimal 255 karakter.',
            'tgl_mulai.required' => 'Kolom Tanggal Mulai wajib diisi.',
            'tgl_mulai.date' => 'Kolom Tanggal Mulai harus berupa tanggal.',
            'tgl_akhir.required' => 'Kolom Tanggal Akhir wajib diisi.',
            'tgl_akhir.date' => 'Kolom Tanggal Akhir harus berupa tanggal.',
            'tgl_akhir.after_or_equal' => 'Tanggal Akhir harus sama atau setelah Tanggal Mulai.',
            'jml_jam.numeric' => 'Kolom Jumlah Jam harus berupa angka.',
            'jml_jam.min' => 'Kolom Jumlah Jam minimal 0.',
            'penyelenggara.required' => 'Kolom Penyelenggara wajib diisi.',
            'penyelenggara.string' => 'Kolom Penyelenggara harus berupa teks.',
            'penyelenggara.max' => 'Kolom Penyelenggara maksimal 255 karakter.',
            'ket.string' => 'Kolom Keterangan harus berupa teks.',
            'tahun.required' => 'Kolom Tahun wajib diisi.',
            'tahun.string' => 'Kolom Tahun harus berupa teks.',
            'tahun.size' => 'Kolom Tahun harus 4 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        // Update manual menggunakan query builder
        DB::connection('sqlsrv')
            ->table('hrd_r_seminar')
            ->where('kd_karyawan', $id)
            ->where('urut_seminar', $urut)
            ->update([
                'kd_sumber_dana' => $request->kd_sumber_dana,
                'nama_seminar' => $request->nama_seminar,
                'no_sertifikat' => $request->no_sertifikat,
                'tgl_mulai' => $request->tgl_mulai,
                'tgl_akhir' => $request->tgl_akhir,
                'jml_jam' => $request->jml_jam ?: 0,
                'penyelenggara' => $request->penyelenggara,
                'ket' => $request->ket,
                'tahun' => $request->tahun,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Seminar berhasil diperbarui.',
            'code' => 200,
        ]);
    }

    public function destroy($id, $urut)
    {
        try {
            $seminar = DB::connection('sqlsrv')
                ->table('hrd_r_seminar')
                ->where('kd_karyawan', $id)
                ->where('urut_seminar', $urut)
                ->first();

            if (!$seminar) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan.',
                    'code' => 404,
                ], 404);
            }

            // Hapus data dari database
            DB::connection('sqlsrv')
                ->table('hrd_r_seminar')
                ->where('kd_karyawan', $id)
                ->where('urut_seminar', $urut)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Seminar berhasil dihapus.',
                'code' => 200,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data.',
                'code' => 500,
            ], 500);
        }
    }
}
