@extends('administrador.home_admin')
@include('administrador.requests')
@section('acciones')
<div class="card" style="width: 15%;">
         <div class="form-group has-feedback">
    
          <select id="select-acciones" type="text" class="form-control" name="tipo_propiedad_id">
                <option value="bus">
                  BUSCAR
                </option>
                <option value="EDI">
                  EDITAR
                </option>
          </select>

          <input type="input-acciones" name="">
        </div>
</div>
@endsection
