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
        // 1. On retire la colonne de la table principale
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['houseworker_id']);
            $table->dropColumn('houseworker_id');
        });

        // 2. On l'ajoute dans la table pivot (reservation_service)
        Schema::table('reservation_service', function (Blueprint $table) {
            // Nullable car au début, personne n'est assigné à la tâche
            $table->foreignId('houseworker_id')->nullable()->after('service_id')->constrained('users');
            
            // On ajoute aussi un statut par tâche (optionnel mais recommandé)
            // ex: Le ménage est 'fini', mais le repassage est 'en cours'
            $table->enum('status', ['pending', 'assigned', 'completed'])->default('pending')->after('houseworker_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation_service', function (Blueprint $table) {
            $table->dropForeign(['houseworker_id']);
            $table->dropColumn(['houseworker_id', 'status']);
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->foreignId('houseworker_id')->nullable()->constrained('users');
        });
    }
};
