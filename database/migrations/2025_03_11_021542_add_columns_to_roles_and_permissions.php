<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToRolesAndPermissions extends Migration
{
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('description')->nullable()->after('name');
            $table->integer('level')->default(0)->after('description');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->string('slug')->unique()->after('name');
            $table->string('description')->nullable()->after('slug');
        });
    }

    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['description', 'level']);
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['slug', 'description']);
        });
    }
}
