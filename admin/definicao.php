<?php

//definicao.php

include '../base_dados_con.php';

include '../funcoes.php';


if (!is_admin_entrada()) {
	header('location:../admin_entrada.php');
}

$message = '';

if (isset($_POST['editar_definicoes'])) {
	$data = array(
		':nome'					=>	$_POST['nome'],
		':endereco'				=>	$_POST['endereco'],
		':contacto'		=>	$_POST['contacto'],
		':email'		=>	$_POST['email'],
		':numero_dias_emprestimo'	=>	$_POST['numero_dias_emprestimo'],
		':um_dia_multa'			=>	$_POST['um_dia_multa'],
		':moeda'				=>	$_POST['moeda'],
		':fuso_horario'				=>	$_POST['fuso_horario'],
		':limite_requisicao_por_usuario'	=>	$_POST['limite_requisicao_por_usuario']
	);

	$query = "
	UPDATE definicao 
        SET nome = :nome,
        endereco = :endereco, 
        contacto = :contacto, 
        email = :email, 
        numero_dias_emprestimo = :numero_dias_emprestimo, 
        um_dia_multa = :um_dia_multa, 
        moeda = :moeda, 
        fuso_horario = :fuso_horario, 
        limite_requisicao_por_usuario = :limite_requisicao_por_usuario
	";

	$statement = $connect->prepare($query);

	$statement->execute($data);

	$message = '
	<div class="alert alert-success alert-dismissible fade show" role="alert">Data Edited <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
	';
}

$query = "
SELECT * FROM definicao 
LIMIT 1
";

$result = $connect->query($query);

include '../cabecalho.php';

?>

<div class="container-fluid px-4">
	<h1 class="mt-4">Definições</h1>

	<ol class="breadcrumb mt-4 mb-4 bg-color p-2 border">
		<li class="breadcrumb-item"><a href="index.php">Painel de Controle</a></li>
		<li class="breadcrumb-item active">Definições</a></li>
	</ol>
	<?php

	if ($message != '') {
		echo $message;
	}

	?>
	<div class="card mb-4">
		<div class="card-header">
			<i class="fas fa-user-edit"></i> Definições da Biblioteca
		</div>
		<div class="card-body">

			<form method="post">
				<?php
				foreach ($result as $row) {
				?>
					<div class="row">
						<div class="col-md-12">
							<div class="mb-3">
								<label class="form-label">Nome da Biblioteca</label>
								<input type="text" name="nome" id="nome" class="form-control" value="<?php echo $row['nome']; ?>" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="mb-3">
								<label class="form-label">Endereço</label>
								<textarea name="endereco" id="endereco" class="form-control"><?php echo $row["endereco"]; ?></textarea>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">Número de Contacto</label>
								<input type="text" name="contacto" id="contacto" class="form-control" value="<?php echo $row['contacto']; ?>" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">Endereço de Email</label>
								<input type="text" name="email" id="email" class="form-control" value="<?php echo $row['email']; ?>" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">Número de Dias de Emprestimo</label>
								<input type="number" name="numero_dias_emprestimo" id="numero_dias_emprestimo" class="form-control" value="<?php echo $row['numero_dias_emprestimo']; ?>" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">Multa de atraso de livro diário</label>
								<input type="number" name="um_dia_multa" id="um_dia_multa" class="form-control" value="<?php echo $row['um_dia_multa']; ?>" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">Moeda</label>
								<select name="moeda" id="moeda" class="form-control">
									<?php echo Currency_list(); ?>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">Fuso Horário</label>
								<select name="fuso_horario" id="fuso_horario" class="form-control">
									<?php echo Timezone_list(); ?>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<label class="form-label">Número limite de pedidos por usuário</label>
							<input type="number" name="limite_requisicao_por_usuario" id="limite_requisicao_por_usuario" class="form-control" value="<?php echo $row['limite_requisicao_por_usuario']; ?>" />
						</div>
					</div>
					<div class="mt-4 mb-0">
						<input type="submit" name="editar_definicoes" class="btn btn-danger" value="Salvar" />
					</div>
					<script type="text/javascript">
						document.getElementById('moeda').value = "<?php echo $row['moeda']; ?>";

						document.getElementById('fuso_horario').value = "<?php echo $row['fuso_horario']; ?>";
					</script>
				<?php
				}
				?>
			</form>

		</div>
	</div>
</div>

<?php

include '../rodape.php';

?>