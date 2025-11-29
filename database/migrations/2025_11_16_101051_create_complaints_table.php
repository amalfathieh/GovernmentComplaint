<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();

            $table->string('reference_number')->unique();

            $table->string('type')->nullable();

            $table->string('title')->nullable();

            $table->text('description');
            $table->text('note')->nullable();
            $table->string('location')->nullable();
            $table->enum('status', [
                'new',
                'under_review',
                'in_progress',
                'need_info',
                'resolved',
                'rejected',
                'closed'
            ])->default('new');

            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('locked_until')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
