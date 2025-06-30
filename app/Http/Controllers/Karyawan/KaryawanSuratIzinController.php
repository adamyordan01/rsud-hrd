<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\JenisSurat;
use App\Models\KategoriIzin;
use App\Models\SuratIzin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class KaryawanSuratIzinController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $kd_karyawan = $request->user()->kd_karyawan;

            $query = SuratIzin::with(['jenisSurat', 'kategoriIzin', 'karyawan'])
                ->where('kd_karyawan', $kd_karyawan);

            return DataTables::of($query)
                ->addColumn('nomor', function ($row) {
                    static $no = 0;
                    return ++$no;
                })
                ->addColumn('jenis_surat', function ($row) {
                    return $row->jenisSurat->jenis_surat ?? '-';
                })
                ->addColumn('tanggal', function ($row) {
                    $tglMulai = Carbon::parse($row->tgl_mulai)->format('d-m-Y');
                    $tglAkhir = Carbon::parse($row->tgl_akhir)->format('d-m-Y');

                    if ($tglMulai === $tglAkhir) {
                        return '<div class="text-center">' . $tglMulai . '</div>';
                    } else {
                        return '<div class="text-center">' . $tglMulai . ' s.d '. $tglAkhir . '</div>';
                    }
                })
                ->addColumn('kategori', function ($row) {
                    return $row->kategoriIzin->nama_kategori ?? '-';
                })
                ->addColumn('alasan', function ($row) {
                    $alasan = strlen($row->alasan) > 50 ? substr($row->alasan, 0, 50) . '...' : $row->alasan;
                    return '<span class="txt-gray-600 fs-7">' . $alasan . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return view('karyawan.surat_izin.columns._actions', compact('row'));
                })
                ->order(function ($query) {
                    $query->orderBy('tgl_mulai', 'desc');
                })
                ->skipTotalRecords() // Skip counting total records to avoid ORDER BY issues
                ->rawColumns(['action', 'tanggal', 'alasan'])
                ->make(true);
        }

        $jenisSurat = JenisSurat::all();

        return view('karyawan.surat_izin.index', compact('jenisSurat'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_surat' => 'required|exists:hrd_jenis_surat,kd_jenis_surat',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'kategori_izin' => 'required|exists:hrd_kategori_izin,kd_kategori',
            'alasan' => 'required|string|max:255',
        ], [
            'jenis_surat.required' => 'Jenis surat harus dipilih.',
            'jenis_surat.exists' => 'Jenis surat tidak valid.',
            'tgl_mulai.required' => 'Tanggal mulai harus diisi.',
            'tgl_mulai.date' => 'Tanggal mulai harus berupa tanggal yang valid.',
            'tgl_selesai.required' => 'Tanggal selesai harus diisi.',
            'tgl_selesai.date' => 'Tanggal selesai harus berupa tanggal yang valid.',
            'tgl_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
            'kategori_izin.required' => 'Kategori izin harus dipilih.',
            'kategori_izin.exists' => 'Kategori izin tidak valid.',
            'alasan.required' => 'Alasan harus diisi.',
            'alasan.string' => 'Alasan harus berupa teks.',
            'alasan.max' => 'Alasan tidak boleh lebih dari 255 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // ambil kode_surat terakhir
            $lastSurat = SuratIzin::max('kd_surat');
            $nextKodeSurat = $lastSurat ? $lastSurat + 1 : 1;

            SuratIzin::create([
                'kd_surat' => $nextKodeSurat,
                'kd_karyawan' => $request->user()->kd_karyawan,
                'kd_jenis_surat' => $request->jenis_surat,
                'tgl_mulai' => Carbon::parse($request->tgl_mulai)->format('Y-m-d'),
                'tgl_akhir' => Carbon::parse($request->tgl_selesai)->format('Y-m-d'),
                'kd_kategori' => $request->kategori_izin,
                'alasan' => $request->alasan,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Surat izin berhasil dibuat.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Terjadi kesalahan: " . $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $suratIzin = SuratIzin::with(['jenisSurat', 'kategoriIzin', 'karyawan'])
            ->where('kd_surat', $id)
            ->where('kd_karyawan', request()->user()->kd_karyawan)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'kd_surat' => $suratIzin->kd_surat,
                'jenis_surat' => $suratIzin->kd_jenis_surat,
                'tgl_mulai' => Carbon::parse($suratIzin->tgl_mulai)->format('Y-m-d'),
                'tgl_selesai' => Carbon::parse($suratIzin->tgl_akhir)->format('Y-m-d'),
                'kategori_izin' => $suratIzin->kd_kategori,
                'alasan' => $suratIzin->alasan,
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $surat = SuratIzin::where('kd_surat', $id)
            ->where('kd_karyawan', $request->user()->kd_karyawan)
            ->firstOrFail();
            // dd($surat);
            // dd($request->all());

        $validator = Validator::make($request->all(), [
            'jenis_surat' => 'required|exists:hrd_jenis_surat,kd_jenis_surat',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'kategori_izin' => 'required|exists:hrd_kategori_izin,kd_kategori',
            'alasan' => 'required|string|max:255',
        ], [
            'jenis_surat.required' => 'Jenis surat harus dipilih.',
            'jenis_surat.exists' => 'Jenis surat tidak valid.',
            'tgl_mulai.required' => 'Tanggal mulai harus diisi.',
            'tgl_mulai.date' => 'Tanggal mulai harus berupa tanggal yang valid.',
            'tgl_selesai.required' => 'Tanggal selesai harus diisi.',
            'tgl_selesai.date' => 'Tanggal selesai harus berupa tanggal yang valid.',
            'tgl_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
            'kategori_izin.required' => 'Kategori izin harus dipilih.',
            'kategori_izin.exists' => 'Kategori izin tidak valid.',
            'alasan.required' => 'Alasan harus diisi.',
            'alasan.string' => 'Alasan harus berupa teks.',
            'alasan.max' => 'Alasan tidak boleh lebih dari 255 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $surat->update([
                'kd_jenis_surat' => $request->jenis_surat,
                'tgl_mulai' => Carbon::parse($request->tgl_mulai)->format('Y-m-d'),
                'tgl_akhir' => Carbon::parse($request->tgl_selesai)->format('Y-m-d'),
                'kd_kategori' => $request->kategori_izin,
                'alasan' => $request->alasan,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Surat izin berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Terjadi kesalahan: " . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $surat = SuratIzin::where('kd_surat', $id)
            ->where('kd_karyawan', request()->user()->kd_karyawan)
            ->firstOrFail();

        try {
            $surat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Surat izin berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Terjadi kesalahan: " . $e->getMessage(),
            ], 500);
        }
    }

    public function getKategori(Request $request)
    {
        $kategori = KategoriIzin::where('kd_jenis_surat', $request->kd_jenis_surat)
            ->get();
            // dd($kategori);

        $html = '<option value="">Pilih Kategori Izin</option>';
        foreach ($kategori as $item) {
            $html .= '<option value="' . $item->kd_kategori . '">' . $item->kategori . '</option>';
        }
        return response()->json($html, 200);
    }

    public function print($id)
    {
        $surat = SuratIzin::with(['jenisSurat', 'kategoriIzin', 'karyawan'])
            ->where('kd_surat', $id)
            ->where('kd_karyawan', request()->user()->kd_karyawan)
            ->firstOrFail();

        return view('karyawan.surat_izin.print', compact('surat'));
    }
}
