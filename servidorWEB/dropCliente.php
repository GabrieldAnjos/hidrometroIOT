<?php

require_once 'conexao.php';

session_start();

?>  

<?php
  if(isset($_POST['btn-deletar'])){
    $id_cliente = $_POST['id_cliente'];


    $sql= "DELETE FROM clientes WHERE id_cliente = :id_cliente";

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':id_cliente', $id_cliente);
    
    if($stmt->execute()){
      $_SESSION['mensagem']="Excluído com Sucesso";
        
    }else{
      $_SESSION['mensagem']="Erro ao Excluir";  
    }
    
    header('Location: listarClientes.php');
  }

?>