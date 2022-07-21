<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Snayvik\Translation\Models\SnvkLocale;

class CreateSnvkLocalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('snvk_locales', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 20)->unique();
            $table->string('name')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        $lang_directories = File::directories(App::langPath());
        foreach ($lang_directories as $dir){
            $dir_name = File::name($dir);

            SnvkLocale::create(['locale' => $dir_name]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('snvk_locales');
    }
}
