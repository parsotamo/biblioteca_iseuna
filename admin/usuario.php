<?php

//usuario.php

include '../base_dados_con.php';

include '../funcoes.php';

if (!is_admin_entrada()) {
	header('location:../admin_entrada.php');
}

if (isset($_GET["accao"], $_GET['status'], $_GET['codigo']) && $_GET["accao"] == "apagar") {
	$id_usuario = $_GET["codigo"];
	$status = $_GET["estado"];

	$data = array(
		':estado'		=>	$status,
		':actualizado_em'	=>	get_data_temp($connect),
		':id_usuario'			=>	$id_usuario
	);

	$query = "
	UPDATE usuario 
    SET estado = :estado, 
    actualizado_em = :actualizado_em 
    WHERE unique_id = :id_usuario
	";

	$statement = $connect->prepare($query);

	$statement->execute($data);

	header('location:usuario.php?msg=' . strtolower($status) . '');
}

$query = "
	SELECT * FROM usuario 
    ORDER BY id DESC
";

$statement = $connect->prepare($query);

$statement->execute();

include '../cabecalho.php';

?>

<div class="container-fluid py-4" style="min-height: 700px;">
	<h1>Gestão de Usuários</h1>
	<ol class="breadcrumb mt-4 mb-4 bg-color p-2 border">
		<li class="breadcrumb-item"><a href="index.php">Painel de Controle</a></li>
		<li class="breadcrumb-item active">Gestão de Usuários</li>
	</ol>
	<?php

	if (isset($_GET["msg"])) {
		if ($_GET["msg"] == 'disativado') {
			echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Estado de Usuário alterado para Disativado <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
		}

		if ($_GET["msg"] == 'activado') {
			echo '
 			<div class="alert alert-success alert-dismissible fade show" role="alert">Estado de Usuário alterado para Activado <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
 			';
		}
	}

	?>
	<div class="card mb-4">
		<div class="card-header">
			<div class="row">
				<div class="col col-md-6">
					<i class="fas fa-table me-1"></i> Gestão de Usuários
				</div>
				<div class="col col-md-6" align="right">
				</div>
			</div>
		</div>
		<div class="card-body">
			<table id="datatablesSimple">
				<thead>
					<tr>
						<th>ID</th>
						<th>Foto</th>
						<th>Nome de Usuário</th>
						<th>Endereço de Email</th>
						<th>Morada</th>
						<th>Contacto</th>
						<th>Verificado?</th>
						<th>Estado</th>
						<th>Criado Em</th>
						<th>Actualizado Em</th>
						<th>Acção</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>ID</th>
						<th>Foto</th>
						<th>Nome de Usuário</th>
						<th>Endereço de Email</th>
						<th>Morada</th>
						<th>Contacto</th>
						<th>Verificado?</th>
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
								$estado = '<div class="badge bg-danger">Desativado</div>';
							}
							$estadoString = $row['estado'] == 'activado' ? 'Desativar' : 'Activar';
							$estadoCor = $row['estado'] == 'activado' ? 'danger' : 'success';
							echo '
    					<tr>
						<td>' . $row["unique_id"] . '</td>
    						<td><img src="../upload/' . $row["foto"] . '" class="img-thumbnail" width="75" /></td>
    						<td>' . $row["nome"] . '</td>
    						<td>' . $row["email"] . '</td>
    						<td>' . $row["endereco"] . '</td>
    						<td>' . $row["contacto"] . '</td>
    						<td>' . $row["estado_verificacao"] . '</td>
    						<td>' . $estado . '</td>
    						<td>' . $row["criado_em"] . '</td>
    						<td>' . $row["actualizado_em"] . '</td>
    						<td><button type="button" name="delete_button" class="btn btn-' . $estadoCor . ' btn-sm" onclick="apagar_dados(`' . $row["unique_id"] . '`, `' . $row["estado"] . '`)">' . $estadoString . '</td>
    					</tr>
    					';
						}
					} else {
						echo '

    				<tr>
    					<td colspan="12" class="text-center">Nenhum usuário encontrado!</td>
    				</tr>
    				';
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
	function apagar_dados(code, estado) {
		var novo_estado = 'activado';
		if (estado == 'activado') {
			novo_estado = 'desativado';
		}
		var novo_estado_str = novo_estado === 'activado' ? 'activar' : 'desativar';
		if (confirm("Tem certeza que deseja " + novo_estado_str + " este Usuário?")) {
			window.location.href = "usuario.php?accao=apagar&codigo=" + code + "&estado=" + novo_estado + "";
		}
	}
</script>

<?php

include '../rodape.php';

?>