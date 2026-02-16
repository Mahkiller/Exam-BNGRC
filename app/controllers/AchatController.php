<?php
class AchatController extends Controller {
    private $achatService;
    private $donService;
    private $besoinService;
    
    public function __construct() {
        $this->achatService = ServiceContainer::getAchatService();
        $this->donService = ServiceContainer::getDonService();
        $this->besoinService = ServiceContainer::getBesoinService();
    }
    
    // Afficher tous les achats
    public function index() {
        $ville_id = $_GET['ville_id'] ?? null;
        
        if ($ville_id) {
            $achats = $this->achatService->getAchatsByVille($ville_id);
        } else {
            $achats = $this->achatService->getAllAchats();
        }
        
        $data = [
            'achats' => $achats,
            'villes' => $this->besoinService->getVilles(),
            'ville_id' => $ville_id,
            'prix_unitaires' => $this->achatService->getPrixUnitaires(),
            'total_montant' => $this->achatService->getTotalMontantAchats(),
            'montant_par_ville' => $this->achatService->getMontantTotalParVille()
        ];
        
        $this->view('achats', $data);
    }
    
    // Créer un achat
    public function creer() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $don_id = $_POST['don_id'] ?? null;
            $besoin_id = $_POST['besoin_id'] ?? null;
            $description_article = $_POST['description_article'] ?? '';
            $quantite = $_POST['quantite'] ?? 0;
            $prix_unitaire = $_POST['prix_unitaire'] ?? 0;
            
            $result = $this->achatService->creerAchat(
                $don_id, 
                $besoin_id, 
                $description_article, 
                $quantite, 
                $prix_unitaire
            );
            
            if ($result['success']) {
                $_SESSION['message'] = 'Achat créé avec succès';
            } else {
                $_SESSION['error'] = $result['message'];
            }
            
            $this->redirect('/achats');
        }
        
        $data = [
            'dons_argent' => array_filter($this->donService->getAllDons(), function($d) {
                return $d['type_don'] === 'argent';
            }),
            'besoins' => $this->besoinService->getAllBesoins(),
            'prix_unitaires' => $this->achatService->getPrixUnitaires()
        ];
        
        $this->view('achat_form', $data);
    }
    
    // Supprimer un achat
    public function supprimer() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            
            if ($id) {
                $result = $this->achatService->supprimerAchat($id);
                $_SESSION['message'] = $result['message'];
            }
            
            $this->redirect('/achats');
        }
    }
}
