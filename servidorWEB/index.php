<?php
//Header
include_once 'includes/header.php';
?>

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js"></script>

  <title>Tela Inicial</title>
</head>

<body>
<!--Filtro-->
  <?php
  $unidade = 1;
  $tempo = 3;
  $id_regiao = 999;

  if (isset($_POST['btn_filtrar'])) {
    $unidade = $_POST['unidade'];
    $tempo = $_POST['tempo'];
    $id_regiao = $_POST['id_regiao'];
  }
  ?>

  <div class=row>
    <div class="col s12 m6 push-m3">
      <h3 class="light">Filtros</h3>
      <form method="post" action="index.php">
        <label for="unidade">Unidade de Consumo</label>
        <select name="unidade" class="browser-default">
          <option value="1" <?php if($unidade == 1) echo " selected"; ?>>Litros(L)</option>
          <option value="2" <?php if($unidade == 2) echo " selected"; ?>>Reais($)</option>
        </select>

        <label for="tempo">Intervalo de Tempo</label>
        <select name="tempo" class="browser-default">
          <option value="1" <?php if($tempo == 1) echo " selected"; ?>>Janeiro</option>
          <option value="2" <?php if($tempo == 2) echo " selected"; ?>>Fevereiro</option>
          <option value="3" <?php if($tempo == 3) echo " selected"; ?>>Março</option>
          <option value="4" <?php if($tempo == 4) echo " selected"; ?>>Abril</option>
          <option value="5" <?php if($tempo == 5) echo " selected"; ?>>Maio</option>
          <option value="6" <?php if($tempo == 6) echo " selected"; ?>>Junho</option>
          <option value="7" <?php if($tempo == 7) echo " selected"; ?>>Julho</option>
          <option value="8" <?php if($tempo == 8) echo " selected"; ?>>Agosto</option>
          <option value="9" <?php if($tempo == 9) echo " selected"; ?>>Setembro</option>
          <option value="10" <?php if($tempo == 10) echo " selected"; ?>>Outubro</option>
          <option value="11" <?php if($tempo == 11) echo " selected"; ?>>Novembro</option>
          <option value="12" <?php if($tempo == 12) echo " selected"; ?>>Dezembro</option>
          <option value="13" <?php if($tempo == 13) echo " selected"; ?>>Último Ano</option>
        </select>

        <label for="id_regiao">Região</label>
        <select name="id_regiao" class="browser-default">
          <option value="999" <?php if($id_regiao == 999) echo " selected"; ?>>Todas</option>
          <?php
          $sql = "SELECT DISTINCT * FROM `regioes`";

          $stmt = $PDO->prepare($sql);
          $stmt->execute();

          while ($dados = $stmt->fetch(PDO::FETCH_OBJ)) :
          ?>
            <option value="<?php echo $dados->id_regiao; ?>" <?php if($id_regiao == $dados->id_regiao) echo " selected"; ?>><?php echo $dados->nome; ?></option>
          <?php
          endwhile;
          ?>
        </select>
        <div class="input-field col push-m5">
          <button class="btn" type="submit" name="btn_filtrar">Filtrar</button>
      </form>
    </div>
  </div>

  <?php

  if ($unidade == 1) {
    $sql = "SELECT SUM(l.consumoPulso) AS consumoPulso FROM leituras l";
    if ($id_regiao != 999) {
      $sql = $sql . " INNER JOIN clientes c
                    ON c.id_hidrometro = l.id_hidrometro
                    WHERE c.id_regiao = :id_regiao";
    } else {
      $sql = $sql . " WHERE 1";
    }
  } else {
    $sql = "SELECT SUM(consumoPulso*0.37) AS consumoPulso FROM leituras l";
    if ($id_regiao != 999) {
      $sql = $sql . " INNER JOIN clientes c
                    ON c.id_hidrometro = l.id_hidrometro
                    WHERE c.id_regiao = :id_regiao";
    } else {
      $sql = $sql . " WHERE 1";
    }
  }
  switch ($tempo) {
    case 1:
      $sql = $sql . " AND MONTH(l.dataHora) = 1";
      break;
    case 2:
      $sql = $sql . " AND MONTH(l.dataHora) = 2";
      break;
    case 3:
      $sql = $sql . " AND MONTH(l.dataHora) = 3";
      break;
    case 4:
      $sql = $sql . " AND MONTH(l.dataHora) = 4";
      break;
    case 5:
      $sql = $sql . " AND MONTH(l.dataHora) = 5";
      break;
    case 6:
      $sql = $sql . " AND MONTH(l.dataHora) = 6";
      break;
    case 7:
      $sql = $sql . " AND MONTH(l.dataHora) = 7";
      break;
    case 8:
      $sql = $sql . " AND MONTH(l.dataHora) = 8";
      break;
    case 9:
      $sql = $sql . " AND MONTH(l.dataHora) = 9";
      break;
    case 10:
      $sql = $sql . " AND MONTH(l.dataHora) = 10";
      break;
    case 11:
      $sql = $sql . " AND MONTH(l.dataHora) = 11";
      break;
    case 12:
      $sql = $sql . " AND MONTH(l.dataHora) = 12";
      break;
  }
  if ($tempo == 13) {
    $sql = $sql . " GROUP BY MONTH(l.dataHora)";
  } else {
    $sql = $sql . " GROUP BY DAY(l.dataHora)";
  }

  $stmt = $PDO->prepare($sql);
  $stmt->bindParam(':id_regiao', $id_regiao);
  $stmt->execute();


  $array = array();

  while ($dados = $stmt->fetch(PDO::FETCH_OBJ)) :
    array_push($array, $dados->consumoPulso);
  endwhile;

  
  ?>

<!--Exibição Gráfico-->
  <div class="container">
    <canvas id="myChart"></canvas>
  </div>


  <?php
  //Footer
  include_once 'includes/footer.php';
  ?>

<!--Código Gráfico-->
  <script>
    let vetor = <?= json_encode($array) ?>;
    let tempo = <?=$tempo?>;
    let unidade = <?= $unidade ?>;
    
    let myChart = document.getElementById('myChart').getContext('2d');
    let numDias = [];
    let label;

    for (let i = 1; i <= vetor.length; i++) {
      numDias.push(i);
    }

    if(unidade == 1){
      label = "Consumo(L)";
    }
    else{
      label = "Consumo($)";
    }
    if(tempo == 13){
      label = label + " / Mês       ";
    }
    else{ 
      label = label + " / Dia       ";
    }
    
    // Global Options
    Chart.defaults.global.defaultFontFamily = 'Lato';
    Chart.defaults.global.defaultFontSize = 18;
    Chart.defaults.global.defaultFontColor = '#777';

    let massPopChart = new Chart(myChart, {
      type: 'bar', // bar, horizontalBar, pie, line, doughnut, radar, polarArea
      data: {
        labels: numDias,
        datasets: [{
          label: label,
          data: vetor,
          //backgroundColor:'green',
          backgroundColor:

            'rgba(54, 162, 235, 0.6)'

            ,
          borderWidth: 1,
          borderColor: '#777',
          hoverBorderWidth: 3,
          hoverBorderColor: '#000'
        }]
      },
      options: {
        title: {
          display: true,
          text: 'Consumo de Água       ',
          fontSize: 25
        },
        legend: {
          display: true,
          position: 'top',
          labels: {
            fontColor: '#000'
          }
        },
        layout: {
          padding: {
            left: 100,
            right: 100,
            bottom: 100,
            top: 0
          }
        },
        tooltips: {
          enabled: true
        }
      }
    });
  </script>