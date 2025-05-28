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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable()->comment('專案名稱');
            $table->json('category')->nullable()->comment('json格式');
            // $table->foreignId('project_status_id')->nullable()->constrained('project_statuses', 'id')->comment('對應專案狀態id');
            $table->unsignedBigInteger('project_status')->nullable()->comment('對應專案狀態id(在const.php)');
            $table->text('description')->nullable()->comment('ˋ專案描述');
            $table->string('color_code')->nullable()->comment('色碼');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
