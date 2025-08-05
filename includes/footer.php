<footer class="bg-dark text-white py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <a href="<?php echo APP_URL; ?>" class="col-md-4 d-flex mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
                    <img src="<?php echo APP_URL; ?>/assets/SecureNotes-Logo-white.png" class="footerbrand">
                </a>
                <p class="text-white mt-3">Secure note sharing made simple.</p>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h6>Product</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?php echo APP_URL; ?>/#create-note">Get Started</a></li>
                    <li><a href="<?php echo APP_URL; ?>/#security">Security</a></li>
                    <li><a href="<?php echo APP_URL; ?>/#how-it-works">How It Works</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h6>Insights</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?php echo APP_URL; ?>/faq/">FAQ</a></li>
                    <li><a href="<?php echo APP_URL; ?>/stats/">Stats</a></li>
                    <li><a href="<?php echo APP_URL; ?>/api-docs/">API Docs</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h6>Legal</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?php echo APP_URL; ?>/privacy/">Privacy Policy</a></li>
                    <li><a href="<?php echo APP_URL; ?>/terms/">Terms of Service</a></li>
                    <li><a href="<?php echo APP_URL; ?>/cookies/">Cookie Policy</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h6>Connect</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="/donate" class="donateBtnFooter"><i class="bi bi-cup-hot-fill me-2"></i>Donate</a></li>
                    <li><a href="<?php echo APP_URL; ?>/blog/"><i class="bi bi-journal-text me-2"></i>Blog</a></li>
                    <li><a href="#"><i class="bi bi-github me-2"></i>GitHub</a></li>
                </ul>
            </div>
        </div>
        <hr class="my-4" style="border-color: #505964ff;">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0 text-white">&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0 text-white">Made with <i class="bi bi-heart-fill text-danger"></i> for your privacy</p>
            </div>
        </div>
    </div>
</footer>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="<?php echo APP_URL; ?>/assets/js/app.js"></script>
<script data-name="BMC-Widget" data-cfasync="false" src="https://cdnjs.buymeacoffee.com/1.0.0/widget.prod.min.js" data-id="porra" data-description="Support me on Buy me a coffee!" data-message="" data-color="#667eea" data-position="Right" data-x_margin="18" data-y_margin="18"></script>

<script>
    document.querySelector(".donateBtn").addEventListener("click", function(e) {
        e.preventDefault();
        document.getElementById("bmc-wbtn").click();
    });
    document.querySelector(".donateBtnFooter").addEventListener("click", function(e) {
        e.preventDefault();
        document.getElementById("bmc-wbtn").click();
    });
</script>