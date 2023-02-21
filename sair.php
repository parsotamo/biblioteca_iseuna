<?php

//sair.php

session_start();

session_destroy();

header('location:usuario_entrada.php');
