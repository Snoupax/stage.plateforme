<?php

namespace App\Service;


class cmpDateService
{
    public function cmpDate($a, $b)
    {
        $date1 = $a->getDateEnvoi();
        $date2 = $b->getDateEnvoi();

        $date1 = $date1->getTimestamp();
        $date2 = $date2->getTimestamp();
        return $date1 - $date2;
    }
}
