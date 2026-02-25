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
        // 1. On ajoute le prix total à la table reservations
        Schema::table('reservations', function (Blueprint $table) {
            $table->decimal('total_price', 10, 2)->after('address'); // ex: 150.00
            
            // On rend service_id nullable pour la transition, 
            // ou on le supprime si tu veux être propre tout de suite.
            // Pour être propre, on devrait le supprimer, car les services seront dans la table pivot.
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
        });


        // 2. On crée la table pivot pour relier Réservations <-> Services
        Schema::create('reservation_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            
            // Optionnel : On peut stocker le prix du service AU MOMENT de la commande
            // pour figer le prix même si le tarif change plus tard.
            $table->decimal('price_at_booking', 10, 2); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_service');
        
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('total_price');
            $table->foreignId('service_id')->constrained(); // On remet l'ancienne colonne
        });
    }
};
