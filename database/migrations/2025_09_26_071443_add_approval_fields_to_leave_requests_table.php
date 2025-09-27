<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->string('reject_reason')->nullable()->after('status');
            $table->string('approver_name')->nullable()->after('reject_reason');
            $table->string('approver_position')->nullable()->after('approver_name');
            $table->date('approval_date')->nullable()->after('approver_position');
            $table->enum('approval_type', ['paid', 'unpaid'])->nullable()->after('approval_date');
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn([
                'reject_reason',
                'approver_name',
                'approver_position',
                'approval_date',
                'approval_type',
            ]);
        });
    }
};
