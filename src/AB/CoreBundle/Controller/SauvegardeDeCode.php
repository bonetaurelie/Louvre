<?php
/**
 * Created by PhpStorm.
 * User: Audrophe
 * Date: 03/06/2016
 * Time: 16:57
 */



if ($billet->getQuantite() == 4) {
    $nom_prec = "";
    $cpt = 0;
    $enf = $adulte = 0;
    $date = new \DateTime();
    foreach ($billet->getVisiteurs() as $vis) {
        $nom_actuel = strtolower($vis->getNom());
        $age=$vis->getDateNaissance();
        if($cpt == 0){
            $nom_prec = $nom_actuel;
        }else {
            if ($nom_actuel !== $nom_prec) {
                $flag_famille = FALSE;
                break;
            }
            else{
                if ($nom_actuel == $nom_prec) {
                    $age_pour_enf = $date->sub(new \DateInterval('P12Y'));
                    if ($vis->getDateNaissance() >= $age_pour_enf) {
                        echo "enfants";
                        // C'est un enfant !
                        dump($enf);

                        $enf++;
                    } else {
                        echo "adultes";
                        dump($adulte);
                        // C'est un adulte
                        $adulte++;
                    }
                }
            }
        }
        $cpt++;
    }
    if($enf == 2 && $adulte == 2){
        echo"famille";

        // C'est une famille avec deux enfants 2 adultes
        $flag_famille = TRUE;
    }else{
        // C'est pas une famille
        echo "Il y a ".$enf." enfant(s) et ".$adulte." adulte(s)";
        $flag_famille = FALSE;
    }
}
else {
    $flag_famille = FALSE;
}