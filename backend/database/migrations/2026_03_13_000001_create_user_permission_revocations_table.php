<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $permissionsTable = config('permission.table_names.permissions') ?? 'permissions';

        Schema::create('user_permission_revocations', function (Blueprint $table) use ($permissionsTable) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained($permissionsTable)->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'permission_id']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_permission_revocations');
    }
};

