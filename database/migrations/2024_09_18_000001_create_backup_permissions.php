<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class CreateBackupPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create backup permissions
        $permissions = [
            'hrd_manage_backup' => 'Mengelola backup data karyawan',
            'hrd_view_backup' => 'Melihat riwayat backup',
            'hrd_perform_backup' => 'Melakukan backup data'
        ];

        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['guard_name' => 'web']
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $permissions = [
            'hrd_manage_backup',
            'hrd_view_backup', 
            'hrd_perform_backup'
        ];

        foreach ($permissions as $permission) {
            Permission::where('name', $permission)->delete();
        }
    }
}