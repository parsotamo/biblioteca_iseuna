<?php

//usuario_entrada.php

include 'base_dados_con.php';

include 'funcoes.php';

if (is_usuario_entrada()) {
	header('location:requisitar_livro_detalhes.php');
}

$mensagem = '';

if (isset($_POST["login_btn"])) {
	$formdata = array();

	if (empty($_POST["email"])) {
		$mensagem .= '<li>Endereço de email é obrigatório</li>';
	} else {
		if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
			$mensagem .= '<li>Endereço de Email Inválido!</li>';
		} else {
			$formdata['email'] = trim($_POST['email']);
		}
	}

	if (empty($_POST['senha'])) {
		$mensagem .= '<li>Palavra-passe é obrigatório</li>';
	} else {
		$formdata['senha'] = trim($_POST['senha']);
	}

	if ($mensagem == '') {
		$data = array(
			':email'		=>	$formdata['email']
		);

		$query = "
		SELECT * FROM usuario 
        WHERE email = :email
		";

		$statement = $connect->prepare($query);

		$statement->execute($data);

		if ($statement->rowCount() > 0) {
			foreach ($statement->fetchAll() as $row) {
				if ($row['estado'] == 'activado') {
					if ($row['senha'] == $formdata['senha']) {
						$_SESSION['id_usuario'] = $row['unique_id'];
						header('location:requisitar_livro_detalhes.php');
					} else {
						$mensagem = '<li>Palavra-passe incorrecta</li>';
					}
				} else {
					$mensagem = '<li>A sua conta foi desactivada</li>';
				}
			}
		} else {
			$mensagem = '<li>Endereço de email incorrecto</li>';
		}
	}
}

include 'cabecalho.php';

?>

<div class="d-flex align-items-center justify-content-center" style="height:700px;">
	<div class="col-md-6">
		<?php

		if ($mensagem != '') {
			echo '<div class="alert alert-danger"><ul>' . $mensagem . '</ul></div>';
		}

		?>
		<div class="card">
			<div class="card-header">Entrar no Sistema</div>
			<div class="card-body">
				<form method="POST">
					<div class="mb-3">
						<label class="form-label">Endereço de email</label>
						<input type="text" name="email" id="email" class="form-control" />
					</div>
					<div class="mb-3">
						<label class="form-label">Senha</label>
						<input type="senha" name="senha" id="senha" class="form-control" />
					</div>
					<div class="d-flex align-items-center justify-content-between mt-4 mb-0">
						<input type="submit" name="login_btn" class="btn btn-danger" value="Entrar" />
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include 'rodape.php';

?>