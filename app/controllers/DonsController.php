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
        // Obtenir toutes les catégories et produits pour affichage du stock
        $categories = $this->donService->getCategories();
        $produits = $this->donService->getProduits();
        
        // Récupérer les données d'attribution
        $besoins = $this->donService->getBesoinsNonSatisfaits();
        $dons_disponibles = $this->donService->getDonsDisponibles();
        
        // Calculer les totaux par catégorie
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
        
        // Ajouter l'argent aux totaux
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
    
    // MÉTHODE AJOUTER COMPLÈTE
    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $donateur = $_POST['donateur'];
                $type_don = $_POST['type_don'];
                
                error_log("Tentative d'ajout de don - Type: $type_don, Donateur: $donateur");
                
                if ($type_don === 'argent') {
                    // Don en argent
                    $quantite = $_POST['quantite_argent'] ?? 0;
                    if ($quantite <= 0) {
                        $_SESSION['error'] = 'Le montant doit être positif';
                        $this->redirect('dons');
                        return;
                    }
                    $description = 'Don en argent';
                    $unite = 'Ariary';
                    $produit_id = null;
                    
                    $result = $this->donService->ajouterDon($donateur, 'argent', $description, $quantite, $unite, $produit_id);
                    
                } else if ($type_don === 'produit') {
                    // Don en produit
                    $produit_id = $_POST['produit_id'] ?? null;
                    $quantite = $_POST['quantite_produit'] ?? 0;
                    
                    if ($quantite <= 0) {
                        $_SESSION['error'] = 'La quantité doit être positive';
                        $this->redirect('dons');
                        return;
                    }
                    
                    if (!$produit_id) {
                        $_SESSION['error'] = 'Veuillez sélectionner un produit';
                        $this->redirect('dons');
                        return;
                    }
                    
                    // Récupérer les infos du produit
                    $produit = $this->donService->getProduitInfo($produit_id);
                    
                    if (!$produit) {
                        $_SESSION['error'] = 'Produit introuvable';
                        $this->redirect('dons');
                        return;
                    }
                    
                    $description = $produit['nom_produit'];
                    $unite = $produit['unite_mesure'];
                    $type_don_reel = $produit['nom_categorie']; // 'nature' ou 'materiel'
                    
                    $result = $this->donService->ajouterDon($donateur, $type_don_reel, $description, $quantite, $unite, $produit_id);
                    
                } else {
                    $_SESSION['error'] = 'Type de don invalide';
                    $this->redirect('dons');
                    return;
                }
                
                if ($result['success']) {
                    $_SESSION['message'] = '✅ Don enregistré avec succès';
                    error_log("Don ajouté avec succès - ID: " . ($result['don_id'] ?? 'N/A'));
                } else {
                    $_SESSION['error'] = $result['message'];
                    error_log("Erreur ajout don: " . $result['message']);
                }
                
            } catch (Exception $e) {
                error_log("Exception dans ajouter(): " . $e->getMessage());
                error_log("Fichier: " . $e->getFile() . " Ligne: " . $e->getLine());
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
                echo json_encode(['success' => false, 'message' => 'Données manquantes (besoin_id, quantite, type)']);
                return;
            }
            
            // Vérification stock
            $validation = $this->validationService->validerAttribution($type, $quantite);
            
            if (!$validation['valide']) {
                echo json_encode(['success' => false, 'message' => $validation['message']]);
                return;
            }
            
            // Appeler le service avec le type
            $result = $this->donService->attribuerDon($besoin_id, $don_id, $quantite, $type);
            echo json_encode($result);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
        }
    }
}