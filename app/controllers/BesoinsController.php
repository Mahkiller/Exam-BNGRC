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
            'critiques' => $this->besoinService->getBesoinsCritiques(),
            'categories' => $this->besoinService->getCategories(),
            'produits' => $this->besoinService->getProduits()
        ];
        $this->view('besoins', $data);
    }
    
        
            public function ajouter() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        $ville_id = $_POST['ville_id'];
        $type_besoin = $_POST['type_besoin'];
        $niveau_urgence = $_POST['niveau_urgence'];
        
        if ($type_besoin === 'argent') {
            // Besoin en argent
            $quantite = $_POST['quantite_argent'] ?? 0;
            $description = 'Besoin en argent';
            $unite = 'Ar';
            $produit_id = null;
            
            $result = $this->besoinService->ajouterBesoin(
                $ville_id, $type_besoin, $description, $quantite, $unite, $niveau_urgence, $produit_id
            );
        } else if ($type_besoin === 'nature') {
            // Besoin en produit
            $produit_id = $_POST['produit_id'] ?? null;
            $quantite = $_POST['quantite'] ?? 0;
            
            // Récupérer les infos du produit
            $produit = $this->besoinService->getProduitInfo($produit_id);
            
            if (!$produit) {
                $_SESSION['error'] = 'Produit introuvable';
                $this->redirect('besoins');
                return;
            }
            
            $description = $produit['nom_produit'];
            $unite = $produit['unite_mesure'];
            
            $result = $this->besoinService->ajouterBesoin(
                $ville_id, $type_besoin, $description, $quantite, $unite, $niveau_urgence, $produit_id
            );
        } else {
            $_SESSION['error'] = 'Type de besoin invalide';
            $this->redirect('besoins');
            return;
        }
        
        if ($result['success']) {
            $_SESSION['message'] = 'Besoin ajouté avec succès';
            $this->redirect('besoins');  
        } else {
            $_SESSION['error'] = $result['message'];
            $this->redirect('besoins');  
        }
    }
}
}