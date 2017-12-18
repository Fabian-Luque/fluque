<?php

use Illuminate\Database\Seeder;

class PrimerUser extends Seeder {
    public function run() {
        DB::table('users')->insert(
            array(
                'name' 	   => 'admin',
                'email'    => 'cambiodeclave@gofeels.com',
                'password' => bcrypt('12345678')
            )
        );
    }
}