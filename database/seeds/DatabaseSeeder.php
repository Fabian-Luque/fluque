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

/*       $this->call(TipoPropiedadTableSeeder::class);
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
       $this->call(TipoCobroTableSeeder::class);*/
       $this->call(SeccionTableSeeder::class);
       $this->call(PermisosTableSeeder::class);
       $this->call(RolTableSeeder::class);
       $this->call(PermisosRolTableSeeder::class);
       $this->call(PropiedadUserTableSeeder::class);
       $this->call(EstadoTableSeeder::class);
       $this->call(EstadosdeCuenta::class);
       $this->call(PrimerUser::class);
       $this->call(EstadoCajaTableSeeder::class);

    }
}
