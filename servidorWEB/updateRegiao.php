<?php

require_once 'conexao.php';

session_start();

  if(isset($_POST['btn_salvar'])){
    $id_regiao = $_POST['id_regiao'];
    $nome = $_POST['nome'];
    
    if(empty($nome)){
      $_SESSION['mensagem']="Favor preencher todos os campos";
      header('Location: editarRegiao.php?id_regiao='.$id_regiao);
    }
    else{
    
    $sql = "UPDATE regioes SET nome=:nome WHERE id_regiao=:id_regiao";

    $stmt  = $PDO->prepare($sql);
    
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':id_regiao', $id_regiao);

    if($stmt->execute()){
      $_SESSION['mensagem']="Alterado com Sucesso";
        
    }else{
      $_SESSION['mensagem']="Erro ao Alterar";  
    }
    header('Location: listarRegioes.php');
  }
  }

?>