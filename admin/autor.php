<?php

//autor.php

include '../base_dados_con.php';

include '../funcoes.php';

if (!is_admin_entrada()) {
	header('location:../admin_entrada.php');
}

$message = '';

$error = '';

if (isset($_POST["adicionar_autor"])) {
	$formdata = array();

	if (empty($_POST["nome"])) {
		$error .= '<li>Nome de Autor é obrigatório!</li>';
	} else {
		$formdata['nome'] = trim($_POST["nome"]);
	}

	if ($error == '') {
		$query = "
		SELECT * FROM autor 
        WHERE nome = '" . $formdata['nome'] . "'
		";

		$statement = $connect->prepare($query);

		$statement->execute();

		if ($statement->rowCount() > 0) {
			$error = '<li>Nome de Autor já existe!</li>';
		} else {
			$data = array(
				':nome'			=>	$formdata['nome'],
				':estado'		=>	'activado',
				':criado_em'	=>	get_data_temp($connect)
			);

			$query = "
			INSERT INTO autor 
            (nome, estado, criado_em) 
            VALUES (:nome, :estado, :criado_em)
			";

			$statement = $connect->prepare($query);

			$statement->execute($data);

			header('location:autor.php?msg=adicionar');
		}
	}
}

if (isset($_POST["editar_autor"])) {
	$formdata = array();

	if (empty($_POST["nome"])) {
		$error .= '<li>Nome de Autor é obrigatório!</li>';
	} else {
		$formdata['nome'] = trim($_POST['nome']);
	}

	if ($error == '') {
		$id = converter_dados($_POST['id'], 'decrypt');

		$query = "
		SELECT * FROM autor 
        WHERE nome = '" . $formdata['nome'] . "' 
        AND id != '" . $id . "'
		";

		$statement = $connect->prepare($query);

		$statement->execute();

		if ($statement->rowCount() > 0) {
			$error = '<li>Nome de Autor já existe!</li>';
		} else {
			$data = array(
				':nome'		=>	$formdata['nome'],
				':actualizado_em' =>	get_data_temp($connect),
				':id'		=>	$id
			);

			$query = "
			UPDATE autor 
            SET nome = :nome, 
            actualizado_em = :actualizado_em  
            WHERE id = :id
			";

			$statement = $connect->prepare($query);

			$statement->execute($data);

			header('location:autor.php?msg=editar');
		}
	}
}

if (isset($_GET["accao"], $_GET["codigo"], $_GET["estado"]) && $_GET["accao"] == "apagar") {
	$id = $_GET["codigo"];

	$status = $_GET["estado"];

	$data = array(
		':estado'			=>	$status,
		':actualizado_em'		=>	get_data_temp($connect),
		':id'				=>	$id
	);

	$query = "
	 UPDATE autor 
    SET estado = :estado, 
    actualizado_em = :actualizado_em 
    WHERE id = :id
	";

	$statement = $connect->prepare($query);

	$statement->execute($data);

	header('location:autor.php?msg=' . strtolower($status) . '');
}


$query = "
	SELECT * FROM autor 
    ORDER BY nome ASC
";

$statement = $connect->prepare($query);

$statement->execute();

include '../cabecalho.php';

?>

