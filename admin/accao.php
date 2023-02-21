<?php

//accao.php

include '../base_dados_con.php';

if (isset($_POST["action"])) {
	if ($_POST["action"] == 'procurar_livro_isbn') {
		$query = "
		SELECT numero_isbn, nome FROM livro 
		WHERE numero_isbn LIKE '%" . $_POST["request"] . "%' 
		AND estado = 'activado'
		";

		$result = $connect->query($query);

		$data = array();

		foreach ($result as $row) {
			$data[] = array(
				'numero_isbn'		=>	str_replace($_POST["request"], '<b>' . $_POST["request"] . '</b>', $row["numero_isbn"]),
				'nome_livro'		=>	$row['nome']
			);
		}
		echo json_encode($data);
	}

	if ($_POST["action"] == 'procurar_id_usuario') {
		$query = "
		SELECT unique_id, nome FROM usuario 
		WHERE unique_id LIKE '%" . $_POST["request"] . "%' 
		AND estado = 'activado'
		";

		$result = $connect->query($query);

		$data = array();

		foreach ($result as $row) {
			$data[] = array(
				'unique_id'	=>	str_replace($_POST["request"], '<b>' . $_POST["request"] . '</b>', $row["unique_id"]),
				'nome'			=>	$row["nome"]
			);
		}

		echo json_encode($data);
	}
}
