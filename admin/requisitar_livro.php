<?php

//requisitar_livro.php

include '../base_dados_con.php';

include '../funcoes.php';

if (!is_admin_entrada()) {
    header('location:../admin_entrada.php');
}

$error = '';

if (isset($_POST["issue_book_button"])) {
    $formdata = array();

    if (empty($_POST["id_livro"])) {
        $error .= '<li>Número ISBN é obrigatório!</li>';
    } else {
        $formdata['id_livro'] = trim($_POST['id_livro']);
    }

    if (empty($_POST["id_usuario"])) {
        $error .= '<li>ID do usuário é obrigatório!</li>';
    } else {
        $formdata['id_usuario'] = trim($_POST['id_usuario']);
    }

    if ($error == '') {
        //Check Book Available or Not

        $query = "
        SELECT * FROM livro 
        WHERE numero_isbn = '" . $formdata['id_livro'] . "'
        ";

        $statement = $connect->prepare($query);

        $statement->execute();

        if ($statement->rowCount() > 0) {
            foreach ($statement->fetchAll() as $book_row) {
                //check book is available or not
                if ($book_row['estado'] == 'activado' && $book_row['nr_copia'] > 0) {
                    //Check User is exist

                    $query = "
                    SELECT unique_id, estado FROM usuario 
                    WHERE unique_id = '" . $formdata['id_usuario'] . "'
                    ";

                    $statement = $connect->prepare($query);

                    $statement->execute();

                    if ($statement->rowCount() > 0) {
                        foreach ($statement->fetchAll() as $user_row) {
                            if ($user_row['estado'] == 'activado') {
                                //Check User Total issue of Book

                                $book_issue_limit = get_limite_requisicao_por_usuario($connect);

                                $total_book_issue = get_total_livro_requisitado_por_usuario($connect, $formdata['id_usuario']);

                                if ($total_book_issue < $book_issue_limit) {
                                    $numero_dias_emprestimo = get_numero_dias_emprestimo($connect);

                                    $today_date = get_data_temp($connect);

                                    $data_retorno_esperada = date('Y-m-d H:i:s', strtotime($today_date . ' + ' . $numero_dias_emprestimo . ' days'));

                                    $data = array(
                                        ':id_livro'      =>  $formdata['id_livro'],
                                        ':id_usuario'      =>  $formdata['id_usuario'],
                                        ':data_requisicao'  =>  $today_date,
                                        ':data_retorno_esperada' => $data_retorno_esperada,
                                        // ':data_retorno' =>  '',
                                        ':multas'       =>  0,
                                        ':estado_livro'    =>  'requisitado'
                                    );

                                    $query = "
                                    INSERT INTO requisitar_livro 
                                    (id_livro, id_usuario, data_requisicao, data_retorno_esperada, multas, estado_livro) 
                                    VALUES (:id_livro, :id_usuario, :data_requisicao, :data_retorno_esperada, :multas, :estado_livro)
                                    ";

                                    $statement = $connect->prepare($query);

                                    $statement->execute($data);

                                    $query = "
                                    UPDATE livro 
                                    SET nr_copia = nr_copia - 1, 
                                    actualizado_em = '" . $today_date . "' 
                                    WHERE numero_isbn = '" . $formdata['id_livro'] . "' 
                                    ";

                                    $connect->query($query);

                                    header('location:requisitar_livro.php?msg=add');
                                } else {
                                    $error .= 'Usuário atingiu limite máximo de requisição de livros, Primeiro retorne livros pendentes.';
                                }
                            } else {
                                $error .= '<li>Conta de Usuário desativada, Contacte o Administrador.</li>';
                            }
                        }
                    } else {
                        $error .= '<li>Usuário não encontrado!</li>';
                    }
                } else {
                    $error .= '<li>Livro não disponível!</li>';
                }
            }
        } else {
            $error .= '<li>Livro não encontrado!</li>';
        }
    }
}

if (isset($_POST["book_return_button"])) {
    if (isset($_POST["book_return_confirmation"])) {
        $data = array(
            ':data_retorno'     =>  get_data_temp($connect),
            ':estado_livro'    =>  'devolvido',
            ':id_livro'        =>  $_POST['id_livro']
        );

        $query = "
        UPDATE requisitar_livro 
        SET data_retorno = :data_retorno, 
        estado_livro = :estado_livro 
        WHERE id_livro = :id_livro
        ";

        $statement = $connect->prepare($query);

        $statement->execute($data);

        $query = "
        UPDATE livro 
        SET nr_copia = nr_copia + 1 
        WHERE numero_isbn = '" . $_POST["numero_isbn"] . "'
        ";

        $connect->query($query);

        header("location:requisitar_livro.php?msg=return");
    } else {
        $error = 'Por favor primeiro confirme a recepção do Livro clicando na caixinha.';
    }
}

