<?php
//Header
include_once 'includes/header.php';
?>

<head>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta charset="UTF-8">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js"></script>


    <title>Tela Inicial</title>
</head>

<body>
<!--Filtro-->
<?php

    $tempo = 13;
    if (isset($_POST['btn_filtrar'])) {
        $tempo = $_POST['tempo'];
    }
?>
    <div class=row>
        <div class="col s12 m6 push-m3">
            <h3 class="light">Regiões</h3>
            <form method="post" action="listarRegioes.php">

                <label for="tempo">Intervalo de Tempo</label>
                <select name="tempo" class="browser-default">

                    <option value="1"<?php if($tempo==1) echo " selected";?>>Janeiro</option>
                    <option value="2"<?php if($tempo==2) echo " selected";?>>Fevereiro</option>
                    <option value="3"<?php if($tempo==3) echo " selected";?>>Março</option>
                    <option value="4"<?php if($tempo==4) echo " selected";?>>Abril</option>
                    <option value="5"<?php if($tempo==5) echo " selected";?>>Maio</option>
                    <option value="6"<?php if($tempo==6) echo " selected";?>>Junho</option>
                    <option value="7"<?php if($tempo==7) echo " selected";?>>Julho</option>
                    <option value="8"<?php if($tempo==8) echo " selected";?>>Agosto</option>
                    <option value="9"<?php if($tempo==9) echo " selected";?>>Setembro</option>
                    <option value="10"<?php if($tempo==10) echo " selected";?>>Outubro</option>
                    <option value="11"<?php if($tempo==11) echo " selected";?>>Novembro</option>
                    <option value="12"<?php if($tempo==12) echo " selected";?>>Dezembro</option>
                    <option value="13"<?php if($tempo==13) echo " selected";?>>Total</option>
                </select>

                <div class="input-field col push-m5">
                    <button class="btn" type="submit" name="btn_filtrar">Filtrar</button>
            </form>
        </div>
    </div>

    <?php

    $sql = "SELECT r.nome, SUM(l.consumoPulso) AS consumoPulso FROM leituras l";

    $sql = $sql . " INNER JOIN clientes c
                    ON c.id_hidrometro = l.id_hidrometro
                    INNER JOIN regioes r
                    ON r.id_regiao = c.id_regiao";


    switch ($tempo) {
        case 1:
            $sql = $sql . " WHERE MONTH(l.dataHora) = 1";
            break;
        case 2:
            $sql = $sql . " WHERE MONTH(l.dataHora) = 2";
            break;
        case 3:
            $sql = $sql . " WHERE MONTH(l.dataHora) = 3";
            break;
        case 4:
            $sql = $sql . " WHERE MONTH(l.dataHora) = 4";
            break;
        case 5:
            $sql = $sql . " WHERE MONTH(l.dataHora) = 5";
            break;
        case 6:
            $sql = $sql . " WHERE MONTH(l.dataHora) = 6";
            break;
        case 7:
            $sql = $sql . " WHERE MONTH(l.dataHora) = 7";
            break;
        case 8:
            $sql = $sql . " WHERE MONTH(l.dataHora) = 8";
            break;
        case 9:
            $sql = $sql . " WHERE MONTH(l.dataHora) = 9";
            break;
        case 10:
            $sql = $sql . " WHERE MONTH(l.dataHora) = 10";
            break;
        case 11:
            $sql = $sql . " WHERE MONTH(l.dataHora) = 11";
            break;
        case 12:
            $sql = $sql . " WHERE MONTH(l.dataHora) = 12";
            break;
    }
    $sql = $sql . " GROUP BY c.id_regiao";

    $stmt = $PDO->prepare($sql);
    $stmt->execute();

    $objetoLeitura = new stdClass();

    $arrayConsumo = array();
    $arrayRegiao = array();
    while ($dados = $stmt->fetch(PDO::FETCH_OBJ)) :  
        array_push($arrayConsumo, $dados->consumoPulso);
        array_push($arrayRegiao, $dados->nome);
    endwhile;
        
    ?>

<!--Exibição Gráfico -->
    <div class=row>
        <div class="col s12">
            <div class="container" style="position: relative; height:40vh; width:80vw">
                <canvas id="myChart"></canvas>
            </div>
        </div>
    </div>

