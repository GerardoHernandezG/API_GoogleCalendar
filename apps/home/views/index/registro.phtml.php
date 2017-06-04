<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
	<div class="well barraregistro">
	    <form action="registro" method="post">
	        <legend>Registro de Aplicación</legend>
		        <div class="row">
		        	<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				        <div class="form-group">
				            <label for="username-email">Correo</label>
				            <?= $correo ?>
				        </div>
				    </div>
			        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				        <div class="form-group">
				            <label for="password">Contraseña</label>
				            <?= $password ?>
				        </div>
				    </div>
	        	</div>

	        	<div class="row">
	        		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				        <div class="form-group">
				            <label for="username-email">Nombre</label>
				            <?= $nombre ?>
				        </div>
				    </div>
				    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				        <div class="form-group">
				            <label for="username-email">Apellido</label>
				            <?= $apellido ?>
				        </div>
				    </div>
	        	</div>

	        	<div class="row">
				    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				        <div class="form-group">
				            <label for="username-email">Empresa</label>
				            <?= $empresa ?>
				        </div>
				    </div>
	        	</div>
 				
 				<div class="row">
 					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	            		<button class="btn btn-danger" type="button" id="cancelar">Cancelar</button>
	            		<input type="submit" class="btn btn-success btn-login-submit pull-right" value="Guardar"/>
	        		</div>
 				</div>   
	    </form>
	</div>
</div>
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
	<div class="well barraregistro">
        <legend>Datos de la App</legend>
        	<div class="row">
        		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			        <div class="form-group">
			            <label for="username-email">Client Id:</label>
			            <?= $client_id ?>
			        </div>
			    </div>
        	</div>
	        <div class="row">
	        	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			        <div class="form-group">
			            <label for="username-email">Codigo de Autorización App:</label>
			            <?= $token ?>
			        </div>
			    </div>
        	</div>
	</div>
</div>
<script type="text/javascript">
	var site_url = <?= $this->url->get() ?>

	$(document).ready(function(){
		$('body').on('click','#cancelar',regresar);
	});

	function regresar(){
		pagina = site_url+'home/login';
		document.location.href=pagina;
	}
</script>