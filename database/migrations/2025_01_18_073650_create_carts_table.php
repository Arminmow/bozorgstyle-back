<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to users table
            $table->json('items')->nullable(); // JSON column for cart items
            $table->timestamps(); // Created at & updated at timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('carts');
    }
};
