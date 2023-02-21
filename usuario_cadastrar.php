<?php

//usuario_cadastrar.php

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\SMTP;
// use PHPMailer\PHPMailer\Exception;

include 'base_dados_con.php';

include 'funcoes.php';

if (is_usuario_entrada()) {
	header('location:requisitar_livro_detalhes.php');
}

$message = '';

$success = '';

if (isset($_POST["register_button"])) {
	$formdata = array();

	if (empty($_POST["email"])) {
		$message .= '<li>Endereço de email é obrigatório</li>';
	} else {
		if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
			$message .= '<li>Endereço de email inválido</li>';
		} else {
			$formdata['email'] = trim($_POST['email']);
		}
	}

	if (empty($_POST["senha"])) {
		$message .= '<li>Palavra-passe é obrigatório</li>';
	} else {
		$formdata['senha'] = trim($_POST['senha']);
	}

	if (empty($_POST['nome'])) {
		$message .= '<li>Nome de usuário é obrigatório</li>';
	} else {
		$formdata['nome'] = trim($_POST['nome']);
	}

	if (empty($_POST['endereco'])) {
		$message .= '<li>Endereço do usuário é obrigatório</li>';
	} else {
		$formdata['endereco'] = trim($_POST['endereco']);
	}

	if (empty($_POST['contacto'])) {
		$message .= '<li>O contacto do usuário é obrigatório</li>';
	} else {
		$formdata['contacto'] = trim($_POST['contacto']);
	}

	if (!empty($_FILES['foto']['name'])) {
		$img_name = $_FILES['foto']['name'];
		$img_type = $_FILES['foto']['type'];
		$tmp_name = $_FILES['foto']['tmp_name'];
		$img_explode = explode(".", $img_name);

		$img_ext = strtolower(end($img_explode));

		$extensions = ["jpeg", "png", "jpg"];

		if (in_array($img_ext, $extensions)) {
			$new_img_name = time() . '-' . rand() . '.' . $img_ext;
			$path = "upload/" . $new_img_name;
		} else {
			$message .= '<li>Ficheiro de imagem inválido</li>';
		}
	} else {
		$message .= '<li>Por favor selecione uma imagem</li>';
	}

	if ($message == '') {
		$formdata['foto'] = $new_img_name;
		$data = array(
			':email'		=>	$formdata['email']
		);

		$query = "
		SELECT * FROM usuario 
        WHERE email = :email
		";

		$statement = $connect->prepare($query);

		$statement->execute($data);

		if ($statement->rowCount() > 0) {
			$message = '<li>Email já existe</li>';
		} else {
			$codigo_verificacao = md5(uniqid());

			$user_unique_id = 'U' . rand(10000000, 99999999);

			move_uploaded_file($tmp_name, $path);
			$img = new Imagick($path);
			$img->resizeImage(150, 150, Imagick::FILTER_LANCZOS, 1);
			$img->writeImage($path);

			$data = array(
				'unique_id'				=>  $user_unique_id,
				':nome'			=>	$formdata['nome'],
				':endereco'			=>	$formdata['endereco'],
				':contacto'		=>	$formdata['contacto'],
				':foto'			=>	$formdata['foto'],
				':email'	=>	$formdata['email'],
				':senha'		=>	$formdata['senha'],
				':codigo_verificacao' =>	$codigo_verificacao,
				':estado_verificacao'	=>	'nao',
				':estado'			=>	'activado',
				':criado_em'		=>	get_date_time($connect)
			);

			$query = "
			INSERT INTO usuario 
            (unique_id, nome, endereco, contacto, foto, email, senha, codigo_verificacao, estado_verificacao, estado, criado_em) 
            VALUES (:unique_id, :nome, :endereco, :contacto, :foto, :email, :senha, :codigo_verificacao, :estado_verificacao, :estado, :criado_em)
			";

			$statement = $connect->prepare($query);

			$statement->execute($data);

			// require 'vendor/autoload.php';

			// $mail = new PHPMailer(true);

			// $mail->isSMTP();

			// $mail->Host = 'smtp.gmail.com';  // Aqui define-se servido GMail SMTP

			// $mail->SMTPAuth = true;

			// $mail->Username = 'xxxx';  // Aqui posso usar um endereço Gmail

			// $mail->Password = 'xxxx';  // Palavra-passe do endereço Gmail

			// $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

			// $mail->Port = 80;

			// $mail->setFrom('glakert@gmail.com', 'Admin');

			// $mail->addAddress($formdata['email'], $formdata['nome']);

			// $mail->isHTML(true);

			// $mail->Subject = 'Verificação de registro da Biblioteca ISEUNA';

			// $mail->Body = '
			//  <p>Obrigado por se registar na Biblioteca ISEUNA. Seu endereço único é <b>' . $user_unique_id . '</b> que será usado para requisitar livro.</p>
			//     <p>Este email é de verificação, por favor clica no link para verificar o endereço de email.</p>
			//     <p><a href="' . base_url() . 'verificacao.php?code=' . $codigo_verificacao . '">Clica para verificar</a></p>
			//     <p>Obrigado...</p>
			// ';

			// $mail->send();

			// $success = 'Verificação de email enviado para ' . $formdata['email'] . ', então antes de entrar verifica primeiro o endereço de email.';
		}
	}
}

include 'cabecalho.php';

?>


<div class="d-flex align-items-center justify-content-center mt-5 mb-5" style="min-height:700px;">
	<div class="col-md-6">
		<?php

		if ($message != '') {
			echo '<div class="alert alert-danger"><ul>' . $message . '</ul></div>';
		}

		if ($success != '') {
			echo '<div class="alert alert-success">' . $success . '</div>';
		}

		?>
		<div class="card">
			<div class="card-header">Registro de novo usuário</div>
			<div class="card-body">
				<form method="POST" enctype="multipart/form-data">
					<div class="mb-3">
						<label class="form-label">Endereço de Email</label>
						<input type="text" name="email" id="email" class="form-control" />
					</div>
					<div class="mb-3">
						<label class="form-label">Senha</label>
						<input type="password" name="senha" id="senha" class="form-control" />
					</div>
					<div class="mb-3">
						<label class="form-label">Nome do usuário</label>
						<input type="text" name="nome" class="form-control" id="nome" value="" />
					</div>
					<div class="mb-3">
						<label class="form-label">Contacto</label>
						<input type="text" name="contacto" id="contacto" class="form-control" />
					</div>
					<div class="mb-3">
						<label class="form-label">Endereço de morada</label>
						<textarea name="endereco" id="endereco" class="form-control"></textarea>
					</div>
					<div class="mb-3">
						<label class="form-label">Foto</label><br />
						<input type="file" name="foto" id="foto" />
						<br />
						<span class="text-muted">Somente imagem no formato .jpg ou .png é permitido. O tamanho será redimensionado para 150 x 150.</span>
					</div>
					<div class="text-center mt-4 mb-2">
						<input type="submit" name="register_button" class="btn btn-primary" value="Cadastrar" />
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<?php


include 'rodape.php';

?>