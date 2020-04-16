<?php
//Header
include_once 'includes/header.php';
?>

<title>Clientes</title>

<div class=row>
  <div class="col s12 m6 push-m3">
    <h3 class="light">Clientes</h3>
    <table class="striped">
      <thead>
        <tr>
          <th>Nome</th>
          <th>Consumo Total(L)</th>
          <th>Total Gasto($)</th>
          <th>Região</th>
          <th>Categoria</th>
        </tr>
      </thead>

      <tbody>
        <?php

        $sql = "SELECT c.nome, SUM(l.consumoPulso) AS consumoTotal, ROUND(SUM(l.consumoPulso)*t.preco, 2) AS consumoTotalReais, r.nome AS regiao, c.id_cliente, t.categoria FROM leituras l
          INNER JOIN clientes c
          ON c.id_hidrometro = l.id_hidrometro
          LEFT JOIN regioes r
          ON r.id_regiao = c.id_regiao
          INNER JOIN tarifas t
          ON t.id_tarifa = c.id_tarifa
          GROUP BY c.cpf
          ORDER BY c.nome";

        $stmt = $PDO->prepare($sql);
        $stmt->execute();

        while ($dados = $stmt->fetch(PDO::FETCH_OBJ)) :
        ?>
          <tr>
            <td><?php echo $dados->nome; ?></td>
            <td><?php echo $dados->consumoTotal; ?></td>
            <td><?php echo $dados->consumoTotalReais; ?></td>
            <td><?php echo $dados->regiao; ?></td>
            <td><?php echo $dados->categoria; ?></td>

            <td><a href="historicoCliente.php?id_cliente=<?php echo $dados->id_cliente; ?>" class="btn-floating blue"><i class="material-icons">search</i></a></td>
            <td><a href="editarCliente.php?id_cliente=<?php echo $dados->id_cliente; ?>" class="btn-floating orange"><i class="material-icons">edit</i></a></td>
            <td><a href="#modal<?php echo $dados->id_cliente;?>" class="btn-floating red modal-trigger"><i class="material-icons">delete</i></a></td>


            <!-- Modal Structure -->
            <div id="modal<?php echo $dados->id_cliente; ?>" class="modal">
              <div class="modal-content">
                <h4>Opa!</h4>
                <h4>Deseja realmente Excluir esse Cliente?</h4>
              </div>
              <div class="modal-footer">
                <form method="post" action="dropCliente.php">
                  <input type="hidden" name="id_cliente" value="<?php echo $dados->id_cliente;?>">
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



    <a href="cliente.php" class="btn">Adicionar</a>
    <a href="index.php" class="btn green">Menu</a>
  </div>
</div>


<?php
//Footer
include_once 'includes/footer.php';
?>
<script>
  $(document).ready(function() {
    $('.modal').modal();
  });
</script>