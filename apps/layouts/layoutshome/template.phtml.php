<!DOCTYPE html>
<html>
<head>
	<title>Control de Expedientes</title>
	<meta content='text/html; charset=UTF-8' http-equiv='Content-Type'>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<?php $this->assets->outputCss(); ?>
	<?php $this->assets->outputJs(); ?>
</head>
<body>

	<!-- <div class="row">
		<nav class="navbar  navbar-inverse  navbar-fixed-top">
			  <div class="container">
			  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
			  <span class="sr-only"> Toggle navigation</span>
			  <span class="icon-bar"> </span>
			  <span class="icon-bar"> </span>
			  <span class="icon-bar"> </span>
			  </button>
			  
			   <a class="navbar-brand" href="#">Control de expedientes</a>
			       <div class="navbar-collapse collapse">
			           <ul class="nav navbar-nav navbar-right">
					     <li class=""><a href="#">Inicio de Sesión</a></li>
						 <li><a href="#">Registro</a></li>
					   </ul>
			       </div>
			  </div>
		</nav>
	</div> -->

	<div class="container">
		<div class="row">
			<?php echo $this->getContent(); ?>
		</div>
	</div>
</body>
<footer>
	<div class="container">
		<p class="navbar-text pull-left">© 2016 - Desarrollado por <a href="http://desoftware.mx/" target="_blank" >Desoftware</a>
		</p>	      
	</div>
</footer>
</html>