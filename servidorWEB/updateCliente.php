<?php

require_once 'conexao.php';

session_start();

  if(isset($_POST['btn_salvar'])){
    $id_cliente = $_POST['id_cliente'];
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $id_regiao = $_POST['id_regiao'];
    $id_tarifa = $_POST['id_tarifa'];
    $id_hidrometro = $_POST['id_hidrometro'];

    if(empty($nome)||empty($cpf)){
      $_SESSION['mensagem']="Favor preencher todos os campos";
      header('Location: editarCliente.php?id_cliente='.$id_cliente);
  
    }
    else{
    
    $sql = "UPDATE clientes SET nome=:nome, cpf=:cpf, id_regiao=:id_regiao, id_tarifa=:id_tarifa, id_hidrometro=:id_hidrometro WHERE id_cliente=:id_cliente";

    $stmt  = $PDO->prepare($sql);
    
    $stmt->bindParam(':id_cliente', $id_cliente);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':cpf', $cpf);
    $stmt->bindParam(':id_regiao', $id_regiao);
    $stmt->bindParam(':id_tarifa', $id_tarifa);
    $stmt->bindParam(':id_hidrometro', $id_hidrometro);

    if($stmt->execute()){
      $_SESSION['mensagem']="Alterado com Sucesso";
        
    }else{
      $_SESSION['mensagem']="Erro ao Alterar";  
    }
    header('Location: listarClientes.php');
  }
  }

?>