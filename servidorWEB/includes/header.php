<!DOCTYPE html>
<?php 
  include_once 'mensagem.php';
  include_once 'conexao.php';
?>
  <html>
    
    <head>
      <!--Import Google Icon Font-->
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <!--Import materialize.css-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

      <!--Let browser know website is optimized for mobile-->
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

      
    </head>

    <nav class="blue lighten-1">
      <div class="nav-wrapper">
        <a href="index.php" class="brand-logo right"> <img  src="includes/copasa.png"></a>
        <ul id="nav-mobile" class="left hide-on-med-and-down">
          <li><a href="listarClientes.php">Clientes</a></li>
          <li><a href="listarRegioes.php">Regiões</a></li>
          <li><a href="listarVazamentos.php">Vazamentos</a></li>
        </ul>
      </div>
  </nav>
    <body>