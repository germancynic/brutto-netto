<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$data = json_decode(file_get_contents("php://input"), true);

$brutto = floatval($data["brutto"] ?? 0);
$steuerklasse = $data["steuerklasse"] ?? "I";
$kinder = intval($data["kinder"] ?? 0);
$kirchensteuer = filter_var($data["kirchensteuer"] ?? false, FILTER_VALIDATE_BOOLEAN);
$nacht_zuschlag = filter_var($data["nacht_zuschlag"] ?? false, FILTER_VALIDATE_BOOLEAN);

$steuer_satz = ["I" => 0.25, "II" => 0.22, "III" => 0.18, "IV" => 0.25, "V" => 0.35, "VI" => 0.42];
$kirchensteuer_satz = $kirchensteuer ? 0.09 : 0.0;
$kinderfreibetrag = $kinder * 500;

$steuer = ($brutto * ($steuer_satz[$steuerklasse] ?? 0.25)) - $kinderfreibetrag;
$steuer = max($steuer, 0);
$kirchensteuer_betrag = $steuer * $kirchensteuer_satz;
$sozialabgaben = $brutto * 0.2;
$nacht_bonus = $nacht_zuschlag ? ($brutto * 0.1) : 0;

$netto = $brutto - $steuer - $kirchensteuer_betrag - $sozialabgaben + $nacht_bonus;

echo json_encode([
    "brutto" => $brutto,
    "netto" => round($netto, 2),
    "steuerklasse" => $steuerklasse,
    "kinder" => $kinder,
    "kirchensteuer" => $kirchensteuer,
    "nacht_zuschlag" => $nacht_zuschlag
]);

?>
