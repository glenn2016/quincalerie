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
        Schema::create('facture_produits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('facture_id')->nullable();
            $table->foreign('facture_id')->references('id')->on('factures')->onDelete('cascade');
            $table->unsignedBigInteger('produit_id')->nullable();
            $table->foreign('produit_id')->references('id')->on('produits')->onDelete('cascade');
            $table->integer('quantite')->nullable();
            $table->float('prix_total')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facture_produits');
    }
};
