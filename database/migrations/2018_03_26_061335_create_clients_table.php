<?php

use App\Client;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_email',50);
            $table->string('password',100);
            $table->string('user_name',50);
            $table->timestamps();
        });
        $newUser = [
            'user_email' => '123@abc',
            'user_name' => 'admin',
            'password' => bcrypt('1234567')
        ];
        $user = Client::create($newUser);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
