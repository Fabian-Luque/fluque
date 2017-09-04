<?php

use Illuminate\Database\Seeder;

class PrimerUser extends Seeder {
    public function run() {
        DB::table('users')->insert(
            array(
                'name' 	   => 'admin',
                'email'    => 'soporte@gofeels.com',
                'password' => bcrypt('78831375')
            )
        );
    }
}