<?php
// class TrancheService {
//     // Seuils des tranches en FCFA
//     private const SEUILS = [
//         'T1' => ['min' => 0, 'max' => 11850],      // 0 à 150 kWh à 79 FCFA/kWh
//         'T2' => ['min' => 11851, 'max' => 21250],  // 151 à 250 kWh à 94 FCFA/kWh  
//         'T3' => ['min' => 21251, 'max' => 45750],  // 251 à 500 kWh à 99 FCFA/kWh
//         'T4' => ['min' => 45751, 'max' => PHP_INT_MAX] // > 500 kWh à 109 FCFA/kWh
//     ];

//     private const TARIFS = [
//         'T1' => 79,
//         'T2' => 94,
//         'T3' => 99,
//         'T4' => 109
//     ];

//     public function calculerKwhEtTranche(float $montant, float $consommationMoisCourant): array {
//         $montantTotal = $consommationMoisCourant + $montant;
        
//         // Déterminer la tranche principale
//         $tranche = $this->determinerTranche($montantTotal);
        
//         // Calculer le nombre de kWh selon la tranche
//         $kwh = $montant / self::TARIFS[$tranche];
        
//         return [
//             'kwh' => round($kwh, 2),
//             'tranche' => $tranche,
//             'prix_unitaire' => self::TARIFS[$tranche]
//         ];
//     }

//     private function determinerTranche(float $montantTotal): string {
//         foreach (self::SEUILS as $nomTranche => $seuil) {
//             if ($montantTotal >= $seuil['min'] && $montantTotal <= $seuil['max']) {
//                 return $nomTranche;
//             }
//         }
//         return 'T4'; // Par défaut la tranche la plus élevée
//     }
// }