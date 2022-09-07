<?php 
require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\ContractsController;
use Core\FlashMessages\Flash;

(new ContractsController())->preview();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
	<div class="pagetitle">
		<h1>Exporter le contrat au format PDF</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
				<li class="breadcrumb-item"><a href="<?= VIEWS . "Contracts" ?>">Contrats</a></li>
				<li class="breadcrumb-item active">Exporter en PDF</li>
			</ol>
		</nav>
	</div><!-- End Page Title -->

    <section class="section dashboard">
		<div class="row">
			<div class="col-md-12 col-xl-12 col-xxl-10">

                <?= Flash::render() ?>

                <div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
                    <span class="bi bi-info-circle flex-shrink-0 me-2" role="img" aria-label="Info:"></span>
                    <div>
                        Verifiez si tout est en ordre, appliquez des modifications si nécessaire.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>

                <form class="row g-3 needs-validation" action="exportpdf.php" method="post" novalidate>
                    <div class="mb-3">
                        <label for="ccontent" class="form-label">Contrat <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="content" id="ccontent" rows="3" required><?= isset($form_data['content']) ? $form_data['content'] : $preview; ?></textarea>
                        <div class="invalid-feedback">Le contenu du contrat ne peut être vide.</div>
                    </div>

                    <input type="hidden" name="contract_id" value="<?= $contract->getId() ?>">

                    <div class="text-center">
                        <input type="submit" class="btn btn-primary" name="export_contract" value="Exporter">
                        <button type="button" onclick="javascript:history.back()" class="btn btn-secondary">Annuler</button>
                    </div>

                </form>

            </div>
        </div>
    </section>
</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>

<script src="<?= TEMPLATE_PATH ?>assets/vendor/tinymce/tinymce.min.js"></script>

<script type="text/javascript">

tinymce.init({
    selector: '#ccontent',
    language: 'fr',
    plugins: "advlist table preview autolink visualblocks visualchars fullscreen link charmap pagebreak nonbreaking anchor insertdatetime lists wordcount help quickbars emoticons",
    menubar: true,
    quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
    noneditable_noneditable_class: "mceNonEditable",
    toolbar_drawer: 'sliding',
    contextmenu: false,
    menubar: 'file edit view insert format tools table help',
    toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | ltr rtl',
    toolbar_sticky: true,
    browser_spellcheck: true,
    height: 500,
});
</script>