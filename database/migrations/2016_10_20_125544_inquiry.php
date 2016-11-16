<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Inquiry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('inquiry', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->index();
            $table->longText('inquir_details');
            $table->integer('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('users');
            $table->enum('priority',['Urgent','Normal'])->default('Normal');
            $table->enum('delivery_required',['Yes','No'])->default('Yes');
            $table->string('location')->nullable();
            $table->integer('replies_count')->unsigned();
            $table->integer('remarks')->unsigned();
            $table->integer('industry_id')->unsigned();
//            $table->foreign('industry_id')->references('id')->on('industry');
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
        Schema::table('inquiry', function($table) {
            $table->dropForeign('customer_id');
//            $table->dropForeign('industry_id');
        });
        Schema::drop('inquiry');
    }
}
