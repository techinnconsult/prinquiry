<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SellerInquiry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('seller_inquiry', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->index();
            $table->integer('seller_id')->unsigned();
            $table->foreign('seller_id')->references('id')->on('users');
            $table->integer('inquiry_id')->unsigned();
            $table->foreign('inquiry_id')->references('id')->on('inquiry');
            $table->enum('status', ['New','Reply', 'Pending']);
            $table->date('delievery_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seller_inquiry', function(Blueprint $table) {
            $table->dropForeign('seller_id');
            $table->dropForeign('inquiry_id');
        });
        Schema::drop('seller_inquiry');
    }
}
