<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Classes;

/**
 * Description of Orientation
 *
 * @author fcarbah
 */
final class Orientation {
    
    const POTRAIT = 'P';
    const LANDSCAPE = 'L';
    
    public static function allowable(){
        return [
            'P','L'
        ];
    }
}
