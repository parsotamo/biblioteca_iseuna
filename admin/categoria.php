<?php

//categoria.php

include '../base_dados_con.php';

include '../funcoes.php';

if (!is_admin_entrada()) {
	header('location:../admin_entrada.php');
}

$message = '';

$error = '';

if (isset($_POST['add_category'])) {
	$formdata = array();

	if (empty($_POST['nome'])) {
		$error .= '<li>Nome de Categoria é obrigatório!</li>';
	} else {
		$formdata['nome'] = trim($_POST['nome']);
	}

	if ($error == '') {
		$query = "
		SELECT * FROM categoria 
        WHERE nome = '" . $formdata['nome'] . "'
		";

		$statement = $connect->prepare($query);

		$statement->execute();

		if ($statement->rowCount() > 0) {
			$error = '<li>Nome de Categoria já existe!</li>';
		} else {
			$data = array(
				':nome'			=>	$formdata['nome'],
				':estado'			=>	'activado',
				':criado_em'		=>	get_data_temp($connect)
			);

			$query = "
			INSERT INTO categoria 
            (nome, estado, criado_em) 
            VALUES (:nome, :estado, :criado_em)
			";

			$statement = $connect->prepare($query);

			$statement->execute($data);

			header('location:categoria.php?msg=add');
		}
	}
}

if (isset($_POST["edit_category"])) {
	$formdata = array();

	if (empty($_POST["nome"])) {
		$error .= '<li>Nome de categoria é obrigatório</li>';
	} else {
		$formdata['nome'] = $_POST['nome'];
	}

	if ($error == '') {
		$id = converter_dados($_POST['id'], 'decrypt');

		$query = "
		SELECT * FROM categoria 
        WHERE nome = '" . $formdata['nome'] . "' 
        AND id != '" . $id . "'
		";

		$statement = $connect->prepare($query);

		$statement->execute();

		if ($statement->rowCount() > 0) {
			$error = '<li>Nome de Categoria já existe</li>';
		} else {
			$data = array(
				':nome'		=>	$formdata['nome'],
				':actualizado_em'	=>	get_data_temp($connect),
				':id'			=>	$id
			);

			$query = "
			UPDATE categoria 
            SET nome = :nome, 
            actualizado_em = :actualizado_em  
            WHERE id = :id
			";

			$statement = $connect->prepare($query);

			$statement->execute($data);

			header('location:categoria.php?msg=edit');
		}
	}
}

if (isset($_GET["accao"], $_GET["codigo"], $_GET["Estado"]) && $_GET["accao"] == "apagar") {
	$id = $_GET["codigo"];
	$Estado = $_GET["Estado"];
	$data = array(
		':estado'			=>	$Estado,
		':actualizado_em'		=>	get_data_temp($connect),
		':id'				=>	$id
	);
	$query = "
	UPDATE categoria 
    SET estado = :estado, 
    actualizado_em = :actualizado_em 
    WHERE id = :id
	";

	$statement = $connect->prepare($query);

	$statement->execute($data);

	header('location:categoria.php?msg=' . strtolower($Estado) . '');
}


$query = "
SELECT * FROM categoria 
    ORDER BY nome ASC
";

$statement = $connect->prepare($query);

$statement->execute();

include '../cabecalho.php';

?>

