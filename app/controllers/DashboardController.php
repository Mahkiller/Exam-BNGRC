<?php
class DashboardController extends Controller {
    private $besoinService;
    private $donService;
    private $stockService;
    
    public function __construct() {
        $this->besoinService = ServiceContainer::getBesoinService();
        $this->donService = ServiceContainer::getDonService();
        $this->stockService = ServiceContainer::getStockService();
    }
    
    public function index() {
        $stockGlobal = $this->stockService->getStockGlobal();
        
        // Obtenir les stats par type de donateur
        $statsDonateurs = $this->donService->getStatsDonateurs();
        $donateurStats = [
            'internationaux' => 0,
            'nationaux' => 0
        ];
        
        foreach ($statsDonateurs as $stat) {
            if ($stat['type_donateur'] === 'International') {
                $donateurStats['internationaux'] = $stat['nombre_dons'];
            } else {
                $donateurStats['nationaux'] = $stat['nombre_dons'];
            }
        }
        
        // DEBUG : Afficher le contenu de $stockGlobal pour vérifier
        // var_dump($stockGlobal); exit;
        
        $data = [
            'stats' => [
                'total_besoins' => $this->besoinService->getTotalBesoins(),
                'total_dons' => $this->donService->getTotalDons(),
                'villes_aidees' => $this->besoinService->getVillesAidees(),
                // CORRECTION : Utiliser les bonnes clés du stock global
                'stock_riz' => $stockGlobal['nature'] ?? 0,        // Riz = nature
                'stock_argent' => $stockGlobal['argent'] ?? 0,      // Argent
                'stock_toles' => $stockGlobal['materiaux'] ?? 0     // Tôles = materiaux
            ],
            'besoins_recents' => $this->besoinService->getBesoinsRecents(5),
            'dons_recents' => $this->donService->getDonsRecents(5),
            'villes' => $this->besoinService->getAllBesoinsParVille(),
            'urgences' => [
                'critiques' => $this->besoinService->getBesoinsCritiques(),
                'stats' => $this->besoinService->getStatsUrgence()
            ],
            'top_donateurs' => $this->donService->getTopDonateurs(3),
            'donateurs' => $donateurStats
        ];
        
        $this->view('dashboard', $data);
    }
}