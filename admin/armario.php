<?php

//armario.php

include '../base_dados_con.php';

include '../funcoes.php';

if (!is_admin_entrada()) {
	header('location:../admin_entrada.php');
}

$message = '';

$error = '';

if (isset($_POST["add_armario"])) {
	$formdata = array();

	if (empty($_POST["nome"])) {
		$error .= '<li>Nome do Armário é obrigatório!</li>';
	} else {
		$formdata['nome'] = trim($_POST["nome"]);
	}

	if ($error == '') {
		$query = "
		SELECT * FROM armario 
        WHERE nome = '" . $formdata['nome'] . "'
		";

		$statement = $connect->prepare($query);

		$statement->execute();

		if ($statement->rowCount() > 0) {
			$error = '<li>Nome de Armário já existe!</li>';
		} else {
			$data = array(
				':nome'		=>	$formdata['nome'],
				':estado'		=>	'activado',
				':criado_em'	=>	get_data_temp($connect)
			);

			$query = "
			INSERT INTO armario 
            (nome, estado, criado_em) 
            VALUES (:nome, :estado, :criado_em)
			";

			$statement = $connect->prepare($query);

			$statement->execute($data);

			header('location:armario.php?msg=add');
		}
	}
}

if (isset($_POST["edit_armario"])) {
	$formdata = array();

	if (empty($_POST["nome"])) {
		$error .= '<li>Nome do Armário é obrigatório!</li>';
	} else {
		$formdata['nome'] = trim($_POST["nome"]);
	}

	if ($error == '') {
		$id = converter_dados($_POST["id"], 'decrypt');

		$query = "
		SELECT * FROM armario 
	        WHERE nome = '" . $formdata['nome'] . "' 
	        AND id != '" . $id . "'
		";

		$statement = $connect->prepare($query);

		$statement->execute();

		if ($statement->rowCount() > 0) {
			$error = '<li>Nome de Armário já existe!</li>';
		} else {
			$data = array(
				':nome'		=>	$formdata['nome'],
				':actualizado_em'	=>	get_data_temp($connect),
				':id'			=>	$id
			);

			$query = "
			UPDATE armario 
	            SET nome = :nome, 
	            actualizado_em = :actualizado_em  
	            WHERE id = :id
			";

			$statement = $connect->prepare($query);

			$statement->execute($data);

			header('location:armario.php?msg=edit');
		}
	}
}

if (isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'delete') {
	$id = $_GET["code"];

	$status = $_GET["status"];

	$data = array(
		':estado'			=>	$status,
		':actualizado_em'		=>	get_data_temp($connect),
		':id'				=>	$id
	);
	$query = "
	UPDATE armario 
    SET estado = :estado, 
    actualizado_em = :actualizado_em 
    WHERE id = :id
	";

	$statement = $connect->prepare($query);

	$statement->execute($data);

	header('location:armario.php?msg=' . strtolower($status) . '');
}


$query = "
	SELECT * FROM armario 
    ORDER BY nome ASC
";

$statement = $connect->prepare($query);

$statement->execute();

include '../cabecalho.php';

?>

