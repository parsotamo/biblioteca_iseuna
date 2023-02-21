<?php

//livro.php

include '../base_dados_con.php';

include '../funcoes.php';


if (!is_admin_entrada()) {
	header('location:../admin_entrada.php');
}

$message = '';

$error = '';

if (isset($_POST["add_book"])) {
	$formdata = array();

	if (empty($_POST["nome"])) {
		$error .= '<li>Nome de livro é obrigatório</li>';
	} else {
		$formdata['nome'] = trim($_POST["nome"]);
	}

	if (empty($_POST["categoria"])) {
		$error .= '<li>Categoria de livro é obrigatório!</li>';
	} else {
		$formdata['categoria'] = trim($_POST["categoria"]);
	}

	if (empty($_POST["autor"])) {
		$error .= '<li>Autor de Livro é obrigatório!</li>';
	} else {
		$formdata['autor'] = trim($_POST["autor"]);
	}

	if (empty($_POST["armario"])) {
		$error .= '<li>Armário de Livro é obrigatório!</li>';
	} else {
		$formdata['armario'] = trim($_POST["armario"]);
	}

	if (empty($_POST["numero_isbn"])) {
		$error .= '<li>ISBN de Livro é obrigatório!</li>';
	} else {
		$formdata['numero_isbn'] = trim($_POST["numero_isbn"]);
	}
	if (empty($_POST["nr_copia"])) {
		$error .= '<li>Nr. de cópio do livro é obrigatório</li>';
	} else {
		$formdata['nr_copia'] = trim($_POST["nr_copia"]);
	}

	if ($error == '') {
		$data = array(
			':categoria'		=>	$formdata['categoria'],
			':autor'			=>	$formdata['autor'],
			':armario'	=>	$formdata['armario'],
			':nome'			=>	$formdata['nome'],
			':numero_isbn'		=>	$formdata['numero_isbn'],
			':nr_copia'		=>	$formdata['nr_copia'],
			':estado'			=>	'activado',
			':adicionado_em'		=>	get_date_time($connect)
		);
		$query = "
		INSERT INTO livro 
        (categoria, autor, armario, nome, numero_isbn, nr_copia, estado, adicionado_em) 
        VALUES (:categoria, :autor, :armario, :nome, :numero_isbn, :nr_copia, :estado, :adicionado_em)
		";

		$statement = $connect->prepare($query);

		$statement->execute($data);

		header('location:livro.php?msg=add');
	}
}

if (isset($_POST["edit_book"])) {
	$formdata = array();

	if (empty($_POST["nome"])) {
		$error .= '<li>Nome de livro é obrigatório</li>';
	} else {
		$formdata['nome'] = trim($_POST["nome"]);
	}

	if (empty($_POST["categoria"])) {
		$error .= '<li>Categoria de livro é obrigatório!</li>';
	} else {
		$formdata['categoria'] = trim($_POST["categoria"]);
	}

	if (empty($_POST["autor"])) {
		$error .= '<li>Autor de Livro é obrigatório!</li>';
	} else {
		$formdata['autor'] = trim($_POST["autor"]);
	}

	if (empty($_POST["armario"])) {
		$error .= '<li>Armário de Livro é obrigatório!</li>';
	} else {
		$formdata['armario'] = trim($_POST["armario"]);
	}

	if (empty($_POST["numero_isbn"])) {
		$error .= '<li>ISBN de Livro é obrigatório!</li>';
	} else {
		$formdata['numero_isbn'] = trim($_POST["numero_isbn"]);
	}
	if (empty($_POST["nr_copia"])) {
		$error .= '<li>Nr. de cópio do livro é obrigatório</li>';
	} else {
		$formdata['nr_copia'] = trim($_POST["nr_copia"]);
	}

	if ($error == '') {
		$data = array(
			':categoria'		=>	$formdata['categoria'],
			':autor'			=>	$formdata['autor'],
			':armario'	=>	$formdata['armario'],
			':nome'			=>	$formdata['nome'],
			':numero_isbn'		=>	$formdata['numero_isbn'],
			':nr_copia'		=>	$formdata['nr_copia'],
			':actualizado_em'		=>	get_date_time($connect),
			':id'				=>	$_POST["id"]
		);
		$query = "
		UPDATE livro 
        SET categoria = :categoria, 
        autor = :autor, 
        armario = :armario, 
        nome = :nome, 
        numero_isbn = :numero_isbn, 
        nr_copia = :nr_copia, 
        actualizado_em = :actualizado_em 
        WHERE id = :id
		";

		$statement = $connect->prepare($query);

		$statement->execute($data);

		header('location:livro.php?msg=edit');
	}
}

