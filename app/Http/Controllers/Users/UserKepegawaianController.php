<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\SuratIzin;

class UserKepegawaianController extends Controller
{
    public function riwayatSk()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return redirect()->route('login')->with('error', 'Data karyawan tidak ditemukan.');
        }

        // Ambil riwayat SK
        $riwayatSk = DB::table('hrd_sk_pegawai_kontrak')
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->where('stt', '>', 0)
            ->orderBy('tgl_sk', 'desc')
            ->get();

        // Data statistik untuk tampilan
        $totalSk = $riwayatSk->count();
        
        $skTahunIni = DB::table('hrd_sk_pegawai_kontrak')
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->where('stt', '>', 0)
            ->whereYear('tgl_sk', date('Y'))
            ->count();
            
        $skTerbaru = $riwayatSk->first();

        // Data untuk filter tahun saja (karena tidak ada jenis_sk di tabel)
        $jenisSkList = collect(); // Empty collection untuk sementara
        
        $tahunList = DB::table('hrd_sk_pegawai_kontrak')
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->where('stt', '>', 0)
            ->selectRaw('YEAR(tgl_sk) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->get();

        return view('users.kepegawaian.riwayat-sk', compact('riwayatSk', 'karyawan', 'jenisSkList', 'tahunList', 'totalSk', 'skTahunIni', 'skTerbaru'));
    }

    public function riwayatMutasi()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return redirect()->route('login')->with('error', 'Data karyawan tidak ditemukan.');
        }

        // Ambil riwayat mutasi
        $riwayatMutasi = DB::table('hrd_r_mutasi as m')
            ->leftJoin('hrd_jenis_mutasi as jm', 'm.kd_jenis_mutasi', '=', 'jm.kd_jenis_mutasi')
            ->leftJoin('hrd_ruangan as r', 'm.kd_ruangan', '=', 'r.kd_ruangan')
            ->where('m.kd_karyawan', $karyawan->kd_karyawan)
            ->orderBy('m.tmt_jabatan', 'desc')
            ->select(
                'm.*',
                'jm.jenis_mutasi',
                'r.ruangan as nama_ruangan'
            )
            ->get();

        // Data statistik untuk summary
        $totalMutasi = $riwayatMutasi->count();
        $ruanganSekarang = $riwayatMutasi->first()->nama_ruangan ?? 'Belum Ada Data';
        $mutasiTerakhir = $riwayatMutasi->first();

        return view('users.kepegawaian.riwayat-mutasi', compact('riwayatMutasi', 'karyawan', 'totalMutasi', 'ruanganSekarang', 'mutasiTerakhir'));
    }

    public function suratIzin()
    {
        try {
            $user = Auth::user();
            $karyawan = $user->karyawan;
            
            if (!$karyawan) {
                return redirect()->route('login')->with('error', 'Data karyawan tidak ditemukan.');
            }

            // Ambil surat izin dengan pagination
            $suratIzin = DB::table('hrd_surat_izin as si')
                ->leftJoin('hrd_kategori_izin as ki', 'si.kd_kategori', '=', 'ki.kd_kategori')
                ->where('si.kd_karyawan', $karyawan->kd_karyawan)
                ->orderBy('si.tgl_mulai', 'desc')
                ->select(
                    'si.*',
                    'ki.kategori as jenis_izin'  // alias untuk kompatibilitas dengan view
                )
                ->paginate(12);

            // Ambil jenis surat untuk form modal
            $jenisSurat = DB::table('hrd_jenis_surat')->get();

            return view('users.kepegawaian.surat-izin', compact('suratIzin', 'karyawan', 'jenisSurat'));
            
        } catch (\Exception $e) {
            Log::error('Error in suratIzin: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat halaman surat izin. Silakan coba lagi.');
        }
    }

    public function downloadSkDocument($tahun, $urut)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            abort(404, 'Data karyawan tidak ditemukan.');
        }

        // Verifikasi bahwa SK ini milik user yang sedang login
        $sk = DB::table('hrd_sk_pegawai_kontrak')
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->where('tahun_sk', $tahun)
            ->where('urut', $urut)
            ->where('stt', '>', 0)
            ->first();

        if (!$sk) {
            abort(404, 'Dokumen SK tidak ditemukan.');
        }

        // Cek file exist
        $filename = "SK_{$karyawan->kd_karyawan}_{$tahun}_{$urut}_signed.pdf";
        $filepath = storage_path("app/hrd_files/sk/{$tahun}/{$filename}");

        if (!file_exists($filepath)) {
            abort(404, 'File dokumen tidak ditemukan.');
        }

        return response()->download($filepath, $filename);
    }

    public function downloadMutasiDocument($kdMutasi)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            abort(404, 'Data karyawan tidak ditemukan.');
        }

        // Verifikasi bahwa mutasi ini milik user yang sedang login
        $mutasi = DB::table('hrd_r_mutasi')
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->where('kd_mutasi', $kdMutasi)
            ->first();

        if (!$mutasi) {
            abort(404, 'Dokumen mutasi tidak ditemukan.');
        }

        // Cek file exist
        $filename = "MUTASI_{$karyawan->kd_karyawan}_{$kdMutasi}_signed.pdf";
        $filepath = storage_path("app/hrd_files/mutasi/{$filename}");

        if (!file_exists($filepath)) {
            abort(404, 'File dokumen tidak ditemukan.');
        }

        return response()->download($filepath, $filename);
    }

    public function printSuratIzin($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            abort(404, 'Data karyawan tidak ditemukan.');
        }

        // Menggunakan Eloquent model seperti admin untuk mendapatkan relasi lengkap
        $surat = SuratIzin::with(['jenisSurat', 'kategoriIzin', 'karyawan'])
            ->where('kd_surat', $id)
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->first();

        if (!$surat) {
            abort(404, 'Surat izin tidak ditemukan.');
        }

        return view('users.kepegawaian.print.surat-izin', compact('surat'));
    }

    public function kenaikanPangkat()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            abort(404, 'Data karyawan tidak ditemukan.');
        }

        // Ambil data kenaikan pangkat
        $kenaikanPangkat = DB::table('hrd_r_kenaikan_pangkat as kp')
            ->join('hrd_m_golongan as gol_lama', 'kp.kd_golongan_lama', '=', 'gol_lama.kd_golongan')
            ->join('hrd_m_golongan as gol_baru', 'kp.kd_golongan_baru', '=', 'gol_baru.kd_golongan')
            ->select([
                'kp.*',
                'gol_lama.nm_golongan as golongan_lama',
                'gol_baru.nm_golongan as golongan_baru'
            ])
            ->where('kp.kd_karyawan', $karyawan->kd_karyawan)
            ->orderBy('kp.tmt_kenaikan_pangkat', 'desc')
            ->get();

        return view('users.kepegawaian.kenaikan-pangkat', compact('kenaikanPangkat', 'karyawan'));
    }

    public function kgb()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            abort(404, 'Data karyawan tidak ditemukan.');
        }

        // Ambil data KGB
        $dataKgb = DB::table('hrd_r_kgb as kgb')
            ->join('hrd_m_golongan as gol', 'kgb.kd_golongan', '=', 'gol.kd_golongan')
            ->select([
                'kgb.*',
                'gol.nm_golongan'
            ])
            ->where('kgb.kd_karyawan', $karyawan->kd_karyawan)
            ->orderBy('kgb.tmt_kgb', 'desc')
            ->get();

        return view('users.kepegawaian.kgb', compact('dataKgb', 'karyawan'));
    }

    // Helper untuk mendapatkan status text
    private function getStatusFinalisasiText($status)
    {
        $statusMap = [
            0 => 'Draft',
            1 => 'Verifikasi 1',
            2 => 'Verifikasi 2', 
            3 => 'Verifikasi 3',
            4 => 'Verifikasi 4',
            5 => 'Selesai'
        ];

        return $statusMap[$status] ?? 'Tidak Diketahui';
    }

    // Helper untuk menghitung durasi izin
    private function hitungDurasiIzin($tglMulai, $tglSelesai)
    {
        $start = new \DateTime($tglMulai);
        $end = new \DateTime($tglSelesai);
        $interval = $start->diff($end);
        
        return $interval->days + 1; // +1 karena termasuk hari pertama
    }

    public function skDetail($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return response()->json(['error' => 'Data karyawan tidak ditemukan.'], 404);
        }

        // Ambil detail SK berdasarkan kd_index
        $sk = DB::table('hrd_sk_pegawai_kontrak')
            ->where('kd_index', $id)
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->where('stt', '>', 0)
            ->first();

        if (!$sk) {
            return response()->json(['error' => 'SK tidak ditemukan.'], 404);
        }

        $html = view('users.kepegawaian.partials.sk-detail', compact('sk'))->render();
        
        return response()->json(['html' => $html]);
    }

    public function downloadSk($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            abort(404, 'Data karyawan tidak ditemukan.');
        }

        // Verifikasi bahwa SK ini milik user yang sedang login
        $sk = DB::table('hrd_sk_pegawai_kontrak')
            ->where('kd_index', $id)
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->where('stt', '>', 0)
            ->first();

        if (!$sk) {
            abort(404, 'SK tidak ditemukan.');
        }

        // Check if document exists
        if (!$sk->path_dokumen || !file_exists(storage_path('app/' . $sk->path_dokumen))) {
            abort(404, 'File dokumen tidak ditemukan.');
        }

        $fileName = 'SK_' . $sk->no_sk . '_' . $karyawan->kd_karyawan . '.pdf';
        
        return response()->download(storage_path('app/' . $sk->path_dokumen), $fileName);
    }

    public function downloadMutasi($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            abort(404, 'Data karyawan tidak ditemukan.');
        }

        // Verifikasi bahwa mutasi ini milik user yang sedang login
        $mutasi = DB::table('hrd_r_mutasi')
            ->where('kd_mutasi', $id)
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->first();

        if (!$mutasi) {
            abort(404, 'Data mutasi tidak ditemukan.');
        }

        // Check if document exists
        if (!$mutasi->path_dokumen || !file_exists(storage_path('app/' . $mutasi->path_dokumen))) {
            abort(404, 'File dokumen tidak ditemukan.');
        }

        $fileName = 'Mutasi_' . $mutasi->kd_mutasi . '_' . $karyawan->kd_karyawan . '.pdf';
        
        return response()->download(storage_path('app/' . $mutasi->path_dokumen), $fileName);
    }

    public function downloadIzin($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            abort(404, 'Data karyawan tidak ditemukan.');
        }

        // Verifikasi bahwa surat izin ini milik user yang sedang login
        $suratIzin = DB::table('hrd_surat_izin as si')
            ->leftJoin('hrd_kategori_izin as ki', 'si.kd_kategori', '=', 'ki.kd_kategori')
            ->where('si.kd_surat', $id)
            ->where('si.kd_karyawan', $karyawan->kd_karyawan)
            ->select('si.*', 'ki.kategori')
            ->first();

        if (!$suratIzin) {
            abort(404, 'Surat izin tidak ditemukan.');
        }

        // Check if document exists
        if (!$suratIzin->file_surat || !file_exists(storage_path('app/' . $suratIzin->file_surat))) {
            abort(404, 'File dokumen tidak ditemukan.');
        }

        $fileName = 'Surat_Izin_' . $suratIzin->kd_surat . '_' . $karyawan->kd_karyawan . '.pdf';
        
        return response()->download(storage_path('app/' . $suratIzin->file_surat), $fileName);
    }

    public function storeSuratIzin(Request $request)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan.'], 404);
        }

        $request->validate([
            'jenis_surat' => 'required|exists:hrd_jenis_surat,kd_jenis_surat',
            'kategori_izin' => 'required|exists:hrd_kategori_izin,kd_kategori',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'alasan' => 'required|string|max:255'
        ], [
            'jenis_surat.required' => 'Jenis surat harus dipilih.',
            'jenis_surat.exists' => 'Jenis surat tidak valid.',
            'kategori_izin.required' => 'Kategori izin harus dipilih.',
            'kategori_izin.exists' => 'Kategori izin tidak valid.',
            'tgl_mulai.required' => 'Tanggal mulai harus diisi.',
            'tgl_mulai.date' => 'Tanggal mulai harus berupa tanggal yang valid.',
            'tgl_selesai.required' => 'Tanggal selesai harus diisi.',
            'tgl_selesai.date' => 'Tanggal selesai harus berupa tanggal yang valid.',
            'tgl_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
            'alasan.required' => 'Alasan harus diisi.',
            'alasan.max' => 'Alasan tidak boleh lebih dari 255 karakter.',
        ]);

        try {
            // Generate kd_surat (auto increment atau ambil max + 1)
            $lastKdSurat = DB::table('hrd_surat_izin')
                ->max('kd_surat');
            $kdSurat = $lastKdSurat ? $lastKdSurat + 1 : 1;

            $data = [
                'kd_surat' => $kdSurat,
                'kd_karyawan' => $karyawan->kd_karyawan,
                'kd_jenis_surat' => $request->jenis_surat,
                'kd_kategori' => $request->kategori_izin,
                'tgl_mulai' => Carbon::parse($request->tgl_mulai)->format('Y-m-d'),
                'tgl_akhir' => Carbon::parse($request->tgl_selesai)->format('Y-m-d'),
                'alasan' => $request->alasan
            ];

            // Handle file upload
            if ($request->hasFile('file_pendukung')) {
                $file = $request->file('file_pendukung');
                $fileName = 'surat_izin_' . $karyawan->kd_karyawan . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('surat_izin', $fileName, 'hrd_files');
                $data['file_surat'] = $filePath;
            }

            DB::table('hrd_surat_izin')->insert($data);

            return response()->json([
                'success' => true,
                'message' => 'Surat izin berhasil disimpan.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function editSuratIzin($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return response()->json(['success' => false, 'error' => 'Data karyawan tidak ditemukan.'], 404);
        }

        $suratIzin = DB::table('hrd_surat_izin')
            ->where('kd_surat', $id)
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->first();

        if (!$suratIzin) {
            return response()->json(['success' => false, 'error' => 'Surat izin tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true, 
            'data' => [
                'kd_surat' => $suratIzin->kd_surat,
                'jenis_surat' => $suratIzin->kd_jenis_surat,
                'kategori_izin' => $suratIzin->kd_kategori,
                'tgl_mulai' => $suratIzin->tgl_mulai,
                'tgl_selesai' => $suratIzin->tgl_akhir ?? $suratIzin->tgl_selesai,
                'alasan' => $suratIzin->alasan
            ]
        ]);
    }

    public function updateSuratIzin(Request $request, $id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return response()->json(['error' => 'Data karyawan tidak ditemukan.'], 404);
        }

        $suratIzin = DB::table('hrd_surat_izin')
            ->where('kd_surat', $id)
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->first();

        if (!$suratIzin) {
            return response()->json(['success' => false, 'message' => 'Surat izin tidak ditemukan.'], 404);
        }

        // Validasi bisa diedit (uncomment jika ada kolom status_finalisasi)
        // if (isset($suratIzin->status_finalisasi) && $suratIzin->status_finalisasi > 0) {
        //     return response()->json(['success' => false, 'message' => 'Surat izin yang sudah diproses tidak dapat diedit.'], 400);
        // }

        $request->validate([
            'jenis_surat' => 'required|exists:hrd_jenis_surat,kd_jenis_surat',
            'kategori_izin' => 'required|exists:hrd_kategori_izin,kd_kategori',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'alasan' => 'required|string|max:255'
        ]);

        try {
            $data = [
                'kd_jenis_surat' => $request->jenis_surat,
                'kd_kategori' => $request->kategori_izin,
                'tgl_mulai' => Carbon::parse($request->tgl_mulai)->format('Y-m-d'),
                'tgl_akhir' => Carbon::parse($request->tgl_selesai)->format('Y-m-d'),
                'alasan' => $request->alasan
            ];

            // Handle file upload
            if ($request->hasFile('file_pendukung')) {
                // Delete old file if exists
                if ($suratIzin->file_surat && file_exists(storage_path('app/' . $suratIzin->file_surat))) {
                    unlink(storage_path('app/' . $suratIzin->file_surat));
                }

                $file = $request->file('file_pendukung');
                $fileName = 'surat_izin_' . $karyawan->kd_karyawan . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('surat_izin', $fileName, 'hrd_files');
                $data['file_surat'] = $filePath;
            }

            DB::table('hrd_surat_izin')->where('kd_surat', $id)->update($data);

            return response()->json(['success' => true, 'message' => 'Surat izin berhasil diperbarui.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroySuratIzin($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return response()->json(['error' => 'Data karyawan tidak ditemukan.'], 404);
        }

        $suratIzin = DB::table('hrd_surat_izin')
            ->where('kd_surat', $id)
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->first();

        if (!$suratIzin) {
            return response()->json(['success' => false, 'message' => 'Surat izin tidak ditemukan.'], 404);
        }

        // Validasi bisa dihapus (uncomment jika ada kolom status_finalisasi)
        // if (isset($suratIzin->status_finalisasi) && $suratIzin->status_finalisasi > 0) {
        //     return response()->json(['success' => false, 'message' => 'Surat izin yang sudah diproses tidak dapat dihapus.'], 400);
        // }

        try {
            // Delete file if exists
            if ($suratIzin->file_surat && file_exists(storage_path('app/' . $suratIzin->file_surat))) {
                unlink(storage_path('app/' . $suratIzin->file_surat));
            }

            DB::table('hrd_surat_izin')->where('kd_surat', $id)->delete();

            return response()->json(['success' => true, 'message' => 'Surat izin berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function getKategoriSuratIzin()
    {
        $kategori = DB::table('hrd_kategori_izin')
            ->orderBy('kategori')
            ->get();

        return response()->json(['data' => $kategori]);
    }

    public function getKategoriIzin(Request $request)
    {
        $kategori = DB::table('hrd_kategori_izin')
            ->where('kd_jenis_surat', $request->kd_jenis_surat)
            ->select('kd_kategori', 'kategori')
            ->get();

        $html = '<option value="">Pilih Kategori Izin</option>';
        foreach ($kategori as $item) {
            $html .= '<option value="' . $item->kd_kategori . '">' . $item->kategori . '</option>';
        }
        return response()->json($html, 200);
    }

}