<div class="container-fluid py-4" style="min-height: 700px;">
	<h1>Gestão de Autores</h1>
	<?php

	if (isset($_GET["accao"])) {
		if ($_GET["accao"] == "adicionar") {
	?>

			<ol class="breadcrumb mt-4 mb-4 bg-color p-2 border">
				<li class="breadcrumb-item"><a href="index.php">Painel de Controle</a></li>
				<li class="breadcrumb-item"><a href="autor.php">Gestão de Autores</a></li>
				<li class="breadcrumb-item active">Adicionar Autor</li>
			</ol>

			<div class="row">
				<div class="col-md-6">
					<?php

					if ($error != '') {
						echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">' . $error . '</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
					}

					?>
					<div class="card mb-4">
						<div class="card-header">
							<i class="fas fa-user-plus"></i> Adicionar novo autor
						</div>
						<div class="card-body">
							<form method="post">
								<div class="mb-3">
									<label class="form-label">Nome do Autor</label>
									<input type="text" name="nome" id="nome" class="form-control" />
								</div>
								<div class="mt-4 mb-0">
									<input type="submit" name="adicionar_autor" class="btn btn-success" value="Add" />
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>

			<?php
		} else if ($_GET["accao"] == 'editar') {
			$id = converter_dados($_GET["codigo"], 'decrypt');

			if ($id > 0) {
				$query = "
				SELECT * FROM autor 
                WHERE id = '$id'
				";

				$author_result = $connect->query($query);

				foreach ($author_result as $author_row) {
			?>

					<ol class="breadcrumb mt-4 mb-4 bg-color p-2 border">
						<li class="breadcrumb-item"><a href="index.php">Painel de Controle</a></li>
						<li class="breadcrumb-item"><a href="autor.php">Gestão de Autores</a></li>
						<li class="breadcrumb-item active">Alterar dados de Autor</li>
					</ol>

					<div class="row">
						<div class="col-md-6">
							<div class="card mb-4">
								<div class="card-header">
									<i class="fas fa-user-edit"></i> Alterar detalhes de Autor
								</div>
								<div class="card-body">
									<form method="post">
										<div class="mb-3">
											<label class="form-label">Nome do Autor</label>
											<input type="text" name="nome" id="nome" class="form-control" value="<?php echo $author_row['nome']; ?>" />
										</div>
										<div class="mt-4 mb-0">
											<input type="hidden" name="id" value="<?php echo $_GET['codigo']; ?>" />
											<input type="submit" name="editar_autor" class="btn btn-danger" value="Actualizar" />
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
		<ol class="breadcrumb mt-4 mb-4 bg-color p-2 border">
			<li class="breadcrumb-item"><a href="index.php">Painel de Controle</a></li>
			<li class="breadcrumb-item active">Gestão de Autores</li>
		</ol>
		<?php

		if (isset($_GET["msg"])) {
			if ($_GET["msg"] == 'adicionar') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Novo Autor Adicionado<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
			if ($_GET['msg'] == 'editar') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Dados do Autor Actualizados<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
			if ($_GET["msg"] == 'desativado') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Estado de Autor alterado para desativado <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}

			if ($_GET["msg"] == 'activado') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Estado do Autor alterado para Activado <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
		}

		?>
		<div class="card mb-4">
			<div class="card-header">
				<div class="row">
					<div class="col col-md-6">
						<i class="fas fa-table me-1"></i> Gestão de Autores
					</div>
					<div class="col col-md-6" align="right">
						<a href="autor.php?accao=adicionar" class="btn btn-success btn-sm">Adicionar</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<table id="datatablesSimple">
					<thead>
						<tr>
							<th>Nome do Autor</th>
							<th>Estado</th>
							<th>Criado Em</th>
							<th>Actualizado Em</th>
							<th>Acção</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th>Nome do Autor</th>
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
								<a href="autor.php?accao=editar&codigo=' . converter_dados($row["id"]) . '" class="btn btn-sm btn-warning">Alterar</a>
								<button type="button" name="delete_button" class="btn btn-' . $estadoCor . ' btn-sm" onclick="apagar_dados(`' . $row["id"] . '`, `' . $row["estado"] . '`)">' . $estadoString . '</button>
							</td>
						</tr>
						';
							}
						} else {
							echo '
					<tr>
						<td colspan="4" class="text-center">Nenhum Autor encontrado!</td>
					</tr>
					';
						}
						?>
					</tbody>
				</table>
			</div>
		</div>

		<script>
			function apagar_dados(code, estado) {
				var novo_estado = 'activado';

				if (estado == 'activado') {
					novo_estado = 'desativado';
				}
				var novo_estado_str = novo_estado === 'activado' ? 'activar' : 'desativar';

				if (confirm("Tem certeza que deseja " + novo_estado_str + " este autor?")) {
					window.location.href = "autor.php?accao=apagar&codigo=" + code + "&estado=" + novo_estado + "";
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