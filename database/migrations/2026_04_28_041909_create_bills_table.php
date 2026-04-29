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
        // Schema::create('bills', function (Blueprint $table) {
            // $table->id();
            // $table->timestamps();
        // });
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_no')->unique();
            $table->string('customer_name');
            $table->date('bill_date');
            $table->decimal('item_total', 10, 2)->default(0);
            $table->decimal('gst_percent', 5, 2)->default(0);
            $table->decimal('gst_amount', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
