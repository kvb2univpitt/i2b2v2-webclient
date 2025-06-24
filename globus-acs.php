<?php
session_start();

$error_msg;
if (isset($_SESSION['error_msg'])) {
    $error_msg = $_SESSION['error_msg'];
    unset($_SESSION['error_msg']);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>i2b2 Web Client</title>
        <link rel="stylesheet" href="js-ext/bootstrap/5.0.2/css/bootstrap.min.css" />
    </head>
    <body class="d-flex justify-content-center py-4 bg-body-tertiary">
        <main>
            <?php if (isset($error_msg)) { ?>
                <div class="alert alert-danger" role="alert">
                    <h1><?php echo $error_msg['error']; ?></h1>
                </div>
            <?php } else { ?>
                <h3 class="alert alert-success" role="alert">Login Success!</h3>
                <p>Please close this window and click the "Sign in with Globus" button again to log in.</p>
                <script>
                    if (window.opener) {
                        window.opener.i2b2.PM.ctrlr.SamlLogin("<?php echo $_SESSION["eppn"]; ?>", "<?php echo $_SESSION["shib-session-id"]; ?>", true);
                    }
                </script>
            <?php } ?>
        </main>
    </body>
</html>
