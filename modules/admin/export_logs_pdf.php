<?php
require_once '../../config/db.php';
require_once '../../vendor/fpdf/fpdf.php'; // Chemin vers le fichier fpdf.php

// 1. RÉCUPÉRATION DES FILTRES (Même logique que ta page actuelle)
$filter_user = $_GET['user_id'] ?? '';
$filter_date = $_GET['date_log'] ?? '';

$conditions = [];
$params = [];

if (!empty($filter_user)) {
    $conditions[] = "l.utilisateur_id = ?";
    $params[] = $filter_user;
}
if (!empty($filter_date)) {
    $conditions[] = "DATE(l.date_action) = ?";
    $params[] = $filter_date;
}

$where_clause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

// 2. REQUÊTE SQL
$sql = "SELECT l.*, u.nom as operateur, r.nom_role 
        FROM logs_activite l 
        JOIN utilisateurs u ON l.utilisateur_id = u.id 
        JOIN roles r ON u.role_id = r.id 
        $where_clause
        ORDER BY l.date_action DESC LIMIT 100";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();

// 3. GÉNÉRATION DU PDF AVEC FPDF
class PDF extends FPDF {
    // En-tête du document
    void Header() {
        $this->SetFillColor(5, 5, 5); // Fond noir (Prestige)
        $this->Rect(0, 0, 210, 40, 'F');
        
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(212, 175, 55); // Couleur Or (#D4AF37)
        $this->Cell(0, 10, utf8_decode('BÉMAR PRESTIGE - JOURNAL D\'ACTIVITÉ'), 0, 1, 'C');
        
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(150, 150, 150);
        $this->Cell(0, 5, utf8_decode('RAPPORT GÉNÉRÉ LE ' . date('d/m/Y H:i')), 0, 1, 'C');
        $this->Ln(15);
        
        // En-tête du tableau
        $this->SetFillColor(30, 30, 30);
        $this->SetTextColor(212, 175, 55);
        $this->SetDrawColor(50, 50, 50);
        $this->SetFont('Arial', 'B', 9);
        
        $this->Cell(35, 10, 'DATE', 1, 0, 'C', true);
        $this->Cell(45, 10, 'OPERATEUR', 1, 0, 'C', true);
        $this->Cell(80, 10, 'ACTION', 1, 0, 'C', true);
        $this->Cell(30, 10, 'IP', 1, 1, 'C', true);
    }

    // Pied de page
    void Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb} - Bemar Heritage Group', 0, 0, 'C');
    }
}

// Création du PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(50, 50, 50);

// Remplissage des données
foreach ($logs as $log) {
    // Calcul de la hauteur pour éviter les chevauchements si le texte est long
    $date = date('d/m/Y H:i', strtotime($log['date_action']));
    $nom = utf8_decode($log['operateur'] . ' (' . $log['nom_role'] . ')');
    $action = utf8_decode($log['action']);
    $ip = $log['adresse_ip'] ?? '127.0.0.1';

    $pdf->Cell(35, 8, $date, 1);
    $pdf->Cell(45, 8, $nom, 1);
    
    // MultiCell pour l'action au cas où le texte est trop long
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->MultiCell(80, 8, $action, 1);
    $pdf->SetXY($x + 80, $y);
    
    $pdf->Cell(30, 8, $ip, 1, 1);
}

// Sortie du PDF
$pdf->Output('I', 'Rapport_Logs_Prestige.pdf');