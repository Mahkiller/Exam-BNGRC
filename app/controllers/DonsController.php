<?php
class DonsController extends Controller {
    private $donService;
    private $validationService;
    public function __construct() {
        $this->donService = ServiceContainer::getDonService();
        $this->validationService = ServiceContainer::getValidationService();
    }
    public function index() {
        $stats_raw = $this->donService->getStatsDonateurs();
        $stats_donateurs = [];
        foreach ($stats_raw as $stat) {
            $stats_donateurs[$stat['type_donateur']] = $stat['nombre_dons'];
        }
        $data = [
            'dons' => $this->donService->getAllDons(),
            'stock' => $this->donService->getStockGlobal(),
            'stock_detail' => $this->donService->getStockDetaille(),
            'stats_donateurs' => $stats_donateurs,
            'top_donateurs' => $this->donService->getTopDonateurs(),
            'categories' => $this->donService->getCategories(),
            'produits' => $this->donService->getProduits()
        ];
        $this->view('dons', $data);
    }
    public function attribution() {
        $categories = $this->donService->getCategories();
        $produits = $this->donService->getProduits();
        $besoins = $this->donService->getBesoinsNonSatisfaits();
        $dons_disponibles = $this->donService->getDonsDisponibles();
        $totaux = [];
        foreach ($produits as $produit) {
            $catId = $produit['categorie_id'];
            if (!isset($totaux[$catId])) {
                $totaux[$catId] = [
                    'nom_categorie' => $produit['nom_categorie'],
                    'total_stock' => 0,
                    'unite' => $produit['unite_mesure']
                ];
            }
            $totaux[$catId]['total_stock'] += $produit['stock_actuel'];
        }
        $argent_total = $this->donService->getStockGlobal()['argent'] ?? 0;
        $categorie_argent = array_filter($categories, function($c) { 
            return $c['nom_categorie'] === 'argent'; 
        });
        if (!empty($categorie_argent)) {
            $catId = reset($categorie_argent)['id'];
            $totaux[$catId] = [
                'nom_categorie' => 'argent',
                'total_stock' => $argent_total,
                'unite' => 'Ar'
            ];
        }
        $data = [
            'besoins_non_satisfaits' => $besoins,
            'stock' => $this->donService->getStockGlobal(),
            'dons_disponibles' => $dons_disponibles,
            'categories' => $categories,
            'produits' => $produits,
            'totaux' => $totaux
        ];
        $this->view('attribution', $data);
    }
    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $donateur = $_POST['donateur'] ?? '';
                $type_don = $_POST['type_don'] ?? '';
                if ($type_don === 'argent') {
                    $quantite = $_POST['quantite_argent'] ?? 0;
                    if ($quantite <= 0) {
                        throw new Exception('Le montant doit être positif');
                    }
                    $result = $this->donService->ajouterDon(
                        $donateur, 
                        'argent', 
                        'Don en argent', 
                        $quantite, 
                        'Ariary', 
                        null
                    );
                } else if ($type_don === 'produit') {
                    $produit_id = $_POST['produit_id'] ?? null;
                    $quantite = $_POST['quantite_produit'] ?? 0;
                    if ($quantite <= 0) {
                        throw new Exception('La quantité doit être positive');
                    }
                    if (!$produit_id) {
                        throw new Exception('Veuillez sélectionner un produit');
                    }
                    $produit = $this->donService->getProduitInfo($produit_id);
                    if (!$produit) {
                        throw new Exception('Produit introuvable');
                    }
                    $result = $this->donService->ajouterDon(
                        $donateur,
                        $produit['nom_categorie'],
                        $produit['nom_produit'],
                        $quantite,
                        $produit['unite_mesure'],
                        $produit_id
                    );
                } else {
                    throw new Exception('Type de don invalide');
                }
                if ($result['success']) {
                    $_SESSION['message'] = '✅ Don enregistré avec succès';
                } else {
                    $_SESSION['error'] = $result['message'];
                }
            } catch (Exception $e) {
                $_SESSION['error'] = 'Erreur: ' . $e->getMessage();
            }
            $this->redirect('dons');
        }
    }
    public function attribuer() {
        header('Content-Type: application/json');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
                return;
            }
            $besoin_id = $_POST['besoin_id'] ?? null;
            $don_id = $_POST['don_id'] ?? null;
            $quantite = $_POST['quantite'] ?? 0;
            $type = $_POST['type'] ?? null;
            if (!$besoin_id || !$quantite || !$type) {
                echo json_encode(['success' => false, 'message' => 'Données manquantes']);
                return;
            }
            $validation = $this->validationService->validerAttribution($type, $quantite);
            if (!$validation['valide']) {
                echo json_encode(['success' => false, 'message' => $validation['message']]);
                return;
            }
            $result = $this->donService->attribuerDon($besoin_id, $don_id, $quantite, $type);
            echo json_encode($result);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
        }
    }
}
