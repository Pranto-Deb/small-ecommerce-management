<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('shop_products',function(Blueprint $t){$t->id();$t->foreignId('shop_id')->constrained()->cascadeOnDelete();$t->foreignId('product_id')->constrained()->cascadeOnDelete();$t->decimal('price',12,2);$t->text('details')->nullable();$t->string('thumbnail_path')->nullable();$t->timestamps();$t->unique(['shop_id','product_id']);}); } public function down(): void { Schema::dropIfExists('shop_products'); } };
