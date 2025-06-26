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
        Schema::table('ec_customers', function (Blueprint $table) {
            $table->string('otp_code')->nullable()->after('email');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
            $table->boolean('is_otp_verified')->default(false)->after('otp_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ec_customers', function (Blueprint $table) {
                    $table->dropColumn(['otp_code', 'otp_expires_at', 'is_otp_verified']);

        });
    }
};
