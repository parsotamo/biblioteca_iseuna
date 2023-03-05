                <?php
                if (is_admin_entrada()) {
                ?>
                    </main>
                    <footer class="py-4 bg-color mt-auto">
                        <div class="container-fluid px-4">
                            <div class="d-flex align-items-center justify-content-between small">
                                <div class="text-muted">Direitos do Autor &copy; Gestão de Biblioteca ISEUNA desenvolvido por Nicodemos Afonso Elias <?php echo date('Y'); ?></div>
                                <div>
                                    <a href="#">Politicas de Privacidade</a>
                                    &middot;
                                    <a href="#">Termos &amp; Condições</a>
                                </div>
                            </div>
                        </div>
                    </footer>
                    </div>
                    </div>
                <?php
                } else {
                ?>
                    <footer class="pt-3 mt-4 text-muted text-center border-top">
                        &copy; <?php echo date('Y'); ?>
                    </footer>
                    </div>
                    </main>
                <?php
                }
                ?>
                <script src="<?php echo base_url() ?>asset/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
                <script src="<?php echo base_url() ?>asset/js/scripts.js"></script>
                <script src="<?php echo base_url() ?>asset/js/simple-datatables@latest.js" crossorigin="anonymous"></script>
                <script src="<?php echo base_url() ?>asset/js/datatables-simple-demo.js"></script>

                </body>

                </html>