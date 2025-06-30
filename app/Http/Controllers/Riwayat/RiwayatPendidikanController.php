<?php

namespace App\Http\Controllers\Riwayat;

use App\Models\Jurusan;
use Illuminate\Http\Request;
use App\Models\JenjangPendidikan;
use App\Models\RiwayatPendidikan;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\EmployeeProfileService;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class RiwayatPendidikanController extends Controller
{
    protected $employeeProfileService;

    public function __construct(EmployeeProfileService $employeeProfileService)
    {
        $this->employeeProfileService = $employeeProfileService;
    }
    
    public function index($id)
    {
        $data = $this->employeeProfileService->getEmployeeProfile($id);
        
        // Ambil data untuk dropdown
        $jenjangPendidikan = JenjangPendidikan::orderBy('urutan')->get();
        $jurusan = Jurusan::orderBy('jurusan')->get();
        
        $data['jenjangPendidikan'] = $jenjangPendidikan;
        $data['jurusan'] = $jurusan;
        
        return view('karyawan.pendidikan.index', $data);
    }

    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kd_jenjang_didik' => 'required',
            'kd_jurusan' => 'nullable',
            'nama_lembaga' => 'required|string|max:255',
            'tahun_lulus' => 'required|string|max:4',
            'no_ijazah' => 'nullable|string|max:255',
            'tempat' => 'nullable|string',
        ], [
            'kd_jenjang_didik.required' => 'Kolom Jenjang Pendidikan wajib diisi.',
            'nama_lembaga.required' => 'Kolom Nama Lembaga wajib diisi.',
            'nama_lembaga.string' => 'Kolom Nama Lembaga harus berupa teks.',
            'nama_lembaga.max' => 'Kolom Nama Lembaga maksimal 255 karakter.',
            'tahun_lulus.required' => 'Kolom Tahun Lulus wajib diisi.',
            'tahun_lulus.string' => 'Kolom Tahun Lulus harus berupa teks.',
            'tahun_lulus.max' => 'Kolom Tahun Lulus maksimal 4 karakter.',
            'no_ijazah.string' => 'Kolom No. Ijazah harus berupa teks.',
            'no_ijazah.max' => 'Kolom No. Ijazah maksimal 255 karakter.',
            'tempat.string' => 'Kolom Tempat harus berupa teks.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat kesalahan dalam pengunggahan Riwayat Pendidikan.',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        // Get urutan_didik berikutnya
        $urutanDidik = RiwayatPendidikan::where('kd_karyawan', $id)->max('urut_didik') + 1;

        // Insert manual menggunakan query builder
        DB::connection('sqlsrv')
            ->table('hrd_r_pendidikan')
            ->insert([
                'kd_karyawan' => $id,
                'urut_didik' => $urutanDidik,
                'kd_jenjang_didik' => $request->kd_jenjang_didik,
                'kd_jurusan' => $request->kd_jurusan,
                'nama_lembaga' => $request->nama_lembaga,
                'tahun_lulus' => $request->tahun_lulus,
                'no_ijazah' => $request->no_ijazah,
                'tempat' => $request->tempat,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Riwayat Pendidikan berhasil ditambahkan.',
            'code' => 200,
        ]);
    }

    public function getPendidikanData($id)
    {
        // Menggunakan Collection untuk menghindari masalah ORDER BY di SQL Server
        $pendidikan = DB::connection('sqlsrv')
            ->table('hrd_r_pendidikan as p')
            ->leftJoin('hrd_jenjang_pendidikan as jp', 'p.kd_jenjang_didik', '=', 'jp.kd_jenjang_didik')
            ->leftJoin('hrd_jurusan as j', 'p.kd_jurusan', '=', 'j.kd_jurusan')
            ->select([
                'p.*',
                'jp.jenjang_didik',
                'j.jurusan',
                'jp.urutan'
            ])
            ->where('p.kd_karyawan', $id)
            ->get()
            ->sortByDesc('urutan')
            ->sortByDesc('tahun_lulus');

        return DataTables::of($pendidikan)
            ->addIndexColumn()
            ->editColumn('jenjang_jurusan', function ($row) {
                return $row->jenjang_didik . '<br><small class="text-muted">' . $row->jurusan . '</small>';
            })
            ->editColumn('lembaga_tempat', function ($row) {
                return $row->nama_lembaga . ($row->tempat ? '<br><small class="text-muted">' . $row->tempat . '</small>' : '');
            })
            ->addColumn('action', function ($row) {
                return view('karyawan.pendidikan._actions', ['pendidikan' => $row]);
            })
            ->rawColumns(['jenjang_jurusan', 'lembaga_tempat', 'action'])
            ->make(true);
    }

    public function edit($id, $urut)
    {
        $pendidikan = DB::connection('sqlsrv')
            ->table('hrd_r_pendidikan as p')
            ->leftJoin('hrd_jenjang_pendidikan as jp', 'p.kd_jenjang_didik', '=', 'jp.kd_jenjang_didik')
            ->select([
                'p.*',
                'jp.grup_jurusan'
            ])
            ->where('p.kd_karyawan', $id)
            ->where('p.urut_didik', $urut)
            ->first();

        if (!$pendidikan) {
            return response()->json([
                'success' => false,
                'message' => 'Data pendidikan tidak ditemukan.',
                'code' => 404,
            ], 404);
        }

        // Ambil jurusan berdasarkan grup dari jenjang pendidikan
        $jurusan = [];
        if ($pendidikan->grup_jurusan) {
            $jurusan = DB::connection('sqlsrv')
                ->table('hrd_jurusan')
                ->where('grup_jurusan', $pendidikan->grup_jurusan)
                ->orderBy('jurusan')
                ->get();
        }

        $data = [
            'kd_karyawan' => $pendidikan->kd_karyawan,
            'urutan_didik' => $pendidikan->urut_didik,
            'kd_jenjang_didik' => $pendidikan->kd_jenjang_didik,
            'kd_jurusan' => $pendidikan->kd_jurusan,
            'nama_lembaga' => $pendidikan->nama_lembaga,
            'tahun_lulus' => $pendidikan->tahun_lulus,
            'no_ijazah' => $pendidikan->no_ijazah,
            'tempat' => $pendidikan->tempat,
            'grup_jurusan' => $pendidikan->grup_jurusan,
            'jurusan_options' => $jurusan
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
            'code' => 200,
        ]);
    }

    public function update(Request $request, $id, $urut)
    {
        $pendidikan = DB::connection('sqlsrv')
            ->table('hrd_r_pendidikan')
            ->where('kd_karyawan', $id)
            ->where('urut_didik', $urut)
            ->first();

        if (!$pendidikan) {
            return response()->json([
                'success' => false,
                'message' => 'Data pendidikan tidak ditemukan.',
                'code' => 404,
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kd_jenjang_didik' => 'required',
            'kd_jurusan' => 'nullable',
            'nama_lembaga' => 'required|string|max:255',
            'tahun_lulus' => 'required|string|max:4',
            'no_ijazah' => 'nullable|string|max:255',
            'tempat' => 'nullable|string',
        ], [
            'kd_jenjang_didik.required' => 'Kolom Jenjang Pendidikan wajib diisi.',
            'nama_lembaga.required' => 'Kolom Nama Lembaga wajib diisi.',
            'nama_lembaga.string' => 'Kolom Nama Lembaga harus berupa teks.',
            'nama_lembaga.max' => 'Kolom Nama Lembaga maksimal 255 karakter.',
            'tahun_lulus.required' => 'Kolom Tahun Lulus wajib diisi.',
            'tahun_lulus.string' => 'Kolom Tahun Lulus harus berupa teks.',
            'tahun_lulus.max' => 'Kolom Tahun Lulus maksimal 4 karakter.',
            'no_ijazah.string' => 'Kolom No. Ijazah harus berupa teks.',
            'no_ijazah.max' => 'Kolom No. Ijazah maksimal 255 karakter.',
            'tempat.string' => 'Kolom Tempat harus berupa teks.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat kesalahan dalam pengunggahan Riwayat Pendidikan.',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        // Update manual menggunakan query builder
        DB::connection('sqlsrv')
            ->table('hrd_r_pendidikan')
            ->where('kd_karyawan', $id)
            ->where('urut_didik', $urut)
            ->update([
                'kd_jenjang_didik' => $request->kd_jenjang_didik,
                'kd_jurusan' => $request->kd_jurusan,
                'nama_lembaga' => $request->nama_lembaga,
                'tahun_lulus' => $request->tahun_lulus,
                'no_ijazah' => $request->no_ijazah,
                'tempat' => $request->tempat,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Riwayat Pendidikan berhasil diperbarui.',
            'code' => 200,
        ]);
    }

    public function destroy($id, $urut)
    {
        try {
            $pendidikan = DB::connection('sqlsrv')
                ->table('hrd_r_pendidikan')
                ->where('kd_karyawan', $id)
                ->where('urut_didik', $urut)
                ->first();

            if (!$pendidikan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pendidikan tidak ditemukan.',
                    'code' => 404,
                ], 404);
            }

            // Hapus data dari database
            DB::connection('sqlsrv')
                ->table('hrd_r_pendidikan')
                ->where('kd_karyawan', $id)
                ->where('urut_didik', $urut)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Riwayat Pendidikan berhasil dihapus.',
                'code' => 200,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus Riwayat Pendidikan.',
                'code' => 500,
            ], 500);
        }
    }

    public function getJurusan($grupJurusan)
    {
        $jurusan = DB::connection('sqlsrv')
            ->table('hrd_jurusan')
            ->select('kd_jurusan', 'jurusan', 'grup_jurusan')
            ->where('grup_jurusan', $grupJurusan)
            ->orderBy('jurusan', 'asc')
            ->get();

        return response()->json($jurusan);
    }
}