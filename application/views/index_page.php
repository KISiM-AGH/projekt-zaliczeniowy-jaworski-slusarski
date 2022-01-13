<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Strefa użytkownika</title>
        <!-- Bootstrap core CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link href="<?= base_url(); ?>assets/css/authorized.css" rel="stylesheet">
    </head>

    <body class="d-flex text-center text-white bg-dark">
        <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
            <header class="mb-auto">
                <div>
                    <h3 class="float-md-start mb-0">Bezpieczeństwo sieciowe</h3>
                    <nav class="nav nav-masthead justify-content-center float-md-end">
                        <a class="nav-link" href="<?= base_url(); ?>wyloguj">Wyloguj się</a>
                    </nav>
                </div>
            </header>

            <main class="px-3">
                <h1>Autoryzacja pomyślna.</h1>
                <h3>Twoje ostatnie logowanie: <?= $last_login->last_login; ?></h3>
                <p class="lead">
                    <a href="<?= base_url(); ?>wyloguj" class="btn btn-lg btn-secondary fw-bold border-white bg-white">Wyloguj się</a>
                </p>
            </main>

            <div>
                <table class="table table-striped table-dark" style="height: 100px !important; overflow: scroll">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Czas logowania</th>
                            <th scope="col">Adres IP</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if($logs) {
                                $i = 1;
                                foreach($logs as $log) {
                        ?>
                            <tr <?php if($log->status == 0) { echo 'style="font-weight: bold;"'; } ?>>
                                <th scope="row"><?= $i; ?></th>
                                <td><?= $log->time ?></td>
                                <td><?= $log->ip ?></td>
                                <td>
                                    <?php
                                        if($log->status == 0)
                                            echo 'zalogowany pomyślnie';
                                        if($log->status == 1)
                                            echo 'błędny PIN';
                                        if($log->status == 2)
                                            echo 'błędny kod 2FA';
                                        if($log->status == 3)
                                            echo 'błędna captcha';
                                        if($log->status == 4)
                                            echo 'kilkukrotnie błędny PIN';
                                    ?>
                                </td>
                            </tr>
                        <?php
                                    $i++;
                                }
                            }
                        ?>
                        
                    </tbody>
                </table>
            </div>

            <footer class="mt-auto text-white-50">
                <p>MJ & IŚ</p>
            </footer>
        </div>
    </body>
</html>
