<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Fcarbah\Classes;

/**
 * Description of OutPutFormat
 *
 * @author fcarbah
 */
final class OutputFormat {
    const SAVETOFILE = 'F';
    const DOWNLOAD = 'D';
    const BROWSER = 'I';
    const STRING = 'S';
    
    public static function allowable(){
        return [
            'D','F','I','S'
        ];
    }
}
