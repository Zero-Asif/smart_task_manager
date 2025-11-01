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
        Schema::table('tasks', function (Blueprint $table) {
            // 'user_id' কলামের পরে নতুন একটি কলাম যোগ করা হচ্ছে
            $table->foreignId('parent_id')
                  ->nullable() // এই কলামটি খালি থাকতে পারে (কারণ সব টাস্ক সাব-টাস্ক নয়)
                  ->after('user_id')
                  ->constrained('tasks') // এই কলামটি 'tasks' টেবিলের 'id'-এর সাথে সম্পর্কিত
                  ->onDelete('cascade'); // প্যারেন্ট টাস্ক ডিলিট হলে সাব-টাস্কগুলোও ডিলিট হয়ে যাবে
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // মাইগ্রেশন রোলব্যাক করার জন্য
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
