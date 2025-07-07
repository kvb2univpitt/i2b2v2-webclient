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
        <link rel="stylesheet" href="js-ext/bootstrap/5.3.3/css/bootstrap.min.css" />
        <script>
            if (window.opener) {
                window.opener.i2b2.PM.ctrlr.SamlLogin("<?php echo $_SESSION["eppn"]; ?>", "<?php echo $_SESSION["shib-session-id"]; ?>", true);
            }
        </script>
    </head>
    <body>
        <header class="p-3" style="background-color: #3560a0;">
            <div class="container">
                <div class="d-flex flex-wrap justify-content-lg-start">
                    <a href="https://www.globus.org/" class="d-flex align-items-center mb-lg-0 text-decoration-none text-white">
                        <img src="assets/images/sso/globus.svg" />
                        <strong>Globus</strong>
                    </a>
                </div>
            </div>
        </header>
        <main>
            <section class="py-5 text-center container">
                <div class="row py-lg-5">
                    <div class="col-lg-6 col-md-8 mx-auto">
                        <h1 class="fw-light">Sign In With Globus</h1>
                        <?php if (isset($error_msg)) { ?>
                            <div class="alert alert-danger" role="alert">
                                <h3><?php echo $error_msg['error']; ?></h3>
                            </div>
                        <?php } else { ?>
                            <h3 class="alert alert-success" role="alert">Login Success!</h3>
                            <p class="lead">Please close this window and click the "Sign in with Globus" button again to log in.</p>
                        <?php } ?>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
