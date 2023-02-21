<?php

include 'base_dados_con.php';
include 'funcoes.php';

if (is_usuario_entrada()) {
	header('location:requisitar_livro_detalhes.php');
}

include 'cabecalho.php';



?>

<div class="p-5 mb-4 bg-light rounded-3">

	<div class="container-fluid py-5">

		<h1 class="display-5 fw-bold">Sistema de Gestão de Biblioteca da faculdade ISEUNA</h1>

		<p class="fs-4">Este é um sistema de gerenciamento de biblioteca simples que usa para manter o registro da biblioteca. Este sistema de gerenciamento de biblioteca foi feito usando script PHP, banco de dados MySQL, JavaScript Vanilla e framework Bootstrap 5. Este é o Projeto PHP no Sistema de Gerenciamento de Biblioteca Online.</p>

	</div>

</div>

<div class="row align-items-md-stretch">

	<div class="col-md-6">

		<div class="h-100 p-5 text-white bg-dark rounded-3">

			<h2>Entrada de Admin</h2>
			<p></p>
			<a href="admin_entrada.php" class="btn btn-outline-light">Entrar</a>

		</div>

	</div>

	<div class="col-md-6">

		<div class="h-100 p-5 bg-light border rounded-3">

			<h2>Entrada de Usuário</h2>

			<p></p>

			<a href="usuario_entrada.php" class="btn btn-outline-secondary">Entrada</a>

			<a href="usuario_cadastrar.php" class="btn btn-outline-primary">Registo</a>

		</div>

	</div>

</div>

<?php

include 'rodape.php';

?>