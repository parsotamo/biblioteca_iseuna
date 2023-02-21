<?php

//index.php

include '../base_dados_con.php';

include '../funcoes.php';

if (!is_admin_entrada()) {
	header('location:../admin_entrada.php');
}


include '../cabecalho.php';

?>

<div class="container-fluid py-4">
	<h1 class="mb-5">Painel de Controle</h1>
	<div class="row">
		<div class="col-xl-3 col-md-6">
			<div class="card bg-primary text-white mb-4">
				<div class="card-body">
					<h1 class="text-center"><?php echo contar_todos_livros_requisitados($connect); ?></h1>
					<h5 class="text-center">Total de Livros Requisitados</h5>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6">
			<div class="card bg-warning text-white mb-4">
				<div class="card-body">
					<h1 class="text-center"><?php echo contar_todos_livros_devolvidos($connect); ?></h1>
					<h5 class="text-center">Total de Livros Devolvidos</h5>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6">
			<div class="card bg-danger text-white mb-4">
				<div class="card-body">
					<h1 class="text-center"><?php echo contar_todos_livros_nao_devolvidos($connect); ?></h1>
					<h5 class="text-center">Total de Livros Não Devolvidos</h5>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6">
			<div class="card bg-success text-white mb-4">
				<div class="card-body">
					<h1 class="text-center"><?php echo contar_total_multas($connect) . get_moeda($connect); ?></h1>
					<h5 class="text-center">Total de Multas</h5>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6">
			<div class="card bg-success text-white mb-4">
				<div class="card-body">
					<h1 class="text-center"><?php echo contar_todos_livros($connect); ?></h1>
					<h5 class="text-center">Total Livros</h5>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6">
			<div class="card bg-danger text-white mb-4">
				<div class="card-body">
					<h1 class="text-center"><?php echo contar_todos_autores($connect); ?></h1>
					<h5 class="text-center">Total Autores</h5>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6">
			<div class="card bg-warning text-white mb-4">
				<div class="card-body">
					<h1 class="text-center"><?php echo contar_todas_categorias($connect); ?></h1>
					<h5 class="text-center">Total Categorias</h5>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6">
			<div class="card bg-primary text-white mb-4">
				<div class="card-body">
					<h1 class="text-center"><?php echo contar_todos_armarios($connect); ?></h1>
					<h5 class="text-center">Total Armários</h5>
				</div>
			</div>
		</div>
	</div>
</div>

<?php

include '../rodape.php';

?>