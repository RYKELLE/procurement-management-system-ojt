<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // Who did it — nullable in case it was a system action
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // What happened — e.g. "PR-00045 created by John Doe"
            $table->string('description');

            // What type of record this activity is about
            // e.g. 'purchase_request', 'purchase_order', 'invoice', 'supplier'
            $table->string('subject_type')->nullable();

            // The ID of that record — so you can link to it later
            $table->unsignedBigInteger('subject_id')->nullable();

            $table->timestamps(); // created_at is your activity timestamp
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
