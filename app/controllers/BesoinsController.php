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
                if ($quantite <= 0) {
                    $_SESSION['error'] = 'Le montant doit être positif';
                    $this->redirect('besoins');
                    return;
                }
                $description = 'Besoin en argent';
                $unite = 'Ar';
                $produit_id = null;
                
                $result = $this->besoinService->ajouterBesoin(
                    $ville_id, $type_besoin, $description, $quantite, $unite, $niveau_urgence, $produit_id
                );
            } else {
                // Besoin en produit (nature ou materiaux)
                $produit_id = $_POST['produit_id'] ?? null;
                $quantite = $_POST['quantite'] ?? 0;
                
                if ($quantite <= 0) {
                    $_SESSION['error'] = 'La quantité doit être positive';
                    $this->redirect('besoins');
                    return;
                }
                
                if (!$produit_id) {
                    $_SESSION['error'] = 'Veuillez sélectionner un produit';
                    $this->redirect('besoins');
                    return;
                }
                
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