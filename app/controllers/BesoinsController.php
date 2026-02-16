<?php
class BesoinsController extends Controller {
    private $besoinService;
    
    public function __construct() {
        $this->besoinService = ServiceContainer::getBesoinService();
    }
    
    public function index() {
        $data = [
            'besoins' => $this->besoinService->getAllBesoins(),
            'villes' => $this->besoinService->getVilles(),
            'stats_urgence' => $this->besoinService->getStatsUrgence(),
            'critiques' => $this->besoinService->getBesoinsCritiques()
        ];
        $this->view('besoins', $data);
    }
    
    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ville_id = $_POST['ville_id'];
            $type_besoin = $_POST['type_besoin'];
            $description = $_POST['description'];
            $quantite = $_POST['quantite'];
            $unite = $_POST['unite'];
            $niveau_urgence = $_POST['niveau_urgence'];
            
            $result = $this->besoinService->ajouterBesoin(
                $ville_id, $type_besoin, $description, $quantite, $unite, $niveau_urgence
            );
            
            if ($result['success']) {
                $_SESSION['message'] = 'Besoin ajouté avec succès';
            } else {
                $_SESSION['error'] = $result['message'];
            }
            
            $this->redirect('besoins');
        }
    }
}