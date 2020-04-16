<?php

require_once 'conexao.php';

session_start();

  if(isset($_POST['btn_salvar'])){
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $id_regiao = $_POST['id_regiao'];
    $id_tarifa = $_POST['id_tarifa'];
    $id_hidrometro = $_POST['id_hidrometro'];

    if(empty($nome)||(empty($cpf))){
      $_SESSION['mensagem']="Favor preencher todos os campos";
      header('Location: cliente.php');
  
    }
    else{
    $sql = "INSERT INTO clientes (nome, cpf, id_regiao, id_tarifa, id_hidrometro) VALUES (:nome, :cpf, :id_regiao, :id_tarifa, :id_hidrometro) ";

    $stmt  = $PDO->prepare($sql);

    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':cpf', $cpf);
    $stmt->bindParam(':id_regiao', $id_regiao);
    $stmt->bindParam(':id_tarifa', $id_tarifa);
    $stmt->bindParam(':id_hidrometro', $id_hidrometro);

    if($stmt->execute()){
      $_SESSION['mensagem']="Cadastrado com Sucesso";        
    }else{
      $_SESSION['mensagem']="Erro ao cadastrar";
    }
    header('Location: listarClientes.php');
  }
  }

?>