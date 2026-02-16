<?php
class DonsController extends Controller {
    private $donService;
    private $validationService;
    
    public function __construct() {
        $this->donService = ServiceContainer::getDonService();
        $this->validationService = ServiceContainer::getValidationService();
    }
    
    public function index() {
        $data = [
            'dons' => $this->donService->getAllDons(),
            'stock' => $this->donService->getStockGlobal(),
            'stock_detail' => $this->donService->getStockDetaille(),
            'stats_donateurs' => $this->donService->getStatsDonateurs(),
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $besoin_id = $_POST['besoin_id'];
            $don_id = $_POST['don_id'] ?? null;
            $quantite = $_POST['quantite'];
            $type = $_POST['type'];
            
            // Vérification stock
            $validation = $this->validationService->validerAttribution($type, $quantite);
            
            if (!$validation['valide']) {
                echo json_encode(['success' => false, 'message' => $validation['message']]);
                return;
            }
            
            $result = $this->donService->attribuerDon($besoin_id, $don_id, $quantite);
            
            header('Content-Type: application/json');
            echo json_encode($result);
        }
    }
}