if (isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'delete') {
	$id = $_GET["code"];
	$status = $_GET["status"];

	$data = array(
		':estado'		=>	$status,
		':actualizado_em'	=>	get_date_time($connect),
		':id'			=>	$id
	);

	$query = "
	UPDATE livro 
    SET estado = :estado, 
    actualizado_em = :actualizado_em 
    WHERE id = :id
	";

	$statement = $connect->prepare($query);

	$statement->execute($data);

	header('location:livro.php?msg=' . strtolower($status) . '');
}


$query = "
	SELECT * FROM livro 
    ORDER BY id DESC
";

$statement = $connect->prepare($query);

$statement->execute();


include '../cabecalho.php';

?>

<div class="container-fluid py-4" style="min-height: 700px;">
	<h1>Gestão de Livro</h1>
	<?php
	if (isset($_GET["action"])) {
		if ($_GET["action"] == 'add') {
	?>

			<ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
				<li class="breadcrumb-item"><a href="index.php">Painel de Controle</a></li>
				<li class="breadcrumb-item"><a href="livro.php">Gestão de Livro</a></li>
				<li class="breadcrumb-item active">Adicionar Livro</li>
			</ol>

			<?php

			if ($error != '') {
				echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">' . $error . '</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}

			?>

			<div class="card mb-4">
				<div class="card-header">
					<i class="fas fa-user-plus"></i> Adicionar novo Livro
				</div>
				<div class="card-body">
					<form method="post">
						<div class="row">
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Nome do Livro</label>
									<input type="text" name="nome" id="nome" class="form-control" />
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Selecione o Autor</label>
									<select name="autor" id="autor" class="form-control">
										<?php echo fill_author($connect); ?>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Selecione a Categoria</label>
									<select name="categoria" id="categoria" class="form-control">
										<?php echo fill_category($connect); ?>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Selecione o Armário</label>
									<select name="armario" id="armario" class="form-control">
										<?php echo fill_armario($connect); ?>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Número ISBN do Livro</label>
									<input type="text" name="numero_isbn" id="numero_isbn" class="form-control" />
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Nr. da Cópia</label>
									<input type="number" name="nr_copia" id="nr_copia" step="1" class="form-control" />
								</div>
							</div>
						</div>
						<div class="mt-4 mb-3 text-center">
							<input type="submit" name="add_book" class="btn btn-success" value="Adicionar" />
						</div>
					</form>
				</div>
			</div>

			<?php
		} else if ($_GET["action"] == 'edit') {
			$id = convert_data($_GET["code"], 'decrypt');

			if ($id > 0) {
				$query = "
				SELECT * FROM livro 
                WHERE id = '$id'
				";

				$book_result = $connect->query($query);

				foreach ($book_result as $book_row) {
			?>
					<ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
						<li class="breadcrumb-item"><a href="index.php">Painel de Controle</a></li>
						<li class="breadcrumb-item"><a href="livro.php">Gestão de Livro</a></li>
						<li class="breadcrumb-item active">Actualizar Livro</li>
					</ol>
					<div class="card mb-4">
						<div class="card-header">
							<i class="fas fa-user-plus"></i> Actualizar Detalhes de Livro
						</div>
						<div class="card-body">
							<form method="post">
								<div class="row">
									<div class="col-md-6">
										<div class="mb-3">
											<label class="form-label">Nome do Livro</label>
											<input type="text" name="nome" id="nome" class="form-control" value="<?php echo $book_row['nome']; ?>" />
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label class="form-label">Selecione o Autor</label>
											<select name="autor" id="autor" class="form-control">
												<?php echo fill_author($connect); ?>
											</select>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="mb-3">
											<label class="form-label">Selecione a Categoria</label>
											<select name="categoria" id="categoria" class="form-control">
												<?php echo fill_category($connect); ?>
											</select>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label class="form-label">Selecione o Armário</label>
											<select name="armario" id="armario" class="form-control">
												<?php echo fill_armario($connect); ?>
											</select>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="mb-3">
											<label class="form-label">Número ISBN do Livro</label>
											<input type="text" name="numero_isbn" id="numero_isbn" class="form-control" value="<?php echo $book_row['numero_isbn']; ?>" />
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label class="form-label">Nr. da Cópia</label>
											<input type="number" name="nr_copia" id="nr_copia" class="form-control" step="1" value="<?php echo $book_row['nr_copia']; ?>" />
										</div>
									</div>
								</div>
								<div class="mt-4 mb-3 text-center">
									<input type="hidden" name="id" value="<?php echo $book_row['id']; ?>" />
									<input type="submit" name="edit_book" class="btn btn-primary" value="Actualizar" />
								</div>
							</form>
							<script>
								document.getElementById('autor').value = "<?php echo $book_row['autor']; ?>";
								document.getElementById('categoria').value = "<?php echo $book_row['categoria']; ?>";
								document.getElementById('armario').value = "<?php echo $book_row['armario']; ?>";
							</script>
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
			<li class="breadcrumb-item active">Gestão de Livro</li>
		</ol>
		<?php

		if (isset($_GET["msg"])) {
			if ($_GET["msg"] == 'add') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Novo Livro adicionado<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
			if ($_GET['msg'] == 'edit') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Livro actualizado <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
			if ($_GET["msg"] == 'desativado') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Estado de Livro alterado para desativado <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
			if ($_GET['msg'] == 'activado') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Estado de Livro alterado para Activado <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
		}

		?>
		<div class="card mb-4">
			<div class="card-header">
				<div class="row">
					<div class="col col-md-6">
						<i class="fas fa-table me-1"></i> Gestão de Livro
					</div>
					<div class="col col-md-6" align="right">
						<a href="livro.php?action=add" class="btn btn-success btn-sm">Adicionar</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<table id="datatablesSimple">
					<thead>
						<tr>
							<th>Nome do Livro</th>
							<th>ISBN No.</th>
							<th>Categoria</th>
							<th>Autor</th>
							<th>Armário</th>
							<th>Nr. da Cópia</th>
							<th>Estado</th>
							<th>Adicionado Em</th>
							<th>Actualizado Em</th>
							<th>Acção</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th>Nome do Livro</th>
							<th>ISBN No.</th>
							<th>Categoria</th>
							<th>Autor</th>
							<th>Armário</th>
							<th>Nr. da Cópia</th>
							<th>Estado</th>
							<th>Adicionado Em</th>
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
        					<td>' . $row["numero_isbn"] . '</td>
        					<td>' . $row["categoria"] . '</td>
        					<td>' . $row["autor"] . '</td>
        					<td>' . $row["armario"] . '</td>
        					<td>' . $row["nr_copia"] . '</td>
        					<td>' . $estado . '</td>
        					<td>' . $row["adicionado_em"] . '</td>
        					<td>' . $row["actualizado_em"] . '</td>
        					<td>
        						<a href="livro.php?action=edit&code=' . convert_data($row["id"]) . '" class="btn btn-sm btn-primary">Actualizar</a>
        						<button type="button" name="delete_button" class="btn btn-' . $estadoCor . ' btn-sm" onclick="delete_data(`' . $row["id"] . '`, `' . $row["estado"] . '`)">' . $estadoString . '</button>
        					</td>
        				</tr>
        				';
							}
						} else {
							echo '
        			<tr>
        				<td colspan="10" class="text-center">Nenhum livro encontrado!</td>
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

				if (confirm("Tem certeza que deseja " + novo_estado_str + " este Livro?")) {
					window.location.href = "livro.php?action=delete&code=" + code + "&status=" + novo_estado + "";
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