<?php
class reserveation {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAnnonce($annonce_id) {
        $stmt = $this->pdo->prepare("
            SELECT a.*, u.pseudo AS vendeur_pseudo 
            FROM annonces a 
            JOIN utilisateurs u ON a.vendeur_id = u.id 
            WHERE a.id = ? AND a.statut = 'EN_VENTE'
        ");
        $stmt->execute([$annonce_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function reserverAnnonce($annonce_id, $acheteur_id, $mode_livraison) {
        try {
            $this->pdo->beginTransaction();

            $stmt_insert = $this->pdo->prepare("
                INSERT INTO achats (annonce_id, acheteur_id, mode_livraison, statut)
                VALUES (?, ?, ?, 'EN_ATTENTE')
            ");
            $stmt_insert->execute([$annonce_id, $acheteur_id, $mode_livraison]);

            $stmt_update = $this->pdo->prepare("
                UPDATE annonces SET statut = 'RESERVER' 
                WHERE id = ? AND statut = 'EN_VENTE'
            ");
            $stmt_update->execute([$annonce_id]);

            if ($stmt_update->rowCount() === 0) {
                throw new Exception("Annonce plus disponible.");
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
