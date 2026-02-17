<?php
class StockController extends Controller {
    private $achatModel;
    private $donModel;
    private $besoinModel;
    public function __construct() {
        $this->achatModel = new AchatModel();
        $this->donModel = new DonModel();
        $this->besoinModel = new BesoinModel();
    }
    public function index() {
        $categorie_id = $_GET['categorie_id'] ?? null;
        $stmt = Database::getInstance()->query("
            SELECT * FROM categorie_produit_BNGRC ORDER BY nom_categorie
        ");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $produits = [];
        if ($categorie_id) {
            $stmt = Database::getInstance()->prepare("
                SELECT p.*, c.nom_categorie 
                FROM produit_BNGRC p
                JOIN categorie_produit_BNGRC c ON p.categorie_id = c.id
                WHERE p.categorie_id = ?
                ORDER BY p.nom_produit
            ");
            $stmt->execute([$categorie_id]);
            $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = Database::getInstance()->query("
                SELECT p.*, c.nom_categorie 
                FROM produit_BNGRC p
                JOIN categorie_produit_BNGRC c ON p.categorie_id = c.id
                ORDER BY c.nom_categorie, p.nom_produit
            ");
            $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        $totaux = [];
        foreach ($categories as $cat) {
            $stmt = Database::getInstance()->prepare("
                SELECT SUM(stock_actuel) as total FROM produit_BNGRC WHERE categorie_id = ?
            ");
            $stmt->execute([$cat['id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $totaux[$cat['id']] = $result['total'] ?? 0;
        }
        $data = [
            'categories' => $categories,
            'produits' => $produits,
            'totaux' => $totaux,
            'categorie_id' => $categorie_id
        ];
        $this->view('stock', $data);
    }
}
