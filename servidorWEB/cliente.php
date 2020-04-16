<?php
//Header
include_once 'includes/header.php';

?>  

<div class=row>
  <div class = "col s12 m6 push-m3">
    <h3 class="light">Cliente</h3>
      <form method="post" action="addCliente.php">
        <div class="input-field col s1 m5">
          <label class="cat" for="nome">Nome</label>
          <input type="text" name="nome" id="nome" value="">
        </div>
        <div class="input-field col s1 m5">
          <label class="cat" for="cpf">CPF</label>
          <input type="text" name="cpf" id="cpf" value="">
        </div>

          <div class="input-field col s12 m10">
              <select name="id_regiao">
                  
                    <?php
                      
                      $sql= "SELECT DISTINCT * FROM `regioes` ORDER BY nome";

                      $stmt = $PDO->prepare($sql);
                      $stmt->execute();

                      while ($dados = $stmt->fetch(PDO::FETCH_OBJ)):
                    ?>
                    <option value="<?php echo $dados->id_regiao; ?>"><?php echo $dados->nome; ?></option>
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
                    <option value="<?php echo $dados->id_tarifa; ?>"><?php echo $dados->categoria; ?></option>
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
                  <option value="<?php echo $dados->id_hidrometro; ?>"><?php echo $dados->id_hidrometro; ?></option>
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
