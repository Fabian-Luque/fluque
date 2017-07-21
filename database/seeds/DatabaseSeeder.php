<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $this->call(TipoPropiedadTableSeeder::class);
       $this->call(TipoFuenteTableSeeder::class);
       $this->call(MetodoPagoTableSeeder::class);
       $this->call(EstadoReservaTableSeeder::class);
       $this->call(TipoClienteTableSeeder::class);
       $this->call(TipoComprobanteTableSeeder::class);
       $this->call(CategoriasTableSeeder::class);
       $this->call(TipoMonedaTableSeeder::class);
       $this->call(ClasificacionMonedaTableSeeder::class);
       $this->call(EstadoHabitacionTableSeeder::class);
       $this->call(EstadoServicioTableSeeder::class);
       $this->call(TipoCobroTableSeeder::class);
    }
}
