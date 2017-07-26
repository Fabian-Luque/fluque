        <form action="registrar" method="post">
      <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">   

      
          
          <div class="form-group has-feedback">
            <label>nombre</label>
            <input type="text" class="form-control" name="name" >
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
          </div>


           <div class="form-group has-feedback">
             <label>email</label>
            <input type="email" class="form-control" name="email" >
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          </div>

          <div class="form-group has-feedback">
                <label>password</label>
            <input type="password" class="form-control" name="password" >
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>

           <div class="form-group has-feedback">
                <label>telefono</label>
            <input type="text" class="form-control" name="phone" >
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>

          Propiedad

              <div class="form-group has-feedback">
                <label>nombre propiedad</label>
            <input type="text" class="form-control" name="nombre" >
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>

            <div class="form-group has-feedback">
                <label>Tipo propiedad</label>
            <input type="text" class="form-control" name="tipo" >
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>

             <div class="form-group has-feedback">
                <label>Numero habitaciones</label>
            <input type="text" class="form-control" name="numero_habitaciones" >
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>

             <div class="form-group has-feedback">
                <label>Ciudad</label>
            <input type="text" class="form-control" name="ciudad" >
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>

              <div class="form-group has-feedback">
              <label>Direccion</label>
            <input type="text" class="form-control" name="direccion" >
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
         
          <div class="row">
            

            
            <div class="col-xs-4">
              <button type="submit" class="btn btn-primary btn-block btn-flat">Registrar</button>
            </div><!-- /.col -->
          </div>
        </form>