$query = "
	SELECT * FROM requisitar_livro 
    ORDER BY id_livro DESC
";

$statement = $connect->prepare($query);

$statement->execute();

include '../cabecalho.php';

?>
<div class="container-fluid py-4" style="min-height: 700px;">
    <h1>Gestão de Pedidos de Livros</h1>
    <?php

    if (isset($_GET["action"])) {
        if ($_GET["action"] == 'add') {
    ?>
            <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
                <li class="breadcrumb-item"><a href="index.php">Painel de Controle</a></li>
                <li class="breadcrumb-item"><a href="requisitar_livro.php">Gestão de Pedidos de Livros</a></li>
                <li class="breadcrumb-item active">Requisitar Livro</li>
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
                            <i class="fas fa-user-plus"></i> Requisitar Livro
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="mb-3">
                                    <label class="form-label">Número ISBN</label>
                                    <input type="text" name="id_livro" id="id_livro" class="form-control" />
                                    <span id="book_isbn_result"></span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">ID Usuário</label>
                                    <input type="text" name="id_usuario" id="id_usuario" class="form-control" />
                                    <span id="unique_id_result"></span>
                                </div>
                                <div class="mt-4 mb-0">
                                    <input type="submit" name="issue_book_button" class="btn btn-success" value="Requisitar" />
                                </div>
                            </form>
                            <script>
                                var id_livro = document.getElementById('id_livro');

                                id_livro.onkeyup = function() {
                                    if (this.value.length > 2) {
                                        var form_data = new FormData();

                                        form_data.append('action', 'procurar_livro_isbn');

                                        form_data.append('request', this.value);

                                        fetch('accao.php', {
                                            method: "POST",
                                            body: form_data
                                        }).then(function(response) {
                                            return response.json();
                                        }).then(function(responseData) {
                                            var html = '<div class="list-group" style="position:absolute; width:93%">';

                                            if (responseData.length > 0) {
                                                for (var count = 0; count < responseData.length; count++) {
                                                    html += '<a href="#" class="list-group-item list-group-item-action"><span onclick="get_text(this)">' + responseData[count].numero_isbn + '</span> - <span class="text-muted">' + responseData[count].nome_livro + '</span></a>';
                                                }
                                            } else {
                                                html += '<a href="#" class="list-group-item list-group-item-action">Livro não encontrado!</a>';
                                            }

                                            html += '</div>';

                                            document.getElementById('book_isbn_result').innerHTML = html;
                                        });
                                    } else {
                                        document.getElementById('book_isbn_result').innerHTML = '';
                                    }
                                }

                                function get_text(event) {
                                    document.getElementById('book_isbn_result').innerHTML = '';

                                    document.getElementById('id_livro').value = event.textContent;
                                }

                                var id_usuario = document.getElementById('id_usuario');

                                id_usuario.onkeyup = function() {
                                    if (this.value.length > 2) {
                                        var form_data = new FormData();

                                        form_data.append('action', 'procurar_id_usuario');

                                        form_data.append('request', this.value);

                                        fetch('accao.php', {
                                            method: "POST",
                                            body: form_data
                                        }).then(function(response) {
                                            return response.json();
                                        }).then(function(responseData) {
                                            var html = '<div class="list-group" style="position:absolute;width:93%">';

                                            if (responseData.length > 0) {
                                                for (var count = 0; count < responseData.length; count++) {
                                                    html += '<a href="#" class="list-group-item list-group-item-action"><span onclick="get_text1(this)">' + responseData[count].unique_id + '</span> - <span class="text-muted">' + responseData[count].nome + '</span></a>';
                                                }
                                            } else {
                                                html += '<a href="#" class="list-group-item list-group-item-action">Usuário não encontrado!</a>';
                                            }
                                            html += '</div>';

                                            document.getElementById('unique_id_result').innerHTML = html;
                                        });
                                    } else {
                                        document.getElementById('unique_id_result').innerHTML = '';
                                    }
                                }

                                function get_text1(event) {
                                    document.getElementById('unique_id_result').innerHTML = '';

                                    document.getElementById('id_usuario').value = event.textContent;
                                }
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        } else if ($_GET["action"] == 'view') {
            $id_livro = converter_dados($_GET["code"], 'decrypt');

            if ($id_livro > 0) {
                $query = "
                SELECT * FROM requisitar_livro 
                WHERE id_livro = '$id_livro'
                ";

                $result = $connect->query($query);

                foreach ($result as $row) {
                    $query = "
                    SELECT * FROM livro 
                    WHERE numero_isbn = '" . $row["id_livro"] . "'
                    ";

                    $book_result = $connect->query($query);

                    $query = "
                    SELECT * FROM usuario 
                    WHERE unique_id = '" . $row["id_usuario"] . "'
                    ";

                    $user_result = $connect->query($query);

                    echo '
                    <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
                        <li class="breadcrumb-item"><a href="index.php">Painel de Controle</a></li>
                        <li class="breadcrumb-item"><a href="requisitar_livro.php">Gestão de Pedidos de Livros</a></li>
                        <li class="breadcrumb-item active">Visualizar Detalhes de Livros Requisitados</li>
                    </ol>
                    ';

                    if ($error != '') {
                        echo '<div class="alert alert-danger">' . $error . '</div>';
                    }

                    foreach ($book_result as $book_data) {
                        echo '
                        <h2>Book Details</h2>
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Número ISBN</th>
                                <td width="70%">' . $book_data["numero_isbn"] . '</td>
                            </tr>
                            <tr>
                                <th width="30%">Título do Livro</th>
                                <td width="70%">' . $book_data["nome"] . '</td>
                            </tr>
                            <tr>
                                <th width="30%">Autor</th>
                                <td width="70%">' . $book_data["autor"] . '</td>
                            </tr>
                        </table>
                        <br />
                        ';
                    }

                    foreach ($user_result as $user_data) {
                        echo '
                        <h2>User Details</h2>
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">ID Usuário</th>
                                <td width="70%">' . $user_data["unique_id"] . '</td>
                            </tr>
                            <tr>
                                <th width="30%">Nome do Usuário</th>
                                <td width="70%">' . $user_data["nome"] . '</td>
                            </tr>
                            <tr>
                                <th width="30%">Morada</th>
                                <td width="70%">' . $user_data["endereco"] . '</td>
                            </tr>
                            <tr>
                                <th width="30%">Contacto de Usuário.</th>
                                <td width="70%">' . $user_data["contacto"] . '</td>
                            </tr>
                            <tr>
                                <th width="30%">Endereço de Email do Usuário</th>
                                <td width="70%">' . $user_data["email"] . '</td>
                            </tr>
                            <tr>
                                <th width="30%">Foto de Usuário</th>
                                <td width="70%"><img src="' . base_url() . 'upload/' . $user_data["foto"] . '" class="img-thumbnail" width="100" /></td>
                            </tr>
                        </table>
                        <br />
                        ';
                    }

                    $estado = $row["estado_livro"];

                    $form_item = '';

                    if ($estado == "requisitado") {
                        $estado = '<span class="badge bg-warning">Requisitado</span>';

                        $form_item = '
                        <label><input type="checkbox" name="book_return_confirmation" value="Yes" /> Eu confirmo que recebi o Livro requisitado</label>
                        <br />
                        <div class="mt-4 mb-4">
                            <input type="submit" name="book_return_button" value="Devolver Livro" class="btn btn-primary" />
                        </div>
                        ';
                    }

                    if ($estado == 'nao_devolvido') {
                        $estado = '<span class="badge bg-danger">Não Devolvido</span>';

                        $form_item = '
                        <label><input type="checkbox" name="book_return_confirmation" value="Yes" /> Eu confirmo que recebi o livro requisitado</label><br />
                        <div class="mt-4 mb-4">
                            <input type="submit" name="book_return_button" value="Devolver Livro" class="btn btn-primary" />
                        </div>
                        ';
                    }

                    if ($estado == 'devolvido') {
                        $estado = '<span class="badge bg-primary">Devolvido</span>';
                    }

                    echo '
                    <h2>Detalhes do Livro Requisitado</h2>
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Data de Requisição de Livro</th>
                            <td width="70%">' . $row["data_requisicao"] . '</td>
                        </tr>
                        <tr>
                            <th width="30%">Data de Retorno de Livro</th>
                            <td width="70%">' . $row["data_retorno"] . '</td>
                        </tr>
                        <tr>
                            <th width="30%">Estado do Livro Requisitado</th>
                            <td width="70%">' . $estado . '</td>
                        </tr>
                        <tr>
                            <th width="30%">Total Multa</th>
                            <td width="70%">' . get_moeda($connect) . ' ' . $row["multas"] . '</td>
                        </tr>
                    </table>
                    <form method="POST">
                        <input type="hidden" name="id_livro" value="' . $id_livro . '" />
                        <input type="hidden" name="numero_isbn" value="' . $row["id_livro"] . '" />
                        ' . $form_item . '
                    </form>
                    <br />
                    ';
                }
            }
        }
    } else {
        ?>
        <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
            <li class="breadcrumb-item"><a href="index.php">Painel de Controle</a></li>
            <li class="breadcrumb-item active">Gestão de Pedidos de Livros</li>
        </ol>

        <?php
        if (isset($_GET['msg'])) {
            if ($_GET['msg'] == 'add') {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Livro requisitado com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }

            if ($_GET["msg"] == 'return') {
                echo '
            <div class="alert alert-success alert-dismissible fade show" role="alert">Livro devolvido a biblioteca com sucesso! <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            ';
            }
        }
        ?>

        <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <div class="col col-md-6">
                        <i class="fas fa-table me-1"></i> Gestão de Pedidos de Livros
                    </div>
                    <div class="col col-md-6" align="right">
                        <a href="requisitar_livro.php?action=add" class="btn btn-success btn-sm">Requisitar</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="datatablesSimple">
                    <thead>
                        <tr>
                            <th>Número ISBN</th>
                            <th>ID Usuário</th>
                            <th>Data de Requisição</th>
                            <th>Data de Retorno</th>
                            <th>Multa por atraso</th>
                            <th>Estado</th>
                            <th>Acção</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Número ISBN</th>
                            <th>ID Usuário</th>
                            <th>Data de Requisição</th>
                            <th>Data de Retorno</th>
                            <th>Multa por atraso</th>
                            <th>Estado</th>
                            <th>Acção</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        if ($statement->rowCount() > 0) {
                            $one_day_fine = get_multa_um_dia($connect);

                            $currency_symbol = get_moeda($connect);

                            set_fusohorario($connect);

                            foreach ($statement->fetchAll() as $row) {
                                $estado_livro = $row["estado_livro"];

                                $multas = $row["multas"];

                                if ($row["estado_livro"] == "Issue") {
                                    $current_date_time = new DateTime(get_data_temp($connect));
                                    $data_retorno_esperada = new DateTime($row["data_retorno_esperada"]);

                                    if ($current_date_time > $data_retorno_esperada) {
                                        $interval = $current_date_time->diff($data_retorno_esperada);

                                        $total_day = $interval->d;

                                        $multas = $total_day * $one_day_fine;

                                        $estado_livro = 'nao_devolvido';

                                        $query = "
        						UPDATE requisitar_livro 
													SET multas = '" . $multas . "', 
													estado_livro = '" . $estado_livro . "' 
													WHERE id_livro = '" . $row["id_livro"] . "'
        						";

                                        $connect->query($query);
                                    }
                                }

                                if ($estado_livro == 'requisitado') {
                                    $estado_livro = '<span class="badge bg-warning">Requisitado</span>';
                                }

                                if ($estado_livro == 'nao_devolvido') {
                                    $estado_livro = '<span class="badge bg-danger">Não Devolvido</span>';
                                }

                                if ($estado_livro == 'devolvido') {
                                    $estado_livro = '<span class="badge bg-primary">Devolvido</span>';
                                }

                                echo '
        				<tr>
        					<td>' . $row["id_livro"] . '</td>
        					<td>' . $row["id_usuario"] . '</td>
        					<td>' . $row["data_requisicao"] . '</td>
        					<td>' . $row["data_retorno"] . '</td>
        					<td>' . $multas . ' ' . $currency_symbol . '</td>
        					<td>' . $estado_livro . '</td>
        					<td>
                                <a href="requisitar_livro.php?action=view&code=' . converter_dados($row["id_livro"]) . '" class="btn btn-info btn-sm">Visualizar</a>
                            </td>
        				</tr>
        				';
                            }
                        } else {
                            echo '
        			<tr>
        				<td colspan="7" class="text-center">Nenhuma requisição efectuada!</td>
        			</tr>
        			';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php
    }
    ?>
</div>

<?php

include '../rodape.php';

?>