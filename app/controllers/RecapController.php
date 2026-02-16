<?php
class RecapController extends Controller {
    private $besoinService;
    private $donService;
    private $achatService;
    
    public function __construct() {
        $this->besoinService = ServiceContainer::getBesoinService();
        $this->donService = ServiceContainer::getDonService();
        $this->achatService = ServiceContainer::getAchatService();
    }
    
    // Afficher le récapitulatif financier
    public function index() {
        // Calculs des besoins
        $besoins = $this->besoinService->getAllBesoins();
        
        $total_montant_besoins = 0;
        $total_montant_satisfait = 0;
        
        foreach ($besoins as $besoin) {
            if ($besoin['type_besoin'] === 'argent') {
                // Les besoins en argent
                $total_montant_besoins += $besoin['quantite_demandee'];
                $montant_attribue = $besoin['quantite_attribuee'] ?? 0;
                $total_montant_satisfait += $montant_attribue;
            } else {
                // Les besoins en nature/matériaux convertis en montant
                $numero_prix = $this->getPrixUnitaire($besoin['type_besoin'], $besoin['description']);
                $montant_besoin = $besoin['quantite_demandee'] * $numero_prix;
                $montant_attribue = ($besoin['quantite_attribuee'] ?? 0) * $numero_prix;
                
                $total_montant_besoins += $montant_besoin;
                $total_montant_satisfait += $montant_attribue;
            }
        }
        
        // Calculs des dons en argent
        $dons = $this->donService->getAllDons();
        $total_dons_recu = 0;
        $total_dons_dispache = 0;
        
        foreach ($dons as $don) {
            if ($don['type_don'] === 'argent') {
                $total_dons_recu += $don['quantite_totale'];
                $montant_utilise = $this->getMontantUtiliseDon($don['id']);
                $total_dons_dispache += $montant_utilise;
            }
        }
        
        $data = [
            'besoins' => [
                'total_montant' => $total_montant_besoins,
                'satisfait' => $total_montant_satisfait,
                'reste' => $total_montant_besoins - $total_montant_satisfait,
                'pourcentage' => $total_montant_besoins > 0 ? ($total_montant_satisfait / $total_montant_besoins) * 100 : 0
            ],
            'dons' => [
                'total_recu' => $total_dons_recu,
                'dispache' => $total_dons_dispache,
                'reste' => $total_dons_recu - $total_dons_dispache,
                'pourcentage' => $total_dons_recu > 0 ? ($total_dons_dispache / $total_dons_recu) * 100 : 0
            ],
            'achats_total' => $this->achatService->getTotalMontantAchats(),
            'achats_recents' => $this->achatService->getAchatsRecents(10)
        ];
        
        $this->view('recap_financier', $data);
    }
    
    // AJAX pour actualiser les données
    public function actualiser() {
        header('Content-Type: application/json');
        
        // Recalculer les montants
        $besoins = $this->besoinService->getAllBesoins();
        $total_montant_besoins = 0;
        $total_montant_satisfait = 0;
        
        foreach ($besoins as $besoin) {
            if ($besoin['type_besoin'] === 'argent') {
                $total_montant_besoins += $besoin['quantite_demandee'];
                $montant_attribue = $besoin['quantite_attribuee'] ?? 0;
                $total_montant_satisfait += $montant_attribue;
            } else {
                $numero_prix = $this->getPrixUnitaire($besoin['type_besoin'], $besoin['description']);
                $montant_besoin = $besoin['quantite_demandee'] * $numero_prix;
                $montant_attribue = ($besoin['quantite_attribuee'] ?? 0) * $numero_prix;
                $total_montant_besoins += $montant_besoin;
                $total_montant_satisfait += $montant_attribue;
            }
        }
        
        $dons = $this->donService->getAllDons();
        $total_dons_recu = 0;
        $total_dons_dispache = 0;
        
        foreach ($dons as $don) {
            if ($don['type_don'] === 'argent') {
                $total_dons_recu += $don['quantite_totale'];
                $montant_utilise = $this->getMontantUtiliseDon($don['id']);
                $total_dons_dispache += $montant_utilise;
            }
        }
        
        echo json_encode([
            'besoins' => [
                'total_montant' => $total_montant_besoins,
                'satisfait' => $total_montant_satisfait,
                'reste' => $total_montant_besoins - $total_montant_satisfait,
                'pourcentage' => $total_montant_besoins > 0 ? ($total_montant_satisfait / $total_montant_besoins) * 100 : 0
            ],
            'dons' => [
                'total_recu' => $total_dons_recu,
                'dispache' => $total_dons_dispache,
                'reste' => $total_dons_recu - $total_dons_dispache,
                'pourcentage' => $total_dons_recu > 0 ? ($total_dons_dispache / $total_dons_recu) * 100 : 0
            ],
            'achats_total' => $this->achatService->getTotalMontantAchats()
        ]);
    }
    
    // Fonctions utilitaires
    private function getPrixUnitaire($type_besoin, $description) {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT prix_unitaire FROM prix_unitaire_BNGRC
            WHERE type_article = ? AND description = ?
        ");
        $stmt->execute([$type_besoin, $description]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['prix_unitaire'] ?? 0;
    }
    
    private function getMontantUtiliseDon($don_id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT SUM(montant_total) as total FROM achat_BNGRC WHERE don_id = ?");
        $stmt->execute([$don_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
}
