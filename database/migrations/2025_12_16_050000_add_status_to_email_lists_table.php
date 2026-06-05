// database/migrations/2025_12_16_050000_add_status_to_email_lists_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('email_lists', function (Blueprint $table) {
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
        });
    }

    public function down()
    {
        Schema::table('email_lists', function (Blueprint $table) {
            $table->dropColumn(['status', 'sent_at', 'error_message', 'retry_count']);
        });
    }
};