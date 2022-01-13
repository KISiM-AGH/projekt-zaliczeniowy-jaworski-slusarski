<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Rejestracja</title>
        <!-- Bootstrap core CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link href="<?= base_url(); ?>assets/css/login.css" rel="stylesheet">
    </head>

    <body class="text-center">
        <main class="form-signin">
        <?= form_open_multipart(); ?>
            <img class="mb-4" src="<?= base_url(); ?>assets/img/agh.png" alt="" width="256">
            <h1 class="h3 mb-3 fw-normal">Rejestracja</h1>
            <?php
                if($secret)
                {
                    echo '<p>Dziękujemy za rejestrację, Twój secret key do 2FA to: <b>' . $secret . '</b></p>';
                    echo '<p><img src="' . $qrcode . '"></p>';
                }
                if($error)
                {
                    echo '<p style="color: red;">' . $error . '</p>';
                }
            ?>

            <div class="form-floating">
            <input type="text" class="form-control" id="login" name="login" placeholder="Nazwa użytkownika">
            <label for="login">Nazwa użytkownika</label>
            </div>
            <div class="form-floating">
            <input type="password" class="form-control" id="pin" name="pin" placeholder="Kod dostępu" maxlength="4">
            <label for="pin">Kod dostępu</label>
            </div>
            <input type="submit" class="w-100 btn btn-lg btn-primary" value="Załóż konto" name="register">
            <a href="<?= base_url(); ?>logowanie" class="w-100 btn btn-lg btn-secondary mt-3">Wróć do logowania</a>
            <p class="mt-5 mb-3 text-muted">&copy; MJ & IŚ</p>
        <?= form_close(); ?>
        </main>
    </body>
</html>