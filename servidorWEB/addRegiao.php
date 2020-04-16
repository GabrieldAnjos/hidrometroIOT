<?php

require_once 'conexao.php';

session_start();

  if(isset($_POST['btn_salvar'])){
    $nome = $_POST['nome'];

    if(empty($nome)){
      $_SESSION['mensagem']="Favor preencher todos os campos";
      header('Location: regiao.php');
  
    }
    else{
    $sql = "INSERT INTO regioes (nome) VALUES (:nome) ";

    $stmt  = $PDO->prepare($sql);

    $stmt->bindParam(':nome', $nome);

    if($stmt->execute()){
      $_SESSION['mensagem']="Cadastrado com Sucesso";
        header('Location: listarRegioes.php');
        
    }else{
      $_SESSION['mensagem']="Erro ao cadastrar";
      header('Location: regiao.php');
  
    }
  }
  }

?>