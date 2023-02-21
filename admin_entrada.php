<?php

//admin_entrada.php
include 'base_dados_con.php';

include 'funcoes.php';


$message = '';

if (isset($_POST["login_btn"])) {

	$formdata = array();

	if (empty($_POST["email"])) {
		$message .= '<li>Endereço de email é obrigatório!</li>';
	} else {
		if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
			$message .= '<li>Endereço de email inválido!</li>';
		} else {
			$formdata['email'] = $_POST['email'];
		}
	}

	if (empty($_POST['senha'])) {
		$message .= '<li>Palavra-passe é obrigatório.</li>';
	} else {
		$formdata['senha'] = $_POST['senha'];
	}

	if ($message == '') {
		$data = array(
			':email'		=>	$formdata['email']
		);

		$query = "
		SELECT * FROM admin 
        WHERE email = :email
		";

		$statement = $connect->prepare($query);

		$statement->execute($data);

		if ($statement->rowCount() > 0) {
			foreach ($statement->fetchAll() as $row) {
				if ($row['senha'] == $formdata['senha']) {
					$_SESSION['admin_id'] = $row['id'];
					header('location:admin/index.php');
				} else {
					$message = '<li>Senha incorrecta!</li>';
				}
			}
		} else {
			$message = '<li>Endereço de email incorrecto</li>';
		}
	}
}

include 'cabecalho.php';

?>

<div class="d-flex align-items-center justify-content-center" style="min-height:700px;">

	<div class="col-md-6">

		<?php
		if ($message != '') {
			echo '<div class="alert alert-danger"><ul>' . $message . '</ul></div>';
		}
		?>

		<div class="card">

			<div class="card-header">Entrada de Admin</div>

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

						<input type="submit" name="login_btn" class="btn btn-primary" value="Entrar" />

					</div>

				</form>

			</div>

		</div>

	</div>

</div>

<?php

include 'rodape.php';

?>