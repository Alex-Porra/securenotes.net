<?php
// 403.php - Access Forbidden
if (basename($_SERVER['PHP_SELF']) == '403.php') {
    require_once 'config/config.php';
    http_response_code(403);
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo APP_NAME; ?> - Access Forbidden</title>
        <meta name="robots" content="noindex, nofollow">
        <?php include "./includes/head.php" ?>

        <style>
            body.custom-body {
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%) !important;
                min-height: 100vh !important;
            }
        </style>
    </head>

    <body class="custom-body">
        <?php include "./includes/nav.php" ?>


        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center p-5">
                            <div class="mb-4">
                                <i class="bi bi-shield-x display-1 text-danger"></i>
                            </div>
                            <h1 class="h2 mb-3">Access Forbidden</h1>
                            <p class="text-muted mb-4">You don't have permission to access this resource.</p>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                <a href="/" class="btn btn-primary">
                                    <i class="bi bi-house me-2"></i>Go Home
                                </a>
                                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Go Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

    </html>
<?php
}
?>