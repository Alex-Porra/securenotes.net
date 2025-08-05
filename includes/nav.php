    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo APP_URL; ?>">
                <img src="<?php echo APP_URL; ?>/assets/SecureNotes-Logo-md.png" class="brand">
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo APP_URL; ?>#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo APP_URL; ?>#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo APP_URL; ?>/faq/">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo APP_URL; ?>/blog/">
                            Blog
                            <div class="pull-right" style="animation: pulse 2s infinite;float:right">
                                <i class="bi bi-circle-fill text-success align-middle ms-1 opacity-75 pulse-animation" style="animation: pulse 2s infinite;"></i>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link btn btn-primary text-white navBtn" style="padding: 0.5rem 1rem !important;" href="<?php echo APP_URL; ?>/#create-note">
                            <i class="bi bi-shield-lock me-2"></i>
                            Create Note
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link btn btn-primary text-white navBtn donateBtn" style="padding: 0.5rem 1rem !important;" href="<?php echo APP_URL; ?>/donate">
                            <img src="https://cdn.buymeacoffee.com/widget/assets/coffee%20cup.svg" alt="Buy Me A Coffee" style="margin-right:8px !important; height: 20px; width: 20px; margin: 0; padding: 0;">
                            Donate
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>