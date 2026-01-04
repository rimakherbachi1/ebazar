<?php

class achat
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAchatsParStatut($userId, $statut)
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                ac.id AS achat_id, 
                a.id AS annonce_id, 
                a.titre, 
                a.prix,
                ac.date_achat,
                ac.statut AS statut_achat,
                ac.mode_livraison,
                a.statut AS statut_annonce,
                (SELECT chemin FROM photos 
                 WHERE annonce_id = a.id 
                 ORDER BY position ASC 
                 LIMIT 1) AS photo_principale
            FROM achats ac
            JOIN annonces a ON ac.annonce_id = a.id
            WHERE ac.acheteur_id = :user_id
              AND ac.statut = :statut
            ORDER BY ac.date_achat DESC
        ");

        $stmt->execute([
            'user_id' => $userId,
            'statut'  => $statut
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function confirmerReception($achatId, $userId)
    {
        $this->pdo->beginTransaction();

        $stmt1 = $this->pdo->prepare("
            UPDATE achats 
            SET statut = 'RECU', date_reception = NOW() 
            WHERE id = ? AND acheteur_id = ?
        ");
        $stmt1->execute([$achatId, $userId]);

        $stmt2 = $this->pdo->prepare("
            UPDATE annonces a
            JOIN achats ac ON ac.annonce_id = a.id
            SET a.statut = 'LIVRE'
            WHERE ac.id = ? AND ac.acheteur_id = ?
        ");
        $stmt2->execute([$achatId, $userId]);

        $this->pdo->commit();
    }

    public function confirmerReservation($achatId, $userId)
    {
        $stmt = $this->pdo->prepare("
            UPDATE annonces a
            JOIN achats ac ON ac.annonce_id = a.id
            SET a.statut = 'VENDU'
            WHERE ac.id = ? AND ac.acheteur_id = ?
        ");
        $stmt->execute([$achatId, $userId]);
    }

    public function annulerReservation($achatId, $userId)
    {
        $this->pdo->beginTransaction();

        $stmt1 = $this->pdo->prepare("
            UPDATE annonces a
            JOIN achats ac ON ac.annonce_id = a.id
            SET a.statut = 'EN_VENTE'
            WHERE ac.id = ? AND ac.acheteur_id = ?
        ");
        $stmt1->execute([$achatId, $userId]);

        $stmt2 = $this->pdo->prepare("
            DELETE FROM achats 
            WHERE id = ? AND acheteur_id = ?
        ");
        $stmt2->execute([$achatId, $userId]);

        $this->pdo->commit();
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

    public function getAcheteur($acheteur_id) {
        $stmt = $this->pdo->prepare("SELECT pseudo FROM utilisateurs WHERE id = ?");
        $stmt->execute([$acheteur_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function effectuerAchat($annonce_id, $acheteur_id, $mode_livraison) {
        try {
            $this->pdo->beginTransaction();

            $stmt_insert = $this->pdo->prepare("
                INSERT INTO achats (annonce_id, acheteur_id, mode_livraison, statut)
                VALUES (?, ?, ?, 'EN_ATTENTE')
            ");
            $stmt_insert->execute([$annonce_id, $acheteur_id, $mode_livraison]);

            $stmt_update = $this->pdo->prepare("
                UPDATE annonces SET statut = 'VENDU' 
                WHERE id = ? AND statut = 'EN_VENTE'
            ");
            $stmt_update->execute([$annonce_id]);

            if ($stmt_update->rowCount() === 0) {
                throw new Exception("Annonce plus disponible.");
            }

            $acheteur = $this->getAcheteur($acheteur_id);

            $this->pdo->commit();

            return [
                'acheteur' => $acheteur['pseudo'],
                'mode_livraison' => $mode_livraison
            ];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
