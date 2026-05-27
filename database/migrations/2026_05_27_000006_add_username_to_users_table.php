<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->after('name');
        });

        DB::table('users')
            ->select(['id', 'name'])
            ->orderBy('id')
            ->get()
            ->each(function (object $user): void {
                $base = Str::lower(Str::slug($user->name, ''));

                if ($base === '') {
                    $base = 'user';
                }

                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'username' => $base.$user->id,
                    ]);
            });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('username');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};