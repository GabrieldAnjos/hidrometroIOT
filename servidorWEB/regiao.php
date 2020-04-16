<?php
//Header
include_once 'includes/header.php';
?>  

<div class=row>
  <div class = "col s12 m6 push-m4">
    <h3 class="light">Região</h3>
      <form method="post" action="addRegiao.php">

        <div class="input-field col s12 m6 ">
          <label class="cat" for="nome">Nome</label>
          <input type="text" name="nome" id="nome" value="cataguases">
        </div>
        <div class="input-field col s12">
        <br>
        <button class="btn" type="submit" name="btn_salvar">Salvar</button>
        <a class="btn green" href="listarRegioes.php">Voltar</a>
      </form>
    
  </div>
</div>

<?php
//Footer
include_once 'includes/footer.php';
?>
