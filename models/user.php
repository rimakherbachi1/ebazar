<?php

class User
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function connexion($email)
    {
        $req = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $req->execute([$email]);
        return $req->fetch();
    }
    public function findByEmailOrPseudo($email, $pseudo)
    {
        $req = $this->pdo->prepare(
            "SELECT id, pseudo, email FROM utilisateurs WHERE email = ? OR pseudo = ?"
        );
        $req->execute([$email, $pseudo]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }
    public function create($pseudo, $email, $motdepasse)
    {
        $hash = password_hash($motdepasse, PASSWORD_DEFAULT);

        $req = $this->pdo->prepare("
            INSERT INTO utilisateurs (pseudo, email, mot_de_passe)
            VALUES (?, ?, ?)
        ");

        return $req->execute([$pseudo, $email, $hash]);
    }
    public function getAllExcept($adminId)
    {
        $stmt = $this->pdo->prepare("
            SELECT id, pseudo, email, role, date_creation
            FROM utilisateurs
            WHERE id != ?
            ORDER BY date_creation DESC
        ");
        $stmt->execute([$adminId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function deleteById($userId)
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM utilisateurs WHERE id = ?"
        );
        return $stmt->execute([$userId]);
    }
    public function getById($id)
    {
        $stmt = $this->pdo->prepare(
            "SELECT pseudo, email, date_creation FROM utilisateurs WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function countAnnonces($userId)
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(id) FROM annonces WHERE vendeur_id = ?"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    public function updateProfile($userId, $data)
    {
        $fields = [];
        $params = ['id' => $userId];

        if (isset($data['pseudo'])) {
            $fields[] = "pseudo = :pseudo";
            $params['pseudo'] = $data['pseudo'];
        }

        if (isset($data['email'])) {
            $fields[] = "email = :email";
            $params['email'] = $data['email'];
        }

        if (isset($data['mot_de_passe'])) {
            $fields[] = "mot_de_passe = :mot_de_passe";
            $params['mot_de_passe'] = $data['mot_de_passe'];
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE utilisateurs SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
    
}
