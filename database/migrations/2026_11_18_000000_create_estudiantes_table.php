<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->unique();
            $table->string('nombres', 60);
            $table->string('apellidos', 60);
            $table->string('email', 80)->unique();
            $table->string('ciclo', 10);
            $table->decimal('promedio', 4, 2);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('estudiantes'); }
};
