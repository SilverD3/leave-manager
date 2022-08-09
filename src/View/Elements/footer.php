<!-- ======= Footer ======= -->
<footer id="footer" class="footer">
<div class="copyright">
	&copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
</div>
<div class="credits">
	Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
</div>
</footer>
<!-- End Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="<?= TEMPLATE_PATH ?>assets/vendor/apexcharts/apexcharts.min.js"></script>
<script src="<?= TEMPLATE_PATH ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= TEMPLATE_PATH ?>assets/vendor/simple-datatables/simple-datatables.js"></script>

<!-- Template Main JS File -->
<script src="<?= TEMPLATE_PATH ?>assets/js/main.js"></script>

<script type="text/javascript">
	var logoutbtn = document.getElementById('logoutBtn');

	if (logoutbtn !== null) {
		logoutbtn.addEventListener('click', function(){
			if (confirm("Voulez-vous fermer votre session ?")) {
				location.href = "<?= VIEWS . 'Auth' . DS . 'logout.php' ?>";
			}
		});
	}

</script>

</body>

</html>