<div class="container-fluid py-4" style="min-height: 700px;">
	<h1>Gestão de Categoria</h1>
	<?php

	if (isset($_GET['action'])) {
		if ($_GET['action'] == 'adicionar') {
	?>

			<ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
				<li class="breadcrumb-item"><a href="index.php">Painel de Controle</a></li>
				<li class="breadcrumb-item"><a href="categoria.php">Gestão de Categoria</a></li>
				<li class="breadcrumb-item active">Adicionar Categoria</li>
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
							<i class="fas fa-user-plus"></i> Adicionar nova categoria
						</div>
						<div class="card-body">

							<form method="POST">

								<div class="mb-3">
									<label class="form-label">Nome de Categoria</label>
									<input type="text" name="nome" id="nome" class="form-control" />
								</div>

								<div class="mt-4 mb-0">
									<input type="submit" name="add_category" value="Adicionar" class="btn btn-success" />
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
				SELECT * FROM categoria 
                WHERE id = '$id'
				";

				$category_result = $connect->query($query);

				foreach ($category_result as $category_row) {
			?>

					<ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
						<li class="breadcrumb-item"><a href="index.php">Painel de Controle</a></li>
						<li class="breadcrumb-item"><a href="categoria.php">Gestão de Categorias</a></li>
						<li class="breadcrumb-item active">Editar Categoria</li>
					</ol>
					<div class="row">
						<div class="col-md-6">
							<div class="card mb-4">
								<div class="card-header">
									<i class="fas fa-user-edit"></i> Editar Detalhes de Categoria
								</div>
								<div class="card-body">

									<form method="post">

										<div class="mb-3">
											<label class="form-label">Nome de Categoria</label>
											<input type="text" name="nome" id="nome" class="form-control" value="<?php echo $category_row['nome']; ?>" />
										</div>

										<div class="mt-4 mb-0">
											<input type="hidden" name="id" value="<?php echo $_GET['code']; ?>" />
											<input type="submit" name="edit_category" class="btn btn-primary" value="Modificar" />
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
			<li class="breadcrumb-item"><a href="index.php">Painel de Controle</a></li>
			<li class="breadcrumb-item active">Gestão de Categoria</li>
		</ol>

		<?php

		if (isset($_GET['msg'])) {
			if ($_GET['msg'] == 'adicionar') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Nova Categoria Adicionada<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}

			if ($_GET["msg"] == 'editar') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Dados de Categoria Actualizados<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
			if ($_GET["msg"] == 'disable') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Category Estado alterado para Desativado <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}

			if ($_GET['msg'] == 'enable') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Estado alterado para Activado <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
		}

		?>

		<div class="card mb-4">
			<div class="card-header">
				<div class="row">
					<div class="col col-md-6">
						<i class="fas fa-table me-1"></i> Gestão de Categoria
					</div>
					<div class="col col-md-6" align="right">
						<a href="categoria.php?accao=add" class="btn btn-success btn-sm">Adicionar</a>
					</div>
				</div>
			</div>
			<div class="card-body">

				<table id="datatablesSimple">
					<thead>
						<tr>
							<th>Nome de Categoria</th>
							<th>Estado</th>
							<th>Criado em</th>
							<th>Actualizado em</th>
							<th>Acção</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th>Nome de Categoria</th>
							<th>Estado</th>
							<th>Criado em</th>
							<th>Actualizado em</th>
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
								$string_estado = $row["estado"]   ==  "activado" ? "Desativar" : "Activar";
								$cor_estado = $string_estado == "Activar" ? "success" : "danger";
								echo '
						<tr>
							<td>' . $row["nome"] . '</td>
							<td>' . $estado . '</td>
							<td>' . $row["criado_em"] . '</td>
							<td>' . $row["actualizado_em"] . '</td>
							<td>
								<a href="categoria.php?accao=edit&codigo=' . converter_dados($row["id"]) . '" class="btn btn-sm btn-primary">Alterar</a>
								<button name="delete_button" class="btn-' . $cor_estado . ' btn-sm" onclick="apagar_dados(`' . $row["id"] . '`, `' . $row["estado"] . '`)"> ' . $string_estado . ' </button>
							</td>
						</tr>
						';
							}
						} else {
							echo '
					<tr>
						<td colspan="4" class="text-center">Nenhma categoria encontrada!</td>
					</tr>
					';
						}

						?>
					</tbody>
				</table>

				<script>
					function apagar_dados(code, Estado) {
						var novo_Estado = 'desativado';

						if (Estado == 'desativado') {
							novo_Estado = 'activado';
						}

						if (confirm("Tem certeza que deseja " + novo_Estado + " esta Categoria")) {
							window.location.href = "categoria.php?accao=apagar&codigo=" + code + "&Estado=" + novo_Estado + "";
						}
					}
				</script>

			</div>
		</div>
	<?php
	}
	?>

</div>

<?php

include '../rodape.php';

?>