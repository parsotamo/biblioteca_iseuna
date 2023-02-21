<?php

//procurar_livro.php

include 'base_dados_con.php';

include 'funcoes.php';

if (!is_usuario_entrada()) {
	header('location:usuario_entrada.php');
}

$query = "
	SELECT * FROM livro 
    WHERE estado = 'activado' 
    ORDER BY id DESC
";

$statement = $connect->prepare($query);

$statement->execute();


include 'cabecalho.php';

?>

<div class="container-fluid py-4" style="min-height: 700px;">

	<h1>Search Book</h1>

	<div class="card mb-4">
		<div class="card-header">
			<div class="row">
				<div class="col col-md-6">
					<i class="fas fa-table me-1"></i> Lista de livros
				</div>
				<div class="col col-md-6" align="right">

				</div>
			</div>
		</div>
		<div class="card-body">
			<table id="datatablesSimple">
				<thead>
					<tr>
						<th>Nome do livro</th>
						<th>Nr. ISBN</th>
						<th>Categoria</th>
						<th>Autor</th>
						<th>Localizacao do armário</th>
						<th>Número de cópias disponíveis</th>
						<th>Estado</th>
						<th>Adicionado em</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>Nome do livro</th>
						<th>Nr. ISBN</th>
						<th>Categoria</th>
						<th>Autor</th>
						<th>Localizacao do armário</th>
						<th>Número de cópias disponíveis</th>
						<th>Estado</th>
						<th>Adicionado em</th>
					</tr>
				</tfoot>
				<tbody>
					<?php

					if ($statement->rowCount() > 0) {
						foreach ($statement->fetchAll() as $row) {
							$estado_livro = '';
							if ($row['nr_copia'] > 0) {
								$estado_livro = '<div class="badge bg-success">Disponível</div>';
							} else {
								$estado_livro = '<div class="badge bg-danger">Não Disponível</div>';
							}
							echo '
							<tr>
								<td>' . $row["nome"] . '</td>
								<td>' . $row["numero_isbn"] . '</td>
								<td>' . $row["categoria"] . '</td>
								<td>' . $row["autor"] . '</td>
								<td>' . $row["armario"] . '</td>
								<td>' . $row["nr_copia"] . '</td>
								<td>' . $estado_livro . '</td>
								<td>' . $row["adicionado_em"] . '</td>
							</tr>
						';
						}
					} else {
						echo '
					<tr>
						<td colspan="8" class="text-center">Nenhum livro encontrado</td>
					</tr>
					';
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