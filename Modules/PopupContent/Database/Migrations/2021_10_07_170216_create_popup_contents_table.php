<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePopupContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('popup_contents', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->text('title')->nullable();
            $table->text('message')->nullable();
            $table->text('link')->nullable();
            $table->text('btn_txt')->nullable();
            $table->timestamps();
        });

        $popup = new \Modules\PopupContent\Entities\PopupContent();
        $popup->image = 'public/uploads/popup/1.png';
        $popup->title = 'Want to get more offer?';
        $popup->message = "Get you 15% discount for all your shopping.";
        $popup->link = '/';
        $popup->btn_txt = 'Visit Website';
        $popup->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('popup_contents');
    }
}
