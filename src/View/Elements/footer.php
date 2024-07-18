<!-- ======= Footer ======= -->
<footer id="footer" class="footer">
	<div class="copyright">
		&copy; Copyright <strong><span>Silevester D.</span> 2022 <span id="todayYear"></span></strong>. All Rights Reserved
	</div>
	<div class="credits">
		Developed by <a target="_blank" href="https://github.com/SilverD3/">Silevester D.</a>
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
	const logoutbtn = document.getElementById('logoutBtn');

	const todayYear = document.querySelector("#todayYear");
	const year = (new Date()).getFullYear();
	todayYear.innerText = " - " + year;

	if (logoutbtn !== null) {
		logoutbtn.addEventListener('click', function(){
			if (confirm("Voulez-vous fermer votre session ?")) {
				location.href = "<?= VIEWS . 'Auth/logout.php' ?>";
			}
		});
	}

</script>

</body>

</html>