<?php

namespace App\Service;

class RandomFlashMessage
{
    public function getTitle()
    {
        $words = ['Énorme !', 'Au top !', 'Yeaaah !', 'Bravo !', 'Super !', 'Hourra !', 'Génial !', 'Cool !', 'Magnifique !', 'Jolie !', 'Fantastique !', 'sensationnel !', 'Parfait !', 'Topissime !', 'Splendide !', 'Fabuleux !', 'Awesome !'];
        $indice = rand(0, count($words) - 1);
        $datas = $words[$indice];

        return $datas;
    }
}
