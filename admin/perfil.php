<?php

//perfil.php

include '../base_dados_con.php';

include '../funcoes.php';

if (!is_admin_entrada()) {
	header('location:../admin_entrada.php');
}

$message = '';

$error = '';

if (isset($_POST['edit_admin'])) {

	$formdata = array();

	if (empty($_POST['email'])) {
		$error .= '<li>Endereço de email é obrigatório!</li>';
	} else {
		if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
			$error .= '<li>Endereço de email inválido!</li>';
		} else {
			$formdata['email'] = $_POST['email'];
		}
	}

	if (empty($_POST['senha'])) {
		$error .= '<li>Palavra-passe inválida!</li>';
	} else {
		$formdata['senha'] = $_POST['senha'];
	}

	if ($error == '') {
		$admin_id = $_SESSION['admin_id'];

		$data = array(
			':email'		=>	$formdata['email'],
			':senha'	=>	$formdata['senha'],
			':id'			=>	$admin_id
		);

		$query = "
		UPDATE admin 
            SET email = :email,
            senha = :senha 
            WHERE id = :id
		";

		$statement = $connect->prepare($query);

		$statement->execute($data);

		$message = 'Dados do usuário actualizados!';
	}
}

$query = "
	SELECT * FROM admin 
    WHERE id = '" . $_SESSION["admin_id"] . "'
";

$result = $connect->query($query);


include '../cabecalho.php';

?>

<div class="container-fluid px-4">
	<h1 class="mt-4">Perfil</h1>
	<ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
		<li class="breadcrumb-item"><a href="index.php">Painel de Controle</a></li>
		<li class="breadcrumb-item active">Perfil</a></li>
	</ol>
	<div class="row">
		<div class="col-md-6">
			<?php

			if ($error != '') {
				echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">' . $error . '</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}

			if ($message != '') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . $message . ' <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}

			?>
			<div class="card mb-4">
				<div class="card-header">
					<i class="fas fa-user-edit"></i> Editar detalhes de Perfil
				</div>
				<div class="card-body">

					<?php

					foreach ($result as $row) {
					?>

						<form method="post">
							<div class="mb-3">
								<label class="form-label">Endereço de Email</label>
								<input type="text" name="email" id="email" class="form-control" value="<?php echo $row['email']; ?>" />
							</div>
							<div class="mb-3">
								<label class="form-label">Senha</label>
								<input type="senha" name="senha" id="senha" class="form-control" value="<?php echo $row['senha']; ?>" />
							</div>
							<div class="mt-4 mb-0">
								<input type="submit" name="edit_admin" class="btn btn-primary" value="Actualizar" />
							</div>
						</form>

					<?php
					}

					?>

				</div>
			</div>

		</div>
	</div>
</div>

<?php

include '../rodape.php';

?>