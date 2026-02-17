<?php
class VenteController extends Controller {
    private $venteService;
    private $donService;
    private $validationService;
    public function __construct() {
        $this->venteService = ServiceContainer::getVenteService();
        $this->donService = ServiceContainer::getDonService();
        $this->validationService = ServiceContainer::getValidationService();
    }
    public function index() {
        try {
            $stocks = $this->venteService->getStocksDisponibles();
            $dons = $this->donService->getDonsNonUtilises();
            $taux_change = $this->venteService->getTauxChange();
            $stats = $this->venteService->getSalesStats();
            $ventes = $this->venteService->getAllVentes();
            $sales_by_category = $this->venteService->getSalesByCategory();
            $data = [
                'stocks' => $stocks,
                'dons' => $dons,
                'taux_change' => $taux_change,
                'stats' => $stats,
                'ventes' => $ventes,
                'sales_by_category' => $sales_by_category,
                'success_message' => isset($_SESSION['vente_success']) ? $_SESSION['vente_success'] : null,
                'error_message' => isset($_SESSION['vente_error']) ? $_SESSION['vente_error'] : null
            ];
            unset($_SESSION['vente_success']);
            unset($_SESSION['vente_error']);
            $this->view('vente', $data);
        } catch (Exception $e) {
            $data = [
                'stocks' => [],
                'dons' => [],
                'taux_change' => 0.10,
                'stats' => null,
                'ventes' => [],
                'sales_by_category' => [],
                'error_message' => $e->getMessage(),
                'success_message' => null
            ];
            $this->view('vente', $data);
        }
    }
    public function vendre() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/ventes');
            return;
        }
        try {
            $don_id = isset($_POST['don_id']) && !empty($_POST['don_id']) ? (int)$_POST['don_id'] : null;
            $produit_id = isset($_POST['produit_id']) ? (int)$_POST['produit_id'] : null;
            $quantite_vendue = isset($_POST['quantite_vendue']) ? (float)$_POST['quantite_vendue'] : 0;
            $prix_unitaire_reference = isset($_POST['prix_unitaire_reference']) ? (float)$_POST['prix_unitaire_reference'] : 0;
            $acheteur = isset($_POST['acheteur']) && !empty($_POST['acheteur']) ? trim($_POST['acheteur']) : null;
            $notes = isset($_POST['notes']) && !empty($_POST['notes']) ? trim($_POST['notes']) : null;
            if (!$produit_id || $quantite_vendue <= 0) {
                throw new Exception('Veuillez sélectionner un produit et saisir une quantité valide');
            }
            $result = $this->venteService->vendreProduct(
                $don_id,
                $produit_id,
                $quantite_vendue,
                $prix_unitaire_reference,
                $acheteur,
                $notes
            );
            $_SESSION['vente_success'] = sprintf(
                'Vente réussie! Montant: %s Ar (Taux de dépréciation: %d%%)',
                number_format($result['montant_total'], 0, ',', ' '),
                $result['taux_depreciation'] * 100
            );
            $this->redirect('/ventes');
        } catch (Exception $e) {
            $_SESSION['vente_error'] = $e->getMessage();
            $this->redirect('/ventes');
        }
    }
    public function config() {
        try {
            $configuration = $this->venteService->getConfiguration();
            $data = [
                'configuration' => $configuration,
                'success_message' => isset($_SESSION['config_success']) ? $_SESSION['config_success'] : null,
                'error_message' => isset($_SESSION['config_error']) ? $_SESSION['config_error'] : null
            ];
            unset($_SESSION['config_success']);
            unset($_SESSION['config_error']);
            $this->view('config_vente', $data);
        } catch (Exception $e) {
            $_SESSION['config_error'] = $e->getMessage();
            $this->redirect('/ventes/config');
        }
    }
    public function updateConfig() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/ventes/config');
        }
        try {
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'config_') === 0) {
                    $param_key = substr($key, 7);
                    $this->venteService->updateConfiguration($param_key, trim($value));
                }
            }
            $_SESSION['config_success'] = 'Configuration mise à jour avec succès!';
            $this->redirect('/ventes/config');
        } catch (Exception $e) {
            $_SESSION['config_error'] = $e->getMessage();
            $this->redirect('/ventes/config');
        }
    }
    public function checkProduct() {
        header('Content-Type: application/json');
        try {
            $produit_id = isset($_GET['produit_id']) ? (int)$_GET['produit_id'] : null;
            if (!$produit_id) {
                echo json_encode(['error' => 'ID produit manquant']);
                return;
            }
            $can_sell = $this->venteService->canSellProduct($produit_id);
            if (!$can_sell) {
                $needs = $this->venteService->getActiveNeedsForProduct($produit_id);
                echo json_encode([
                    'can_sell' => false,
                    'needs' => $needs,
                    'message' => 'Ce produit est encore en demande'
                ]);
                return;
            }
            echo json_encode([
                'can_sell' => true,
                'message' => 'Produit disponible pour la vente'
            ]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
