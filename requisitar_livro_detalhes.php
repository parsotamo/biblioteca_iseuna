<?php

//requisitar_livro_detalhes.php

include 'base_dados_con.php';

include 'funcoes.php';

if (!is_usuario_entrada()) {
	header('location:usuario_entrada.php');
}

$query = "
	SELECT * FROM requisitar_livro 
	INNER JOIN livro 
	ON livro.numero_isbn = requisitar_livro.id_livro 
	WHERE requisitar_livro.id_usuario = '" . $_SESSION['id_usuario'] . "' 
	ORDER BY requisitar_livro.id_livro DESC
";

$statement = $connect->prepare($query);

$statement->execute();

include 'cabecalho.php';

?>
<div class="container-fluid py-4" style="min-height: 700px;">
	<h1>Detalhes de Requisição de Livro</h1>
	<div class="card mb-4">
		<div class="card-header">
			<div class="row">
				<div class="col col-md-6">
					<i class="fas fa-table me-1"></i> Detalhes de Requisição de Livro
				</div>
				<div class="col col-md-6" align="right">
				</div>
			</div>
		</div>
		<div class="card-body">
			<table id="datatablesSimple">
				<thead>
					<tr>
						<th>Nr. ISBN</th>
						<th>Nome de Livro</th>
						<th>Data de Requisição</th>
						<th>Data de Retorno de Livro</th>
						<th>Multas</th>
						<th>Estado</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>Nr. ISBN</th>
						<th>Nome de Livro</th>
						<th>Data de Requisição</th>
						<th>Data de Retorno de Livro</th>
						<th>Multas</th>
						<th>Estado</th>
					</tr>
				</tfoot>
				<tbody>
					<?php
					if ($statement->rowCount() > 0) {
						foreach ($statement->fetchAll() as $row) {
							$status = $row["estado"];
							if ($status == 'requisitado') {
								$status = '<span class="badge bg-warning">Requisitado</span>';
							}

							if ($status == 'nao_devolvido') {
								$status = '<span class="badge bg-danger">Não Devolvido</span>';
							}

							if ($status == 'devolvido') {
								$status = '<span class="badge bg-primary">Devolvido</span>';
							}

							echo '
						<tr>
							<td>' . $row["numero_isbn"] . '</td>
							<td>' . $row["nome"] . '</td>
							<td>' . $row["data_requisicao"] . '</td>
							<td>' . $row["data_retorno"] . '</td>
							<td>' . get_moeda($connect) . $row["multas"] . '</td>
							<td>' . $status . '</td>
						</tr>
						';
						}
					}
					?>
				</tbody>
			</table>
		</div>
	</div>

</div>

<?php

include 'rodape.php';

?>