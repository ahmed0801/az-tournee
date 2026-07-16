<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();
$p = App\Models\TourneeParametre::first();
$now = Carbon\Carbon::now();
echo "now=" . $now->format("H:i") . "\n";
echo "delai=" . $p->delai_min_heures . "\n";
$creneaux = $p->creneaux;
$delai = $p->delai_min_heures;
foreach ($creneaux as $c) {
    $debut = $now->copy()->startOfDay()->setTimeFromTimeString($c["debut"]);
    $limite = $debut->copy()->subHours($delai);
    $ok = $now->lessThan($limite) ? "DISPO" : "PASSE";
    echo $c["label"] . " debut=" . $debut->format("H:i") . " limite=" . $limite->format("H:i") . " => " . $ok . "\n";
}
