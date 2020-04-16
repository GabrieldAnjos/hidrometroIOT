<?php
//Header
include_once 'includes/header.php';
?>

<?php
$id_regiao = $_GET['id_regiao'];

$sql = "SELECT * FROM regioes WHERE id_regiao = :id_regiao";

$stmt = $PDO->prepare($sql);
$stmt->bindParam(':id_regiao', $id_regiao);
$stmt->execute();

$dados = $stmt->fetch(PDO::FETCH_OBJ);

$nomeRegiao = $dados->nome;
?>


<div class=row>
    <div class="col s12 m6 push-m4">
        <h3 class="light">Região</h3>
        <form method="post" action="updateRegiao.php">
        <input type="hidden" name="id_regiao" id="id_regiao" value="<?php echo $id_regiao ?>">
            <div class="input-field col s12 m6 ">
                <label class="cat" for="nome">Nome</label>
                <input type="text" name="nome" id="nome" value="<?php echo $nomeRegiao ?>">
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
<script>
    $(document).ready(function() {
        $('select').formSelect();
    });
</script>