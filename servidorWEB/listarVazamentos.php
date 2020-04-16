<?php
//Header
include_once 'includes/header.php';
?>

<title>Clientes</title>


<div class=row>
  <div class = "col s12 m6 push-m3">
    <h3 class="light">Vazamentos</h3>
    <table class="striped">
        <thead>
            <tr>
            <th>Número de Casos</th>
            <th>Vazamento Total Litros/Hora</th>
            <th>Gasto Total Reais/Hora</th>
            </tr>
        </thead>

        <tbody>
            <?php
            
                $sql= "SELECT COUNT(c.nome) AS numClientes, SUM(v.vazamentoPulso) AS vazamentoTotal, ROUND(SUM(v.vazamentoPulso)*t.preco, 2) AS vazamentoTotalReais FROM vazamentos v
                INNER JOIN clientes c
                ON c.id_cliente = v.id_cliente
                INNER JOIN regioes r
                ON r.id_regiao = c.id_regiao
                INNER JOIN tarifas t
                ON t.id_tarifa = c.id_tarifa";

                $stmt = $PDO->prepare($sql);
                $stmt->execute();
            
                $dados = $stmt->fetch(PDO::FETCH_OBJ);

                ?>
                <tr>
                <td><?php echo $dados->numClientes;?></td>
                <td><?php echo $dados->vazamentoTotal;?></td>
                <td><?php echo $dados->vazamentoTotalReais;?></td>
                </tr>
                </tbody>
                
    </table>
    <br><br>
    <table class="striped">
        <thead>
        <tr>
            <th>Cliente</th>
            <th>Região</th>
            <th>Vazamento Litros/Hora</th>
            <th>Vazamento Reais/Hora</th>
        </tr>
        </thead>                

        <tbody>
            <?php

            $sql= "SELECT c.nome AS cliente, r.nome AS regiao, v.vazamentoPulso, ROUND((v.vazamentoPulso*t.preco), 2) AS vazamentoReais FROM vazamentos v
            INNER JOIN clientes c
            ON c.id_cliente = v.id_cliente
            INNER JOIN regioes r
            ON r.id_regiao = c.id_regiao
            INNER JOIN tarifas t
            ON t.id_tarifa = c.id_tarifa
            ORDER BY v.vazamentoPulso
            DESC";

            $stmt = $PDO->prepare($sql);
            $stmt->execute();
        
            while ($dados = $stmt->fetch(PDO::FETCH_OBJ)):
            ?>
                <tr>
                <td><?php echo $dados->cliente;?></td>
                <td><?php echo $dados->regiao;?></td>
                <td><?php echo $dados->vazamentoPulso;?></td>
                <td><?php echo $dados->vazamentoReais;?></td>
                </tr>
            <?php endwhile;?>
        
      </tbody>
    </table>
            
    <br>
    <a href="cliente.php" class="btn">Relatório</a>
    <a href="index.php" class="btn green">Voltar</a>
  </div>
</div>


<?php
//Footer
include_once 'includes/footer.php';
?>


      