<?php

//verificacao.php

include 'base_dados_con.php';

include 'funcoes.php';

include 'cabecalho.php';

if (isset($_GET['codigo'])) {
	$data = array(
		':codigo_verificacao'		=>	trim($_GET['codigo'])
	);

	$query = "
	SELECT estado_verificacao FROM usuario 
	WHERE codigo_verificacao = :codigo_verificacao
	";

	$statement = $connect->prepare($query);

	$statement->execute($data);

	if ($statement->rowCount() > 0) {
		foreach ($statement->fetchAll() as $row) {
			if ($row['estado_verificacao'] == 'nao') {
				$data = array(
					':estado_verificacao'		=>	'sim',
					':codigo_verificacao'		=>	trim($_GET['codigo'])
				);

				$query = "
				UPDATE usuario 
				SET estado_verificacao = :estado_verificacao 
				WHERE codigo_verificacao = :codigo_verificacao
				";

				$statement = $connect->prepare($query);

				$statement->execute($data);

				echo '<div class="alert alert-success">Seu email foi verificado com sucesso, agora pode aceder ao link <a href="usuario_entrada.php">login</a> into system.</div>';
			} else {
				echo '<div class="alert alert-info">Seu email já foi verificado!</div>';
			}
		}
	} else {
		echo '<div class="alert alert-danger">Inválido URL</div>';
	}
}

include 'rodape.php';