<div class="container-fluid py-4" style="min-height: 700px;">
	<h1>Gestão de Armário</h1>
	<?php

	if (isset($_GET["action"])) {
		if ($_GET["action"] == 'add') {
	?>

			<ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
				<li class="breadcrumb-item"><a href="index.php">Painel de Controle</a></li>
				<li class="breadcrumb-item"><a href="categoria.php">Gestão de Armário</a></li>
				<li class="breadcrumb-item active">Adicionar Armário</li>
			</ol>

			<div class="row">
				<div class="col-md-6">
					<?php

					if ($error != '') {
						echo '
				<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">' . $error . '</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
				';
					}

					?>
					<div class="card mb-4">
						<div class="card-header">
							<i class="fas fa-user-plus"></i> Adicionar novo Armário
						</div>
						<div class="card-body">
							<form method="post">
								<div class="mb-3">
									<label class="form-label">Nome de Armário</label>
									<input type="text" name="nome" id="nome" class="form-control" />
								</div>
								<div class="mt-4 mb-0">
									<input type="submit" name="add_armario" class="btn btn-success" value="Adicionar" />
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>

			<?php
		} else if ($_GET["action"] == 'edit') {
			$id = converter_dados($_GET["code"], 'decrypt');

			if ($id > 0) {
				$query = "
				SELECT * FROM armario 
                WHERE id = '$id'
				";

				$armario_result = $connect->query($query);

				foreach ($armario_result as $armario_row) {
			?>

					<ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
						<li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="armario.php">Gestão de Armário</a></li>
						<li class="breadcrumb-item active">Alterar localização de Armário</li>
					</ol>
					<div class="row">
						<div class="col-md-6">
							<div class="card mb-4">
								<div class="card-header">
									<i class="fas fa-user-edit"></i> Alterar detalhes de Armário
								</div>
								<div class="card-body">
									<form method="post">
										<div class="mb-3">
											<label class="form-label">Nome de Armário</label>
											<input type="text" name="nome" id="nome" class="form-control" value="<?php echo $armario_row["nome"]; ?>" />
										</div>
										<div class="mt-4 mb-0">
											<input type="hidden" name="id" value="<?php echo $_GET['code']; ?>" />
											<input type="submit" name="edit_armario" class="btn btn-primary" value="Alterar" />
										</div>
									</form>
								</div>
							</div>

						</div>
					</div>

		<?php
				}
			}
		}
	} else {

		?>
		<ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
			<li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
			<li class="breadcrumb-item active">Gestão de Armário</li>
		</ol>
		<?php

		if (isset($_GET["msg"])) {
			if ($_GET["msg"] == 'add') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Novo Armário adicionado<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}

			if ($_GET["msg"] == 'edit') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Detalhes de Armário actualizado <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}

			if ($_GET["msg"] == 'desativado') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Estado de Armário desativado <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}

			if ($_GET["msg"] == 'activado') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Estado de Armário ativado <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
		}

		?>
		<div class="card mb-4">
			<div class="card-header">
				<div class="row">
					<div class="col col-md-6">
						<i class="fas fa-table me-1"></i> Gestão de Armário
					</div>
					<div class="col col-md-6" align="right">
						<a href="armario.php?action=add" class="btn btn-success btn-sm">Adicionar</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<table id="datatablesSimple">
					<thead>
						<tr>
							<th>Nome de Armário</th>
							<th>Estado</th>
							<th>Criado Em</th>
							<th>Actualizado Em</th>
							<th>Acção</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th>Nome de Armário</th>
							<th>Estado</th>
							<th>Criado Em</th>
							<th>Actualizado Em</th>
							<th>Acção</th>
						</tr>
					</tfoot>
					<tbody>
						<?php
						if ($statement->rowCount() > 0) {
							foreach ($statement->fetchAll() as $row) {
								$estado = '';
								if ($row['estado'] == 'activado') {
									$estado = '<div class="badge bg-success">Activado</div>';
								} else {
									$estado = '<div class="badge bg-danger">Disativado</div>';
								}
								$estadoString = $row['estado'] == 'activado' ? 'Desativar' : 'Activar';
								$estadoCor = $row['estado'] == 'activado' ? 'danger' : 'success';
								echo '
						<tr>
							<td>' . $row["nome"] . '</td>
							<td>' . $estado . '</td>
							<td>' . $row["criado_em"] . '</td>
							<td>' . $row["actualizado_em"] . '</td>
							<td>
								<a href="armario.php?action=edit&code=' . converter_dados($row["id"]) . '" class="btn btn-sm btn-primary">Alterar</a>
								<button type="button" name="delete_button" class="btn btn-' . $estadoCor . ' btn-sm" onclick="delete_data(`' . $row["id"] . '`, `' . $row["estado"] . '`)">' . $estadoString . '</button>
							</td>
						</tr>
						';
							}
						} else {
							echo '
					<tr>
						<td colspan="5" class="text-center">Nenhum Armário encontrado</td>
					</tr>
					';
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
		<script>
			function delete_data(code, estado) {
				var novo_estado = 'activado';

				if (estado == 'activado') {
					novo_estado = 'desativado';
				}
				var novo_estado_str = novo_estado === 'activado' ? 'activar' : 'desativar';

				if (confirm("Tem certeza que deseja " + novo_estado_str + " este Armário?")) {
					window.location.href = "armario.php?action=delete&code=" + code + "&status=" + novo_estado + ""
				}
			}
		</script>

	<?php

	}

	?>

</div>



<?php

include '../rodape.php';

?>