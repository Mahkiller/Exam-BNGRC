<?php
// app/config/ServiceContainer.php

class ServiceContainer {
    private static $instances = [];
    
    public static function getStockService() {
        if (!isset(self::$instances['stock'])) {
            $donModel = new DonModel();
            $besoinModel = new BesoinModel();
            self::$instances['stock'] = new StockService($donModel, $besoinModel);
        }
        return self::$instances['stock'];
    }
    
    public static function getValidationService() {
        if (!isset(self::$instances['validation'])) {
            self::$instances['validation'] = new ValidationService(
                self::getStockService()
            );
        }
        return self::$instances['validation'];
    }
    
    public static function getBesoinService() {
        if (!isset(self::$instances['besoin'])) {
            $besoinModel = new BesoinModel();
            self::$instances['besoin'] = new BesoinService(
                $besoinModel,
                self::getValidationService()
            );
        }
        return self::$instances['besoin'];
    }
    
    public static function getDonService() {
        if (!isset(self::$instances['don'])) {
            $donModel = new DonModel();
            self::$instances['don'] = new DonService(
                $donModel,
                self::getValidationService(),
                self::getStockService()
            );
        }
        return self::$instances['don'];
    }
    
    public static function getAchatService() {
        if (!isset(self::$instances['achat'])) {
            $achatModel = new AchatModel();
            $donModel = new DonModel();
            $besoinModel = new BesoinModel();
            self::$instances['achat'] = new AchatService(
                $achatModel,
                $donModel,
                $besoinModel
            );
        }
        return self::$instances['achat'];
    }
    
    public static function getVenteService() {
        if (!isset(self::$instances['vente'])) {
            $venteModel = new VenteModel();
            self::$instances['vente'] = new VenteService(
                $venteModel,
                self::getValidationService()
            );
        }
        return self::$instances['vente'];
    }
    
    public static function getResetService() {
        if (!isset(self::$instances['reset'])) {
            self::$instances['reset'] = new ResetService();
        }
        return self::$instances['reset'];
    }
}