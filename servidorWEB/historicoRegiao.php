<?php
//Header
include_once 'includes/header.php';
?>

<title>Regioes</title>

<?php 
//Inicia a Região
if(isset($_GET['id_regiao'])) $_SESSION['id_regiao'] = $_GET['id_regiao'];  
$id_regiao=$_SESSION['id_regiao'];
?>

<div class=row>
  <div class = "col s12 m6 push-m3">
    <h3 class="light">Histórico Região</h3>
    <br>
    <h4 class="light">Filtrar por Data:</h3>
    <form method="post" action="historicoRegiao.php">
        <div class="input-field col s1 m5">
          <label class="cat" for="dataDe">De</label>
          <input type="text" for="dataDe" name="dataDe" class="datepicker">
        </div>
        <div class="input-field col s1 m5">
          <label class="cat" for="dataAte">Até</label>
          <input type="text" for="dataAte" name="dataAte" class="datepicker">
        </div>
        <div class="input-field col s1 m2">
            <button class="btn" type="submit" name="btn_filtrar">Filtrar</button>
        </div>
      </form>
    <table class="striped">
        <thead>
            <tr>
            <th>Região</th>
            <th>Período</th>
            <th>Consumo Total(L)</th>
            <th>Total Gasto($)</th>
            </tr>
        </thead>

        <tbody>
            <?php
            
            if(isset($_POST['btn_filtrar'])):
                $dataDe = $_POST['dataDe'];
                $dataAte = $_POST['dataAte'];
                $sql= "SELECT  r.nome, SUM(l.consumoPulso) AS consumoTotal, ROUND(SUM(l.consumoPulso)*t.preco, 2) AS consumoTotalReais  FROM leituras l
                        INNER JOIN clientes c
                        ON c.id_hidrometro = l.id_hidrometro
                        INNER JOIN regioes r
                        ON r.id_regiao = c.id_regiao
                        INNER JOIN tarifas t
                        ON t.id_tarifa = c.id_tarifa
                        WHERE r.id_regiao = :id_regiao
                        AND l.dataHora > :dataDe
                        AND l.dataHora < :dataAte
                        ORDER BY l.dataHora
                        DESC";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam(':id_regiao', $id_regiao);
                $stmt->bindParam(':dataDe', $dataDe);
                $stmt->bindParam(':dataAte', $dataAte);
                $stmt->execute();
            
                $dados = $stmt->fetch(PDO::FETCH_OBJ);

                ?>
                <tr>
                <td><?php echo $dados->nome;?></td>
                <td><?php echo $dataDe. " até " .$dataAte;?></td>
                <td><?php echo $dados->consumoTotal;?></td>
                <td><?php echo $dados->consumoTotalReais;?></td>
                </tr>
                </tbody>
                
    </table>
    <br><br>
    <table class="striped">
        <thead>
        <tr>
            <th>Hora/Data</th>
            <th>Cliente</th>
            <th>Consumo(L)</th>
            <th>Consumo($)</th>
        </tr>
        </thead>                

        <tbody>
            <?php

            $sql= "SELECT c.nome, l.dataHora, l.consumoPulso , ROUND((l.consumoPulso*t.preco), 2) AS consumoReais  FROM leituras l
                    INNER JOIN clientes c
                    ON c.id_hidrometro = l.id_hidrometro
                    INNER JOIN regioes r
                    ON r.id_regiao = c.id_regiao
                    INNER JOIN tarifas t
                    ON t.id_tarifa = c.id_tarifa
                    WHERE r.id_regiao = :id_regiao
                    AND l.dataHora > :dataDe
                    AND l.dataHora < :dataAte
                    ORDER BY l.dataHora
                    DESC
                    LIMIT 100";

            $stmt = $PDO->prepare($sql);
            $stmt->bindParam(':id_regiao', $id_regiao);
            $stmt->bindParam(':dataDe', $dataDe);
            $stmt->bindParam(':dataAte', $dataAte);
            $stmt->execute();
        
            while ($dados = $stmt->fetch(PDO::FETCH_OBJ)):
            ?>
            <tr>
            <td><?php echo $dados->dataHora;?></td>
            <td><?php echo $dados->nome;?></td>
            <td><?php echo $dados->consumoPulso;?></td>
            <td><?php echo $dados->consumoReais;?></td>
            </tr>
        <?php endwhile;
        endif;?>
        
      </tbody>
    </table>
            
    <br>
    <a href="cliente.php" class="btn">Relatório</a>
    <a href="listarRegioes.php" class="btn green">Voltar</a>
  </div>
</div>



<?php
//Footer
include_once 'includes/footer.php';
?>

<script>
   
     $(document).ready(function(){
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            i18n: {
                cancel: 'Cancelar',
                clear: 'Limpar',
                done: 'Pronto',
                weekdaysAbbrev: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S'],
                weekdaysShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
                weekdays: ['Domingo', 'Segunda-Feira', 'Terça-Feira', 'Quarta-Feira', 'Quinta-Feira', 'Sexta-Feira', 'Sábado'],
                monthsShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                months: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro']
            }
    });
      
});
</script>
      