<!--Tabela -->
    <div class=row>
        <div class="col s12 m6 push-m3">
            <table class="striped">
                <thead>
                    <tr>
                        <th>Região</th>
                        <th>Consumo Total(L)</th>
                        <th>Total Gasto($)</th>
                        <th>Número Clientes</th>
                    </tr>
                </thead>

                <tbody>
                    <?php

                    $sql = "SELECT r.id_regiao, r.nome, SUM(l.consumoPulso) as consumoTotal, ROUND(SUM(l.consumoPulso)*t.preco, 2) AS consumoTotalReais, count(DISTINCT c.id_cliente) AS numClientes FROM leituras l";

                    $sql = $sql . " INNER JOIN clientes c
                                    ON c.id_hidrometro = l.id_hidrometro
                                    RIGHT JOIN regioes r
                                    ON r.id_regiao = c.id_regiao
                                    LEFT JOIN tarifas t
                                    ON t.id_tarifa = c.id_tarifa";


                    switch ($tempo) {
                        case 1:
                            $sql = $sql . " WHERE MONTH(l.dataHora) = 1";
                            break;
                        case 2:
                            $sql = $sql . " WHERE MONTH(l.dataHora) = 2";
                            break;
                        case 3:
                            $sql = $sql . " WHERE MONTH(l.dataHora) = 3";
                            break;
                        case 4:
                            $sql = $sql . " WHERE MONTH(l.dataHora) = 4";
                            break;
                        case 5:
                            $sql = $sql . " WHERE MONTH(l.dataHora) = 5";
                            break;
                        case 6:
                            $sql = $sql . " WHERE MONTH(l.dataHora) = 6";
                            break;
                        case 7:
                            $sql = $sql . " WHERE MONTH(l.dataHora) = 7";
                            break;
                        case 8:
                            $sql = $sql . " WHERE MONTH(l.dataHora) = 8";
                            break;
                        case 9:
                            $sql = $sql . " WHERE MONTH(l.dataHora) = 9";
                            break;
                        case 10:
                            $sql = $sql . " WHERE MONTH(l.dataHora) = 10";
                            break;
                        case 11:
                            $sql = $sql . " WHERE MONTH(l.dataHora) = 11";
                            break;
                        case 12:
                            $sql = $sql . " WHERE MONTH(l.dataHora) = 12";
                            break;
                    }
                    $sql = $sql . " GROUP BY r.id_regiao
                                    ORDER BY consumoTotal 
                                    DESC";



                    $stmt = $PDO->prepare($sql);
                    $stmt->execute();

                    while ($dados = $stmt->fetch(PDO::FETCH_OBJ)) :
                    ?>
                        <tr>
                            <td><?php echo $dados->nome; ?></td>
                            <td><?php echo $dados->consumoTotal; if (!isset($dados->consumoTotal)) echo 0;  ?></td>
                            <td><?php echo $dados->consumoTotalReais; if (!isset($dados->consumoTotalReais)) echo 0;  ?></td>
                            <td><?php echo $dados->numClientes; ?></td>

                            <td><a href="historicoRegiao.php?id_regiao=<?php echo $dados->id_regiao; ?>" class="btn-floating blue"><i class="material-icons">search</i></a></td>
                            <td><a href="editarRegiao.php?id_regiao=<?php echo $dados->id_regiao; ?>" class="btn-floating orange"><i class="material-icons">edit</i></a></td>
                            <td><a href="#modal<?php echo $dados->id_regiao; ?>" class="btn-floating red modal-trigger"><i class="material-icons">delete</i></a></td>

                            <!-- Modal Structure -->
                            <div id="modal<?php echo $dados->id_regiao; ?>" class="modal">
                                <div class="modal-content">
                                    <h4>Opa!</h4>
                                    <h4>Deseja realmente Excluir essa Região?</h4>
                                </div>
                                <div class="modal-footer">
                                    <form method="post" action="dropRegiao.php">
                                        <input type="hidden" name="id_regiao" value="<?php echo $dados->id_regiao; ?>">
                                        <button type="submit" name="btn-deletar" class="btn red">Sim</button>
                                        <a href="#!" class="modal-close waves-effect waves-green btn-flat">Cancelar</a>
                                    </form>
                                </div>
                            </div>

                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <br><br>
            <a href="regiao.php" class="btn">Adicionar</a>
            <a href="index.php" class="btn green">Menu</a>
        </div>
    </div>

    <?php
    //Footer
    include_once 'includes/footer.php';
    ?>



<!--Código Gráfico-->
    <script>
        let vetorConsumo = <?= json_encode($arrayConsumo) ?>;
        let vetorRegiao = <?= json_encode($arrayRegiao) ?>;
        let myChart = document.getElementById('myChart').getContext('2d');

        // Global Options

        Chart.defaults.global.defaultFontFamily = 'Lato';
        Chart.defaults.global.defaultFontSize = 18;
        Chart.defaults.global.defaultFontColor = '#777';

        let massPopChart = new Chart(myChart, {
            type: 'pie', // bar, horizontalBar, pie, line, doughnut, radar, polarArea
            data: {
                labels: vetorRegiao,
                datasets: [{
                    label: 'Consumo(L) / Dia',
                    data: vetorConsumo,
                    //backgroundColor:'green',
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 162, 150, 0.6)',
                        'rgba(198,40,40, 0.6)',
                        'rgba(190, 20, 250, 0.6)',
                        'rgba(10, 11, 23, 0.6)',
                        'rgba(27,94,32, 0.6)',
                    ],

                    borderWidth: 1,
                    borderColor: '#777',
                    hoverBorderWidth: 3,
                    hoverBorderColor: '#000'
                }]
            },
            options: {
                title: {
                    display: true,
                    text: 'Consumo de Água por Região',
                    fontSize: 25
                },
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        fontColor: '#000'
                    }
                },
                layout: {
                    padding: {
                        left: 0,
                        right: 65,
                        bottom: 850,
                        top: 0
                    }
                },
                tooltips: {
                    enabled: true
                }
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.modal').modal();
        });
    </script>