<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MigrateUserController extends Controller
{
    public function __invoke()
    {
        $totalProcessed = 0;

        DB::table('hrd_karyawan')
            // ->where('status_peg', 1)
            ->orderBy('kd_karyawan') // Tambahkan klausa orderBy di sini
            ->chunk(100, function ($users) use (&$totalProcessed) {
                foreach ($users as $user) {
                    // Cek apakah user sudah ada
                    $existingUser = DB::table('users')->where('kd_karyawan', $user->kd_karyawan)->first();
                    
                    if ($existingUser) {
                        // Update existing user
                        DB::table('users')->where('kd_karyawan', $user->kd_karyawan)->update([
                            'name' => $user->nama,
                            'email' => $user->email,
                            'is_active' => $user->status_peg == 1 ? 1 : 0,
                            // 'password' => Hash::make('12345'),
                            'updated_at' => Carbon::now(),
                        ]);
                    } else {
                        // Insert new user
                        DB::table('users')->insert([
                            'kd_karyawan' => $user->kd_karyawan,
                            'name' => $user->nama,
                            'email' => $user->email,
                            'is_active' => $user->status_peg == 1 ? 1 : 0,
                            'password' => Hash::make('12345'),
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                    }

                    $totalProcessed++;
                }
            });

        return response()->json([
            'success' => true,
            'message' => 'Migration success',
            'total' => $totalProcessed
        ]);
    }
}
