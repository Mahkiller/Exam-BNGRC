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
        $data = [
            'stats' => [
                'total_besoins' => $this->besoinService->getTotalBesoins(),
                'total_dons' => $this->donService->getTotalDons(),
                'villes_aidees' => $this->besoinService->getVillesAidees(),
                'stock_riz' => $stockGlobal['nature'] ?? 0,        
                'stock_argent' => $stockGlobal['argent'] ?? 0,      
                'stock_toles' => $stockGlobal['materiaux'] ?? 0     
            ],
            'besoins_recents' => $this->besoinService->getBesoinsRecents(5),
            'dons_recents' => $this->donService->getDonsRecents(5),
            'villes' => $this->besoinService->getAllBesoinsParVille(),
            'urgences' => [
                'critiques' => $this->besoinService->getBesoinsCritiques(),
                'stats' => $this->besoinService->getStatsUrgence()
            ],
            'top_donateurs' => $this->donService->getTopDonateurs(3)
        ];
        $this->view('dashboard', $data);
    }
}
