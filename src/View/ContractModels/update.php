<?php 
require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\ContractModelsController;
use Core\FlashMessages\Flash;

(new ContractModelsController())->update();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
	<div class="pagetitle">
		<h1>Editer un modèle de contrat</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
				<li class="breadcrumb-item"><a href="<?= VIEWS . 'ContractModels' ?>">Modèles de contrat</a></li>
				<li class="breadcrumb-item active">Edition</li>
			</ol>
		</nav>
	</div><!-- End Page Title -->

	<section class="section dashboard">
		<div class="row">
			<div class="col-md-12 col-xl-12 col-xxl-10">
				<div class="card">
					<div class="card-body">
			  			<h5 class="card-title">Remplissez les champs ci-après pour mettre à jour le modèle de contrat</h5>
                        <?= Flash::render() ?>

                        <!-- Help block -->
                        <div class="row mt-2 mb-3">
                            <div class="col-md-12">
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#helpModal">
                                    <i class="bi bi-question-circle"></i> Aide
                                </button>

                                <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#legendCollapse" aria-expanded="false" aria-controls="legendCollapse">
                                    <i class="bi bi-lightbulb"></i> Légende
                                </button>

                                <div class="collapse mt-3" id="legendCollapse">
                                    <div class="pt-3 mb-3 border card card-body table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th> Mots clés </th>
                                                    <th> Reférénces </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td> $_company_name <a href="#" title="Copier" class="link float-end me-3" onclick="copyToClipboard('$_company_name')"><i class="bi bi-clipboard"></i></a></td>
                                                    <td> Nom de l'entreprise </td>
                                                </tr>
                                                <tr>
                                                    <td> $_company_address <a href="#" title="Copier" class="link float-end me-3" onclick="copyToClipboard('$_company_address')"><i class="bi bi-clipboard"></i></a></td>
                                                    <td> Adresse de l'entreprise </td>
                                                </tr>
                                                <tr>
                                                    <td> $_employer_name <a href="#" title="Copier" class="link float-end me-3" onclick="copyToClipboard('$_employer_name')"><i class="bi bi-clipboard"></i></a></td>
                                                    <td> Nom complet de l'employeur </td>
                                                </tr>
                                                <tr>
                                                    <td> $_candidate_name <a href="#" title="Copier" class="link float-end me-3" onclick="copyToClipboard('$_candidate_name')"><i class="bi bi-clipboard"></i></a></td>
                                                    <td> Nom complet de l'employé </td>
                                                </tr>
                                                <tr>
                                                    <td> $_candidate_birth <a href="#" title="Copier" class="link float-end me-3" onclick="copyToClipboard('$_candidate_birth')"><i class="bi bi-clipboard"></i></a></td>
                                                    <td> Date de naissance de l'employé </td>
                                                </tr>
                                                <tr>
                                                    <td> $_candidate_baddress <a href="#" title="Copier" class="link float-end me-3" onclick="copyToClipboard('$_candidate_baddress')"><i class="bi bi-clipboard"></i></a></td>
                                                    <td> Lieu de naissance de l'employé </td>
                                                </tr>
                                                <tr>
                                                    <td> $_candidate_nationality <a href="#" title="Copier" class="link float-end me-3" onclick="copyToClipboard('$_candidate_nationality')"><i class="bi bi-clipboard"></i></a></td>
                                                    <td> Nationalité de l'employé </td>
                                                </tr>
                                                <tr>
                                                    <td> $_candidate_nss <a href="#" title="Copier" class="link float-end me-3" onclick="copyToClipboard('$_candidate_nss')"><i class="bi bi-clipboard"></i></a></td>
                                                    <td> Numéro de sécurité sociale de l'employé </td>
                                                </tr>
                                                <tr>
                                                    <td> $_candidate_address <a href="#" title="Copier" class="link float-end me-3" onclick="copyToClipboard('$_candidate_address')"><i class="bi bi-clipboard"></i></a></td>
                                                    <td> Adresse de résidence de l'employé </td>
                                                </tr>
                                                <tr>
                                                    <td> $_job_start_date <a href="#" title="Copier" class="link float-end me-3" onclick="copyToClipboard('$_job_start_date')"><i class="bi bi-clipboard"></i></a></td>
                                                    <td>Date à partir de laquelle le contrat est effectif </td>
                                                </tr>
                                                <tr>
                                                    <td> $_job_end_date <a href="#" title="Copier" class="link float-end me-3" onclick="copyToClipboard('$_job_end_date')"><i class="bi bi-clipboard"></i></a></td>
                                                    <td>Date de fin du contrat </td>
                                                </tr>
                                                <tr>
                                                    <td> $_job_object <a href="#" title="Copier" class="link float-end me-3" onclick="copyToClipboard('$_job_object')"><i class="bi bi-clipboard"></i></a></td>
                                                    <td> Objet de l'offre </td>
                                                </tr>
                                                <tr>
                                                    <td> $_job_description <a href="#" title="Copier" class="link float-end me-3" onclick="copyToClipboard('$_job_description')"><i class="bi bi-clipboard"></i></a></td>
                                                    <td> Missions / Description des tâches </td>
                                                </tr>
                                                <tr>
                                                    <td> $_job_delay <a href="#" title="Copier" class="link float-end me-3" onclick="copyToClipboard('$_job_delay')"><i class="bi bi-clipboard"></i></a></td>
                                                    <td> Durée du contrat </td>
                                                </tr>
                                                <tr>
                                                    <td> $_job_salary <a href="#" title="Copier" class="link float-end me-3" onclick="copyToClipboard('$_job_salary')"><i class="bi bi-clipboard"></i></a></td>
                                                    <td> Salaire de l'employé </td>
                                                </tr>
                                                <tr>
                                                    <td> $_hourly_rate <a href="#" title="Copier" class="link float-end me-3" onclick="copyToClipboard('$_hourly_rate')"><i class="bi bi-clipboard"></i></a></td>
                                                    <td> Taux horaire </td>
                                                </tr>
                                                <tr>
                                                    <td> $_generated_date <a href="#" title="Copier" class="link float-end me-3" onclick="copyToClipboard('$_generated_date')"><i class="bi bi-clipboard"></i></a></td>
                                                    <td> L'heure à laquelle le contrat est généré </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Modal -->
                                <div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info">
                                                <h4 class="modal-title" id="helpModalLabel">Comment rediger un modèle de contrat ?</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                L'editeur vous permet de saisir le contenu de votre modèle et d'y appliquer des mises en forme. Pour les informations devant être complétées lors de la génération des contrats, utilisez des "placeholders" qui sont documentés dans la légende.
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-check"></i> Compris</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div><!-- Help block end -->

			  			<form class="row g-3 needs-validation" action="" method="post" novalidate>
                            <div class="mb-1"> 
                                <label for="cmname" class="form-label">Nom du modèle <span class="text-danger">*</span></label>
                                <input type="text" maxlength="255" class="form-control" name="name" id="cmname" value="<?= isset($form_data['name']) ? $form_data['name'] : $contractModel->getName() ?>" required>
                                <div class="invalid-feedback">Veuillez renseigner le nom du modèle de contrat.</div>
                            </div>

                            <div class="col-sm-6">
								<label for="cmctid" class="form-label"> Type de contrat <span class="text-danger">*</span></label>
                                <?php if(empty($contract_types)):?>
                                    <div class="alert alert-danger">
                                        Aucun type de contrat trouvé.
                                    </div>
                                <?php else:?>
								    <select id="cmctid" class="form-select" disabled required>
                                        <option selected value="">Choisir un type de contrat</option>
										<?php foreach($contract_types as $ctype): ?>
											<option <?= (isset($form_data['contract_type_id']) && $form_data['contract_type_id'] == $ctype->getId()) ? 'selected': (($contractModel->getContractTypeId() == $ctype->getId()) ? 'selected' : '') ?> value="<?= $ctype->getId() ?>">
												<?= $ctype->getName();?>
											</option>
										<?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Veuillez choisir un type de contrat.</div>
                                <?php endif;?>
							</div>

                            <div class="mb-3">
                                <label for="cmcontent" class="form-label">Contenu du modèle <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="content" id="cmcontent" rows="3" required><?= isset($form_data['content']) ? $form_data['content'] : $contractModel->getContent(); ?></textarea>
                                <div class="invalid-feedback">Veuillez renseigner le contenu du modèle de contrat.</div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" <?= isset($form_data['is_current']) && $form_data['is_current'] ? 'checked' : ($contractModel->getIsCurrent() ? 'checked' : '') ?> type="checkbox" name="is_current" value="1" id="cmiscurrent">
                                    <label class="form-check-label" for="cmiscurrent">Définir comme modèle par défaut</label>
                                </div>
                            </div>

                            <div class="text-center">
                                <input type="submit" class="btn btn-primary" name="update_contract_model" value="Enregistrer">
                                <button type="button" onclick="javascript:history.back()" class="btn btn-secondary">Annuler</button>
                            </div>

						</form>

					</div>
		  		</div>
			</div>

		</div>

	</section>

</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>

<script src="<?= TEMPLATE_PATH ?>assets/vendor/tinymce/tinymce.min.js"></script>
<script type="text/javascript">

tinymce.init({
    selector: '#cmcontent',
    language: 'fr',
    plugins: "advlist table preview searchreplace autolink visualblocks visualchars fullscreen link charmap pagebreak nonbreaking anchor insertdatetime lists wordcount help quickbars emoticons",
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

function fallbackCopyTextToClipboard(text) {
    var textArea = document.createElement("textarea");
    textArea.value = text;

    // Avoid scrolling to bottom
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";

    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        var successful = document.execCommand('copy');
    } catch (err) {
        console.error('Fallback: Oops, unable to copy', err);
    }

    document.body.removeChild(textArea);
}

function copyToClipboard(text) {
    if (!navigator.clipboard) {
        fallbackCopyTextToClipboard(text);
        return;
    }
    navigator.clipboard.writeText(text).then(function() {
        //console.log('Async: Copying to clipboard was successful!');
    }, function(err) {
        console.error('Async: Could not copy text: ', err);
    });
}

</script>