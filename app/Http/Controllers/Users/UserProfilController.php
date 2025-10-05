<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Helpers\PhotoHelper;
use App\Services\EmployeeProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class UserProfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return redirect()->route('login')->with('error', 'Data karyawan tidak ditemukan.');
        }

        // Data detail karyawan
        $detailKaryawan = $karyawan->detailKaryawan;
        
        // Data pendidikan
        $pendidikan = DB::table('hrd_r_pendidikan')
            ->leftJoin('hrd_jenjang_pendidikan', 'hrd_r_pendidikan.kd_jenjang_didik', '=', 'hrd_jenjang_pendidikan.kd_jenjang_didik')
            ->leftJoin('hrd_jurusan', 'hrd_r_pendidikan.kd_jurusan', '=', 'hrd_jurusan.kd_jurusan')
            ->where('hrd_r_pendidikan.kd_karyawan', $karyawan->kd_karyawan)
            ->orderBy('hrd_r_pendidikan.tahun_lulus', 'desc')
            ->select(
                'hrd_r_pendidikan.*',
                'hrd_jenjang_pendidikan.jenjang_didik',
                'hrd_jurusan.jurusan'
            )
            ->get();

        // Data keluarga
        $keluarga = DB::table('hrd_r_keluarga')
            ->leftJoin('hrd_hub_keluarga', 'hrd_r_keluarga.kd_hub_klrg', '=', 'hrd_hub_keluarga.kd_hub_klrg')
            ->where('hrd_r_keluarga.kd_karyawan', $karyawan->kd_karyawan)
            ->orderBy('hrd_r_keluarga.urut_klrg')
            ->select(
                'hrd_r_keluarga.*',
                'hrd_hub_keluarga.hub_klrg'
            )
            ->get();

        // Data kemampuan bahasa - commented sementara sampai struktur tabel dikonfirmasi
        $kemampuanBahasa = collect(); // empty collection
        /*
        $kemampuanBahasa = DB::table('hrd_kemampuan_bahasa')
            ->leftJoin('hrd_bahasa', 'hrd_kemampuan_bahasa.kd_bahasa', '=', 'hrd_bahasa.kd_bahasa')
            ->leftJoin('hrd_tingkat_bahasa', 'hrd_kemampuan_bahasa.kd_tingkat', '=', 'hrd_tingkat_bahasa.kd_tingkat')
            ->where('hrd_kemampuan_bahasa.kd_karyawan', $karyawan->kd_karyawan)
            ->orderBy('hrd_kemampuan_bahasa.urut')
            ->select(
                'hrd_kemampuan_bahasa.*',
                'hrd_bahasa.nama_bahasa',
                'hrd_tingkat_bahasa.tingkat_bahasa'
            )
            ->get();
        */

        // Data BPJS Ketenagakerjaan - commented sementara sampai struktur tabel dikonfirmasi
        $bpjsKetenagakerjaan = collect(); // empty collection
        /*
        $bpjsKetenagakerjaan = DB::table('hrd_bpjs_ketenagakerjaan')
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->orderBy('urut')
            ->get();
        */

        // Data foto karyawan menggunakan PhotoHelper
        $photo = PhotoHelper::getPhotoUrl($karyawan, 'foto_square');

        // Data kepegawaian - informasi dasar dari tabel hrd_karyawan
        $dataKepegawaian = [
            'nip_lama' => $karyawan->nip_lama,
            'nip_baru' => $karyawan->nip_baru,
            'no_absen' => $karyawan->no_absen,
            'kd_gol_sekarang' => $karyawan->kd_gol_sekarang,
            'tmt_gol_sekarang' => $karyawan->tmt_gol_sekarang,
            'masa_kerja_thn' => $karyawan->masa_kerja_thn,
            'masa_kerja_bulan' => $karyawan->masa_kerja_bulan,
            'status_peg' => $karyawan->status_peg,
            'kd_jenis_peg' => $karyawan->kd_jenis_peg,
            'kgb' => $karyawan->kgb,
            'rencana_kp' => $karyawan->rencana_kp,
            // Data yang diminta oleh view
            'ruangan' => null, // akan diisi jika ada relasi ke tabel ruangan
            'golongan' => $karyawan->kd_gol_sekarang,
            'status_kerja' => $karyawan->status_peg,
            'tmt' => $karyawan->tmt_gol_sekarang
        ];

        // Gunakan EmployeeProfileService untuk mendapatkan data kelengkapan yang konsisten
        $employeeProfileService = new EmployeeProfileService();
        $profileData = $employeeProfileService->getEmployeeProfile($karyawan->kd_karyawan);
        
        // Data completion tracking menggunakan service
        $completionData = [
            'percentage' => $profileData['persentase_kelengkapan'],
            'missing_fields' => $profileData['missing_fields']
        ];

        // Data alamat untuk ditampilkan di view
        $alamatData = [];
        
        // Get nama jenis kelamin
        if ($karyawan->kd_jenis_kelamin !== null && $karyawan->kd_jenis_kelamin !== '') {
            $jenisKelamin = DB::table('sex')
                ->where('kode', $karyawan->kd_jenis_kelamin)
                ->first();
            $alamatData['jenis_kelamin'] = $jenisKelamin ? $jenisKelamin->jenis : null;
        }
        
        // Get nama agama
        if ($karyawan->kd_agama !== null && $karyawan->kd_agama !== '') {
            $agama = DB::table('agama')
                ->where('kd_agama', $karyawan->kd_agama)
                ->first();
            $alamatData['agama'] = $agama ? $agama->agama : null;
        }
        
        // Get nama status pernikahan
        if ($karyawan->kd_status_nikah !== null && $karyawan->kd_status_nikah !== '') {
            $statusNikah = DB::table('hrd_status_nikah')
                ->where('kd_status_nikah', $karyawan->kd_status_nikah)
                ->first();
            $alamatData['status_nikah'] = $statusNikah ? $statusNikah->status_nikah : null;
        }
        
        // Get nama provinsi
        if ($karyawan->kd_propinsi !== null && $karyawan->kd_propinsi !== '') {
            $provinsi = DB::table('propinsi')
                ->where('kd_propinsi', $karyawan->kd_propinsi)
                ->first();
            $alamatData['provinsi'] = $provinsi ? $provinsi->propinsi : null;
        }
        
        // Get nama kabupaten
        if ($karyawan->kd_kabupaten !== null && $karyawan->kd_kabupaten !== '') {
            $kabupaten = DB::table('kabupaten')
                ->where('kd_kabupaten', $karyawan->kd_kabupaten)
                ->first();
            $alamatData['kabupaten'] = $kabupaten ? $kabupaten->kabupaten : null;
        }
        
        // Get nama kecamatan
        if ($karyawan->kd_kecamatan !== null && $karyawan->kd_kecamatan !== '') {
            $kecamatan = DB::table('kecamatan')
                ->where('kd_kecamatan', $karyawan->kd_kecamatan)
                ->first();
            $alamatData['kecamatan'] = $kecamatan ? $kecamatan->kecamatan : null;
        }
        
        // Get nama kelurahan
        if ($karyawan->kd_kelurahan !== null && $karyawan->kd_kelurahan !== '') {
            $kelurahan = DB::table('kelurahan')
                ->where('kd_kelurahan', $karyawan->kd_kelurahan)
                ->first();
            $alamatData['kelurahan'] = $kelurahan ? $kelurahan->kelurahan : null;
        }

        return view('users.profil.index', compact(
            'karyawan',
            'detailKaryawan',
            'pendidikan',
            'keluarga',
            'kemampuanBahasa',
            'bpjsKetenagakerjaan',
            'photo',
            'dataKepegawaian',
            'completionData',
            'alamatData'
        ));
    }

    public function edit()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return redirect()->route('login')->with('error', 'Data karyawan tidak ditemukan.');
        }

        // Data master untuk dropdown
        $provinsiList = DB::table('propinsi')->orderBy('propinsi')->get();
        $agamaList = DB::table('agama')->orderBy('agama')->get();
        $statusNikahList = DB::table('hrd_status_nikah')->orderBy('status_nikah')->get();
        $jenisKelaminList = DB::table('sex')->orderBy('jenis')->get();
        
        // Load data wilayah yang sudah terisi jika ada
        $kabupatenList = collect();
        $kecamatanList = collect();
        $kelurahanList = collect();
        
        if ($karyawan->kd_propinsi !== null && $karyawan->kd_propinsi !== '') {
            $kabupatenList = DB::table('kabupaten')
                ->where('kd_propinsi', $karyawan->kd_propinsi)
                ->orderBy('kabupaten')
                ->get();
                
            if ($karyawan->kd_kabupaten !== null && $karyawan->kd_kabupaten !== '') {
                $kecamatanList = DB::table('kecamatan')
                    ->where('kd_kabupaten', $karyawan->kd_kabupaten)
                    ->orderBy('kecamatan')
                    ->get();
                    
                if ($karyawan->kd_kecamatan !== null && $karyawan->kd_kecamatan !== '') {
                    $kelurahanList = DB::table('kelurahan')
                        ->where('kd_kecamatan', $karyawan->kd_kecamatan)
                        ->orderBy('kelurahan')
                        ->get();
                }
            }
        }
        
        return view('users.profil.edit', compact(
            'karyawan', 
            'provinsiList', 
            'agamaList', 
            'statusNikahList', 
            'jenisKelaminList',
            'kabupatenList',
            'kecamatanList', 
            'kelurahanList'
        ));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan.'
            ], 404);
        }

        // Validasi data yang bisa diubah oleh user (TIDAK termasuk gelar_depan, nama, gelar_belakang)
        $validator = Validator::make($request->all(), [
            'no_ktp' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'no_hp' => 'nullable|string|max:50',
            'alamat' => 'nullable|string|max:255',
            'kd_jenis_kelamin' => 'nullable|integer',
            'kd_agama' => 'nullable|integer',
            'kd_status_nikah' => 'nullable|integer',
            'kd_propinsi' => 'nullable|integer',
            'kd_kabupaten' => 'nullable|integer',
            'kd_kecamatan' => 'nullable|integer',
            'kd_kelurahan' => 'nullable|integer',
            'tinggi_badan' => 'nullable|integer|min:100|max:250',
            'berat_badan' => 'nullable|integer|min:30|max:300',
            'rek_bni_syariah' => 'nullable|string|max:100',
            'rek_bpd_aceh' => 'nullable|string|max:50',
        ], [
            'no_ktp.max' => 'NIK maksimal 100 karakter.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 100 karakter.',
            'no_hp.max' => 'Nomor HP maksimal 50 karakter.',
            'alamat.max' => 'Alamat maksimal 255 karakter.',
            'tinggi_badan.min' => 'Tinggi badan minimal 100 cm.',
            'tinggi_badan.max' => 'Tinggi badan maksimal 250 cm.',
            'berat_badan.min' => 'Berat badan minimal 30 kg.',
            'berat_badan.max' => 'Berat badan maksimal 300 kg.',
            'rek_bni_syariah.max' => 'Nomor rekening BNI Syariah maksimal 100 karakter.',
            'rek_bpd_aceh.max' => 'Nomor rekening BPD Aceh maksimal 50 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Update data karyawan - HANYA field yang diizinkan untuk user
            // Field yang TIDAK BOLEH diubah oleh user: gelar_depan, nama, gelar_belakang
            // Field tersebut hanya boleh diubah oleh bagian kepegawaian
            $updateData = $request->only([
                'no_ktp',
                'email',
                'no_hp', 
                'alamat',
                'kd_jenis_kelamin',
                'kd_agama',
                'kd_status_nikah',
                'kd_propinsi',
                'kd_kabupaten',
                'kd_kecamatan',
                'kd_kelurahan',
                'tinggi_badan',
                'berat_badan',
                'rek_bni_syariah',
                'rek_bpd_aceh'
            ]);

            // Filter data yang tidak kosong
            $updateData = array_filter($updateData, function($value) {
                return $value !== null && $value !== '';
            });

            // Update data karyawan (tanpa field yang diproteksi)
            if (!empty($updateData)) {
                DB::table('hrd_karyawan')
                    ->where('kd_karyawan', $karyawan->kd_karyawan)
                    ->update($updateData);
            }

            // Update email di tabel users jika ada
            if (isset($updateData['email'])) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['email' => $updateData['email']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui profil: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadPhoto(Request $request)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan.'
            ], 404);
        }

        // Validasi untuk foto yang bisa diupload
        $rules = [];
        $messages = [];
        
        if ($request->hasFile('foto_square')) {
            $rules['foto_square'] = 'image|mimes:jpeg,jpg,png|max:2048';
            $messages['foto_square.image'] = 'Foto profil harus berupa gambar.';
            $messages['foto_square.mimes'] = 'Format foto profil harus JPEG, JPG, atau PNG.';
            $messages['foto_square.max'] = 'Ukuran foto profil maksimal 2MB.';
        }
        
        if ($request->hasFile('foto')) {
            $rules['foto'] = 'image|mimes:jpeg,jpg,png|max:2048';
            $messages['foto.image'] = 'Foto CV harus berupa gambar.';
            $messages['foto.mimes'] = 'Format foto CV harus JPEG, JPG, atau PNG.';
            $messages['foto.max'] = 'Ukuran foto CV maksimal 2MB.';
        }
        
        if (empty($rules)) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih minimal satu foto untuk diupload.'
            ], 422);
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $uploadedPhotos = [];
            $photoTypes = [
                'foto_square' => ['width' => 400, 'height' => 400, 'format' => 'jpg'],
                'foto' => ['width' => 300, 'height' => 400, 'format' => 'jpg']
            ];
            
            foreach ($photoTypes as $type => $config) {
                if ($request->hasFile($type)) {
                    $file = $request->file($type);
                    
                    // Generate nama file unik sesuai dengan admin
                    $fileName = $karyawan->kd_karyawan . '_' . $type . '_' . time() . '.' . $config['format'];
                    
                    // Path untuk menyimpan di disk hrd_files dengan folder photos
                    $photoPath = 'photos/' . $fileName;
                    
                    // Proses dan resize gambar menggunakan Intervention Image
                    $image = Image::make($file);
                    
                    // Resize sesuai konfigurasi
                    $image->fit($config['width'], $config['height'], function ($constraint) {
                        $constraint->upsize();
                    });
                    
                    // Encode to JPG dengan quality 100
                    $image->encode('jpg', 100);
                    
                    // Hapus foto lama jika ada
                    if ($karyawan->{$type}) {
                        Storage::disk('hrd_files')->delete($karyawan->{$type});
                    }
                    
                    // Simpan ke disk hrd_files
                    Storage::disk('hrd_files')->put($photoPath, $image->stream());
                    
                    // Update database dengan path lengkap
                    DB::table('hrd_karyawan')->where('kd_karyawan', $karyawan->kd_karyawan)->update([
                        $type => $photoPath
                    ]);
                    
                    $uploadedPhotos[$type] = $photoPath;
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil diupload. ' . count($uploadedPhotos) . ' foto telah diperbarui.',
                'uploaded_photos' => $uploadedPhotos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengunggah foto: ' . $e->getMessage()
            ], 500);
        }
    }

    // API untuk dropdown wilayah
    public function getKabupaten(Request $request)
    {
        $provinsiId = $request->get('provinsi_id');
        
        if ($provinsiId === null || $provinsiId === '') {
            return response()->json([
                'success' => false,
                'message' => 'Provinsi ID required',
                'data' => []
            ]);
        }
        
        $kabupaten = DB::table('kabupaten')
            ->where('kd_propinsi', $provinsiId)
            ->orderBy('kabupaten')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $kabupaten,
        ]);
    }

    public function getKecamatan(Request $request)
    {
        $kabupatenId = $request->get('kabupaten_id');
        
        if ($kabupatenId === null || $kabupatenId === '') {
            return response()->json([
                'success' => false,
                'message' => 'Kabupaten ID required',
                'data' => []
            ]);
        }
        
        $kecamatan = DB::table('kecamatan')
            ->where('kd_kabupaten', $kabupatenId)
            ->orderBy('kecamatan')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $kecamatan,
        ]);
    }

    public function getKelurahan(Request $request)
    {
        $kecamatanId = $request->get('kecamatan_id');
        
        if ($kecamatanId === null || $kecamatanId === '') {
            return response()->json([
                'success' => false,
                'message' => 'Kecamatan ID required',
                'data' => []
            ]);
        }
        
        $kelurahan = DB::table('kelurahan')
            ->where('kd_kecamatan', $kecamatanId)
            ->orderBy('kelurahan')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $kelurahan,
        ]);
    }

    public function printCv()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return redirect()->route('user.dashboard')->with('error', 'Data karyawan tidak ditemukan.');
        }

        $id = $karyawan->kd_karyawan;

        // Data keluarga
        $keluarga = DB::table('HRD_R_KELUARGA')
            ->select(
                'HRD_R_KELUARGA.KD_KARYAWAN', 
                'HRD_R_KELUARGA.URUT_KLRG', 
                'HRD_HUB_KELUARGA.HUB_KLRG', 
                'HRD_R_KELUARGA.NAMA', 
                'HRD_R_KELUARGA.TEMPAT_LAHIR', 
                'HRD_R_KELUARGA.TGL_LAHIR', 
                'HRD_R_KELUARGA.JK', 
                'HRD_JENJANG_PENDIDIKAN.JENJANG_DIDIK', 
                'HRD_PEKERJAAN.PEKERJAAN'
            )
            ->leftJoin('HRD_HUB_KELUARGA', 'HRD_R_KELUARGA.KD_HUB_KLRG', '=', 'HRD_HUB_KELUARGA.KD_HUB_KLRG')
            ->leftJoin('HRD_JENJANG_PENDIDIKAN', 'HRD_R_KELUARGA.KD_JENJANG_DIDIK', '=', 'HRD_JENJANG_PENDIDIKAN.KD_JENJANG_DIDIK')
            ->join('HRD_PEKERJAAN', 'HRD_R_KELUARGA.KD_PEKERJAAN', '=', 'HRD_PEKERJAAN.KD_PEKERJAAN')
            ->where('HRD_R_KELUARGA.KD_KARYAWAN', '=', $id)
            ->orderBy('HRD_R_KELUARGA.TGL_LAHIR', 'ASC')
            ->get();

        // Data bahasa
        $bahasa = DB::table('HRD_R_BAHASA')
            ->select(
                'HRD_R_BAHASA.KD_KARYAWAN',
                'HRD_R_BAHASA.URUT_BAHASA',
                'HRD_BAHASA.BAHASA',
                'HRD_TINGKAT_BAHASA.TINGKAT_BAHASA'
            )
            ->join('HRD_BAHASA', 'HRD_R_BAHASA.KD_BAHASA', '=', 'HRD_BAHASA.KD_BAHASA')
            ->join('HRD_TINGKAT_BAHASA', 'HRD_R_BAHASA.KD_TINGKAT_BAHASA', '=', 'HRD_TINGKAT_BAHASA.KD_TINGKAT_BAHASA')
            ->where('HRD_R_BAHASA.KD_KARYAWAN', '=', $id)
            ->get();

        // Data riwayat pendidikan
        $riwayatPendidikan = DB::table('HRD_R_PENDIDIKAN')
            ->select(
                'HRD_R_PENDIDIKAN.KD_KARYAWAN',
                'HRD_JENJANG_PENDIDIKAN.JENJANG_DIDIK',
                'HRD_JURUSAN.JURUSAN',
                'HRD_R_PENDIDIKAN.NAMA_LEMBAGA',
                'HRD_R_PENDIDIKAN.TAHUN_LULUS',
                'HRD_R_PENDIDIKAN.NO_IJAZAH',
                'HRD_R_PENDIDIKAN.TEMPAT',
                'HRD_R_PENDIDIKAN.URUT_DIDIK'
            )
            ->join('HRD_JENJANG_PENDIDIKAN', 'HRD_R_PENDIDIKAN.KD_JENJANG_DIDIK', '=', 'HRD_JENJANG_PENDIDIKAN.KD_JENJANG_DIDIK')
            ->join('HRD_JURUSAN', 'HRD_R_PENDIDIKAN.KD_JURUSAN', '=', 'HRD_JURUSAN.KD_JURUSAN')
            ->where('HRD_R_PENDIDIKAN.KD_KARYAWAN', '=', $id)
            ->orderBy('HRD_R_PENDIDIKAN.URUT_DIDIK', 'ASC')
            ->get();

        // Data riwayat pekerjaan
        $riwayatPekerjaan = DB::table('HRD_R_KERJA')
            ->where('KD_KARYAWAN', '=', $id)
            ->orderBy('URUT_KERJA', 'ASC')
            ->get();

        // Data riwayat organisasi
        $riwayatOrganisasi = DB::table('HRD_R_ORGANISASI')
            ->where('KD_KARYAWAN', '=', $id)
            ->orderBy('URUT_ORG', 'ASC')
            ->get();

        // Data riwayat penghargaan
        $riwayatPenghargaan = DB::table('HRD_R_PENGHARGAAN')
            ->where('KD_KARYAWAN', '=', $id)
            ->orderBy('URUT_PENG', 'ASC')
            ->get();

        // Data riwayat seminar
        $riwayatSeminar = DB::table('hrd_r_seminar')
            ->join('hrd_sumber_dana', 'hrd_r_seminar.kd_sumber_dana', '=', 'hrd_sumber_dana.kd_sumber_dana')
            ->selectRaw('*, lower(hrd_sumber_dana.sumber_dana) as sumber_dana')
            ->where('hrd_r_seminar.kd_karyawan', '=', $id)
            ->orderBy('hrd_r_seminar.urut_seminar', 'asc')
            ->get();

        // Data riwayat tugas
        $riwayatTugas = DB::table('view_tempat_kerja')
            ->where('kd_karyawan', '=', $id)
            ->orderBy('no_urut', 'desc')
            ->get();

        // Data tugas tambahan
        $tugasTambahan = DB::table('hrd_tugas_tambahan as tugas')
            ->join('view_tampil_karyawan as karyawan', 'tugas.kd_karyawan', '=', 'karyawan.kd_karyawan')
            ->leftJoin('hrd_jabatan_struktural as jabatan', 'tugas.kd_jab_struk', '=', 'jabatan.kd_jab_struk')
            ->leftJoin('hrd_jenis_tenaga_sub_detail as sub_detail', function($join) {
                $join->on('tugas.kd_jenis_tenaga', '=', 'sub_detail.kd_jenis_tenaga')
                    ->on('tugas.kd_detail', '=', 'sub_detail.kd_detail')
                    ->on('tugas.kd_sub_detail', '=', 'sub_detail.kd_sub_detail');
            })
            ->leftJoin('hrd_ruangan as ruangan', 'tugas.kd_ruangan', '=', 'ruangan.kd_ruangan')
            ->select(
                'tugas.*', 
                'karyawan.*', 
                'jabatan.jab_struk as jab_struk_tambahan', 
                'sub_detail.sub_detail as sub_detail_tambahan', 
                'ruangan.ruangan as ruangan_tambahan'
            )
            ->where('tugas.kd_karyawan', '=', $id)
            ->whereNotNull('tugas.verif_4')
            ->whereNotNull('tugas.kd_karyawan_verif_4')
            ->whereNotNull('tugas.waktu_verif_4')
            ->whereNotNull('id_dokumen')
            ->orderBy('tugas.tmt_awal', 'asc')
            ->get();

        return view('karyawan.identitas.print-cv', compact(
            'karyawan', 
            'keluarga', 
            'bahasa', 
            'riwayatPendidikan', 
            'riwayatPekerjaan', 
            'riwayatOrganisasi', 
            'riwayatPenghargaan', 
            'riwayatSeminar', 
            'riwayatTugas', 
            'tugasTambahan'
        ));
    }
}