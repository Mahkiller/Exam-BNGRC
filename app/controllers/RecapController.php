<?php
class RecapController extends Controller {
    private $besoinService;
    private $donService;
    private $achatService;
    private $venteService;
    
    public function __construct() {
        $this->besoinService = ServiceContainer::getBesoinService();
        $this->donService = ServiceContainer::getDonService();
        $this->achatService = ServiceContainer::getAchatService();
        $this->venteService = ServiceContainer::getVenteService();
    }
    
    // Afficher le récapitulatif financier
    public function index() {
        try {
            // Calculs des besoins
            $besoins = $this->besoinService->getAllBesoins();
            
            $total_montant_besoins = 0;
            $total_montant_satisfait = 0;
            
            foreach ($besoins as $besoin) {
                if ($besoin['type_besoin'] === 'argent') {
                    $total_montant_besoins += $besoin['quantite_demandee'];
                    $montant_attribue = $besoin['quantite_attribuee'] ?? 0;
                    $total_montant_satisfait += $montant_attribue;
                } else {
                    $prix_unitaire = $this->getPrixUnitaire($besoin['description']);
                    $montant_besoin = $besoin['quantite_demandee'] * $prix_unitaire;
                    $montant_attribue = ($besoin['quantite_attribuee'] ?? 0) * $prix_unitaire;
                    
                    $total_montant_besoins += $montant_besoin;
                    $total_montant_satisfait += $montant_attribue;
                }
            }
            
            // Dons en argent
            $tousDons = $this->donService->getAllDons();
            $total_dons_recu = 0;
            
            foreach ($tousDons as $don) {
                if ($don['type_don'] === 'argent') {
                    $total_dons_recu += $don['quantite_totale'];
                }
            }
            
            // Achats et ventes
            $total_achats = $this->achatService->getTotalMontantAchats();
            $total_ventes = $this->venteService->getTotalVentes();
            
            $budget_disponible = $total_dons_recu - $total_achats + $total_ventes;
            
            $data = [
                'besoins' => [
                    'total_montant' => $total_montant_besoins,
                    'satisfait' => $total_montant_satisfait,
                    'reste' => $total_montant_besoins - $total_montant_satisfait,
                    'pourcentage' => $total_montant_besoins > 0 ? ($total_montant_satisfait / $total_montant_besoins) * 100 : 0
                ],
                'dons' => [
                    'total_recu' => $total_dons_recu,
                    'depense' => $total_achats,
                    'budget_disponible' => $budget_disponible,
                    'pourcentage_depense' => $total_dons_recu > 0 ? ($total_achats / $total_dons_recu) * 100 : 0
                ],
                'achats_total' => $total_achats,
                'ventes_total' => $total_ventes,
                'achats_recents' => $this->achatService->getAchatsRecents(10)
            ];
            
            $this->view('recap_financier', $data);
            
        } catch (Exception $e) {
            error_log("ERREUR dans RecapController: " . $e->getMessage());
            $data = [
                'besoins' => [
                    'total_montant' => 0,
                    'satisfait' => 0,
                    'reste' => 0,
                    'pourcentage' => 0
                ],
                'dons' => [
                    'total_recu' => 0,
                    'depense' => 0,
                    'budget_disponible' => 0,
                    'pourcentage_depense' => 0
                ],
                'achats_total' => 0,
                'ventes_total' => 0,
                'achats_recents' => []
            ];
            $this->view('recap_financier', $data);
        }
    }
    
    // AJAX pour actualiser les données
    public function actualiser() {
        header('Content-Type: application/json');
        
        try {
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
                    $prix_unitaire = $this->getPrixUnitaire($besoin['description']);
                    $montant_besoin = $besoin['quantite_demandee'] * $prix_unitaire;
                    $montant_attribue = ($besoin['quantite_attribuee'] ?? 0) * $prix_unitaire;
                    $total_montant_besoins += $montant_besoin;
                    $total_montant_satisfait += $montant_attribue;
                }
            }
            
            $tousDons = $this->donService->getAllDons();
            $total_dons_recu = 0;
            
            foreach ($tousDons as $don) {
                if ($don['type_don'] === 'argent') {
                    $total_dons_recu += $don['quantite_totale'];
                }
            }
            
            $total_achats = $this->achatService->getTotalMontantAchats();
            $total_ventes = $this->venteService->getTotalVentes();
            $budget_disponible = $total_dons_recu - $total_achats + $total_ventes;
            
            echo json_encode([
                'besoins' => [
                    'total_montant' => $total_montant_besoins,
                    'satisfait' => $total_montant_satisfait,
                    'reste' => $total_montant_besoins - $total_montant_satisfait,
                    'pourcentage' => $total_montant_besoins > 0 ? ($total_montant_satisfait / $total_montant_besoins) * 100 : 0
                ],
                'dons' => [
                    'total_recu' => $total_dons_recu,
                    'depense' => $total_achats,
                    'budget_disponible' => $budget_disponible,
                    'pourcentage_depense' => $total_dons_recu > 0 ? ($total_achats / $total_dons_recu) * 100 : 0
                ],
                'achats_total' => $total_achats,
                'ventes_total' => $total_ventes
            ]);
            
        } catch (Exception $e) {
            error_log("ERREUR dans actualiser: " . $e->getMessage());
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    
    // Fonction utilitaire - récupérer le prix d'un produit
    private function getPrixUnitaire($description) {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT prix_unitaire_reference FROM produit_BNGRC
            WHERE nom_produit LIKE ? OR description LIKE ?
            LIMIT 1
        ");
        $searchTerm = '%' . $description . '%';
        $stmt->execute([$searchTerm, $searchTerm]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['prix_unitaire_reference'] ?? 0;
    }
}