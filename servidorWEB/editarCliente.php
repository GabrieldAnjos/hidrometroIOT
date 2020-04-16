<?php
//Header
include_once 'includes/header.php';
?>  

<?php
    $id_cliente = $_GET['id_cliente'];

    $sql= "SELECT * FROM clientes WHERE id_cliente = :id_cliente";
    
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':id_cliente', $id_cliente);
    $stmt->execute();  

    $dados = $stmt->fetch(PDO::FETCH_OBJ); 

    $nomeCliente = $dados->nome;
    $cpfCliente = $dados->cpf; 
    $id_regiao = $dados->id_regiao;
    $id_tarifa = $dados->id_tarifa;
    $id_hidrometro = $dados->id_hidrometro;
?>                      
                      

<div class=row>
  <div class = "col s12 m6 push-m3">
    <h3 class="light">Cliente</h3>
      <form method="post" action="updateCliente.php">
      <input type="hidden" name="id_cliente" id="id_cliente" value="<?php echo $id_cliente?>">
        <div class="input-field col s1 m5">
          <label class="cat" for="nome">Nome</label>
          <input type="text" name="nome" id="nome" value="<?php echo $nomeCliente?>">
        </div>
        <div class="input-field col s1 m5">
          <label class="cat" for="cpf">CPF</label>
          <input type="text" name="cpf" id="cpf" value="<?php echo $cpfCliente?>">
        </div>

          <div class="input-field col s12 m10">
              <select name="id_regiao">
                  
                    
                    <?php  
                      $sql= "SELECT DISTINCT * FROM `regioes` ORDER BY nome";

                      $stmt = $PDO->prepare($sql);
                      $stmt->execute();

                      while ($dados = $stmt->fetch(PDO::FETCH_OBJ)):                        
                    ?>
                   <option value="<?php echo $dados->id_regiao; ?>" <?php if($id_regiao == $dados->id_regiao) echo " selected"; ?>><?php echo $dados->nome; ?></option>
                    <?php
                      endwhile; 
                    ?>
              </select>
              <label class="cat">Região</label>
          </div>

          <div class="input-field col s12 m10">
              <select name="id_tarifa">
                  
                    <?php
                      
                      $sql= "SELECT DISTINCT * FROM `tarifas` ORDER BY categoria";

                      $stmt = $PDO->prepare($sql);
                      $stmt->execute();

                      while ($dados = $stmt->fetch(PDO::FETCH_OBJ)):
                    ?>
                    <option value="<?php echo $dados->id_tarifa; ?>" <?php if($id_tarifa == $dados->id_tarifa) echo " selected"; ?>><?php echo $dados->categoria; ?></option>
                    <?php
                      endwhile; 
                    ?>
              </select>
              <label class="cat">Categoria</label>
          </div>      
          
        <div class="input-field col s12 m10">
        <select name="id_hidrometro">
                  
                  <?php
                    
                    $sql= "SELECT DISTINCT id_hidrometro FROM `leituras` order by id_hidrometro";

                    $stmt = $PDO->prepare($sql);
                    $stmt->execute();

                    while ($dados = $stmt->fetch(PDO::FETCH_OBJ)):                        
                            ?>
                            <option value="<?php echo $dados->id_hidrometro; ?>" <?php if($id_hidrometro == $dados->id_hidrometro) echo " selected"; ?>><?php echo $dados->id_hidrometro; ?></option>
                            <?php                                    
                    endwhile; 
                  ?>
            </select>
          <label class="cat">Hidrômetro</label>
        </div>
        
        
        <div class="input-field col s12">
        <br>
        <button class="btn" type="submit" name="btn_salvar">Salvar</button>
        <a class="btn green" href="listarClientes.php">Voltar</a>
      </form>
    
  </div>
</div>

<?php
//Footer
include_once 'includes/footer.php';
?>
<script>
  $(document).ready(function(){
    $('select').formSelect();
  });

</script>
