<?php

use Illuminate\Database\Seeder;

class PrimerUser extends Seeder {
    public function run() {
        DB::table('users')->insert(
            array(
                'name' 	   => 'admin',
                'email'    => 'no-reply@gofeels.com',
                'password' => bcrypt('12345678')
            )
        );
    }
}