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
            'top_donateurs' => $this->donService->getTopDonateurs()
        ];
        $this->view('dons', $data);
    }
    
    public function attribution() {
        $data = [
            'besoins_non_satisfaits' => $this->donService->getBesoinsNonSatisfaits(),
            'stock' => $this->donService->getStockGlobal(),
            'dons_disponibles' => $this->donService->getDonsDisponibles()
        ];
        $this->view('attribution', $data);
    }
    
    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $donateur = $_POST['donateur'];
            $type_don = $_POST['type_don'];
            $description = $_POST['description'];
            $quantite = $_POST['quantite'];
            $unite = $_POST['unite'];
            
            $result = $this->donService->ajouterDon($donateur, $type_don, $description, $quantite, $unite);
            
            if ($result['success']) {
                $_SESSION['message'] = 'Don enregistré avec succès';
            } else {
                $_SESSION['error'] = $result['message'];
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