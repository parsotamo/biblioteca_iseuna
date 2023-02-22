<?php

//perfil.php
// use Imagick;

use SRC\Imagick;

require 'vendor/autoload.php';
include 'base_dados_con.php';

include 'funcoes.php';


if (!is_usuario_entrada()) {
	header('location:usuario_entrada.php');
}

$mensagem = '';

$success = '';

if (isset($_POST['save_button'])) {
	$formdata = array();

	if (empty($_POST['email'])) {
		$mensagem .= '<li>Endereço de email é obrigatório</li>';
	} else {
		if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
			$mensagem .= '<li>Endereço de Email Inválido!</li>';
		} else {
			$formdata['email'] = trim($_POST['email']);
		}
	}

	if (empty($_POST['senha'])) {
		$mensagem .= '<li>Palavra-passe é obrigatório</li>';
	} else {
		$formdata['senha'] = trim($_POST['senha']);
	}

	if (empty($_POST['nome'])) {
		$mensagem .= '<li>Nome do usuário é obrigatório</li>';
	} else {
		$formdata['nome'] = trim($_POST['nome']);
	}

	if (empty($_POST['endereco'])) {
		$mensagem .= '<li>Endereço de morada é obrigatório</li>';
	} else {
		$formdata['endereco'] = trim($_POST['endereco']);
	}

	if (empty($_POST['contacto'])) {
		$mensagem .= '<li>Contacto é obrigatório</li>';
	} else {
		$formdata['contacto'] = $_POST['contacto'];
	}

	$formdata['foto'] = $_POST['foto'];

	if (!empty($_FILES['foto']['nome'])) {
		$img_name = $_FILES['foto']['nome'];
		$tmp_name = $_FILES['foto']['tmp_name'];
		$img_explode = explode(".", $img_name);
		$img_ext = strtolower(end($img_explode));
		$extensions = ["jpeg", "png", "jpg"];
		if (in_array($img_ext, $extensions) == false) {
			$mensagem .= '<li>Ficheiro de imagem inválido</li>';
		}
	}

	if ($mensagem == '') {
		$path = "upload/" . $formdata['foto'];
		unlink($path);
		list($width, $height) = getimagesize($tmp_name);
		move_uploaded_file($tmp_name, $path);
		$newwidth = 150;
		$newheight = 150;
		move_uploaded_file($tmp_name, $path);
		$new_img = imagecreatetruecolor($new_width, $new_height);
		switch ($img_type) {
			case 'image/jpg':
				$orig = imagecreatefromjpeg($orig_path);
				break;
			case 'image/jpeg':
				$orig = imagecreatefromjpeg($orig_path);
				break;
			case 'image/png':
				$orig = imagecreatefrompng($orig_path);
				break;
		}
		imagecopyresampled(
			$new_img,
			$orig,
			0,
			0,
			0,
			0,
			$new_width,
			$new_height,
			$width,
			$height
		);
		switch ($media_type) {
			case 'image/jpg':
				imagejpeg($new_img, $path);
			case 'image/jpeg':
				imagejpeg($new_img, $path);
				break;
			case 'image/png':
				imagepng($new_img, $path);
				break;
		}
		// $img = new Imagick($path);
		// $img->resizeImage(150, 150, Imagick::FILTER_LANCZOS, 1);
		// $img->writeImage($path);
		$data = array(
			':nome'			=>	$formdata['nome'],
			':endereco'			=>	$formdata['endereco'],
			':contacto'		=>	$formdata['contacto'],
			':email'	=>	$formdata['email'],
			':senha'		=>	$formdata['senha'],
			':actualizado_em'		=>	get_data_temp($connect),
			':id_usuario'		=>	$_SESSION['id_usuario']
		);

		$query = "
		UPDATE usuario 
            SET nome = :nome, 
            endereco = :endereco, 
            contacto = :contacto, 
            email = :email, 
            senha = :senha, 
            actualizado_em = :actualizado_em 
            WHERE unique_id = :id_usuario
		";

		$statement = $connect->prepare($query);

		$statement->execute($data);

		$success = 'Informação actualizada com sucesso';
	}
}


$query = "
	SELECT * FROM usuario 
	WHERE unique_id = '" . $_SESSION['id_usuario'] . "'
";

$result = $connect->query($query);

include 'cabecalho.php';

?>

<div class="d-flex align-items-center justify-content-center mt-5 mb-5" style="min-height:700px;">
	<div class="col-md-6">
		<?php
		if ($mensagem != '') {
			echo '<div class="alert alert-danger"><ul>' . $mensagem . '</ul></div>';
		}

		if ($success != '') {
			echo '<div class="alert alert-success">' . $success . '</div>';
		}
		?>
		<div class="card">
			<div class="card-header">Perfil</div>
			<div class="card-body">
				<?php
				foreach ($result as $row) {
				?>
					<form method="POST" enctype="multipart/form-data">
						<div class="mb-3">
							<label class="form-label">Endereço de email</label>
							<input type="text" name="email" id="email" class="form-control" value="<?php echo $row['email']; ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Senha</label>
							<input type="password" name="senha" id="senha" class="form-control" value="<?php echo $row['senha']; ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Nome</label>
							<input type="text" name="nome" id="nome" class="form-control" value="<?php echo $row['nome']; ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Contacto</label>
							<input type="text" name="contacto" id="contacto" class="form-control" value="<?php echo $row['contacto']; ?>" />
						</div>
						<div class="mb-3">
							<label class="form-label">Endereço</label>
							<textarea name="endereco" id="endereco" class="form-control"><?php echo $row['endereco']; ?></textarea>
						</div>
						<div class="mb-3">
							<label class="form-label">Foto</label><br />
							<input type="file" name="foto" id="foto" />
							<br />
							<span class="text-muted">Somente imagem .jpg ou .png é permitido. Tamanho será redimensionado para 150 x 150</span>
							<br />
							<input type="hidden" name="foto" value="<?php echo $row['foto']; ?>" />
							<img src="upload/<?php echo $row['foto']; ?>?<?php echo time(); ?>" width="100" class="img-thumbnail" />
						</div>
						<div class="text-center mt-4 mb-2">
							<input type="submit" name="save_button" class="btn btn-primary" value="Actualizar" />
						</div>
					</form>

				<?php
				}
				?>
			</div>
		</div>
	</div>
</div>

<?php

include 'rodape.php';

?>