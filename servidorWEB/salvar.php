<?php

include('conexao.php');

$id_hidrometro = $_GET['id_hidrometro'];
$consumoPulso = $_GET['consumoPulso'];

$sql = "INSERT INTO leituras (id_hidrometro, consumoPulso) VALUES (:id_hidrometro, :consumoPulso) ";

$stmt  = $PDO->prepare($sql);

$stmt->bindParam(':id_hidrometro', $id_hidrometro);
$stmt->bindParam(':consumoPulso', $consumoPulso);


if($stmt->execute()){
    echo "salvo_com_sucesso";
}else{
    echo "erro ao salvar";
}

?>