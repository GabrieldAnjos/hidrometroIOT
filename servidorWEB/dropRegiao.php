<?php

require_once 'conexao.php';

session_start();

?>  

<?php
  if(isset($_POST['btn-deletar'])){
    $id_regiao = $_POST['id_regiao'];

    $sql= "DELETE FROM regioes WHERE id_regiao = :id_regiao";

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':id_regiao', $id_regiao);
    
    if($stmt->execute()){
      $_SESSION['mensagem']="Excluído com Sucesso";
        
    }else{
      $_SESSION['mensagem']="Erro ao Excluir";  
    }
    
    header('Location: listarRegioes.php');
  }

?>