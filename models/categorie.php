<?php

class categorie
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        $stmt = $this->pdo->query(
            "SELECT id, nom, date_creation FROM categories ORDER BY nom ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existsByName($nom)
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM categories WHERE nom = ?"
        );
        $stmt->execute([$nom]);
        return $stmt->fetchColumn() > 0;
    }

    public function existsByNameExcept($nom, $id)
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM categories WHERE nom = ? AND id != ?"
        );
        $stmt->execute([$nom, $id]);
        return $stmt->fetchColumn() > 0;
    }

    public function create($nom)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO categories (nom) VALUES (?)"
        );
        return $stmt->execute([$nom]);
    }

    public function rename($id, $nom)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE categories SET nom = ? WHERE id = ?"
        );
        return $stmt->execute([$nom, $id]);
    }
}
