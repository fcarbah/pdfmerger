<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Classes;
use setasign\Fpdi\Fpdi;
/**
 * Description of PdfMerger
 *
 * @author fcarbah
 */
class PdfMerger {
    
    private $files;
    private $fpdi;
    private $errors;
    
    public function __construct() {
        $this->fpdi = new Fpdi();
        $this->files = array();
        $this->errors = [];
    }
    
    public function __destruct() {
        $this->errors = null;
        $this->files =null;
        $this->fpdi = null;
    }
    
    /**
     * 
     * @param string $filePath
     * @param string $pages
     * @param \Classes\Orientation $orientation
     */
    public function addPdf($filePath,$pages= 'all',Orientation $orientation= Orientation::POTRAIT){
        
        if(file_exists($filePath)){
        
            $this->files[] = array(
                'path'=>$filePath,
                'pages'=>$pages,
                'orientation'=>$orientation
            );
        }
        else{
            $this->errors[] = "Could not add file $filePath. File does not exist";
        }
    }
    
    /**
     * 
     * @return array of error messages
     */
    public function errors(){
        return $this->errors;
    }
    
    /**
     * 
     * @param \Classes\OutputFormat $outputFormat
     * @param string $outputFilePath fullpath to outpput file
     * @param \Classes\Orientation $orientation
     * @return mixed string | boolean
     */
    public function merge(OutputFormat $outputFormat= OutputFormat::STRING,$outputFilePath='',Orientation $orientation= Orientation::POTRAIT){
        
        if(empty($this->files)){
            $this->errors[] = 'No files to merge';
            return '';
        }
        
        if(!in_array($outputFormat, OutputFormat::allowable())){
            $this->errors[] = 'Invalid output format specified. Defaulting to default String';
            $outputFormat = OutputFormat::STRING;
        }
        
        if(!in_array($orientation, Orientation::allowable())){
            $this->errors[] = 'Invalid orientation specified. Defaulting to default Potrait';
            $orientation = OutputFormat::STRING;
        }
        
        foreach($this->files as $file){
            $this->importFile($file);
        }
        
        return $this->getOutput($outputFormat, $outputFilePath);
        
    }
    
    /**
     * 
     * @param \Classes\OutputFormat $outputFormat
     * @param type $outputFilePath
     * @return mixed string | boolean
     */
    protected function getOutput(OutputFormat $outputFormat,$outputFilePath){
        
        if ($outputFormat == OutputFormat::STRING) {
            return $this->fpdi->Output($outputFormat);
        } else {
            if ($this->fpdi->Output($outputFormat,$outputFilePath) == '') {
                return true;
            } else {
                $this->errors[] = "Error outputting PDF to '$outputFormat'.";
                return false;
            }
        }
    }
    
    /**
     * 
     * @param string $pages
     * @return array of page numbers
     * @throws Exception
     */
    protected function getPages($pages){
        
        $tempPages = explode(',',$pages);
        
        $actualPages = array();
        
        foreach($tempPages as $page){
            
            if(stripos($page,'-') === false){
                $actualPages = array_merge($actualPages,$this->getPageRange($page));
            }
            else{
                $pg = (intval($page));
                
                if($pg <1){
                   throw new Exception("Invalid Page Range"); 
                }
                
                $actualPages[] = $pg;
            }
            
        }
        
        return $actualPages;
    }
    /**
     * 
     * @param string $range
     * @return array of page numbers
     * @throws Exception
     */
    protected function getPageRange($range){
        
        $pages = explode('-',$range);
        
        if(count($pages) > 2){
            throw new Exception("Invalid Page Range");
        }
        
        $start = intval($pages[0]);
        $end = intval($pages[1]);
        
        if($start == 0 || $end == 0){
            throw new Exception("Invalid Page Range");
        }
        
        if($start  > $end){
            return $this->parsePageRange($end, $start);
        }
        else{
            return $this->parsePageRange($start, $end);
        }
        
        
    }
    
    /**
     * 
     * @param array $file - associative array containing file properties of path,orientation and pages
     */
    protected function importFile($file){
        
        $filePath = $file['path'];
        
        $fileorientation = $filePath['orientation'];

        if(!in_array($fileorientation, Orientation::allowable())){
            $this->errors[] = 'Invalid orientation specified for file'.$filePath.'. Defaulting to default Potrait';
            $fileorientation = OutputFormat::STRING;
        }

        $pages = $file['pages'];

        $count = $this->fpdi->setSourceFile($filePath);

        if(trim($pages) == 'all'){
            $pages = "1-$count";
        }

        $filePages = $this->getPages($pages);
        
        $this->importPages($filePages, $filePath, $fileorientation);
    }
    
    /**
     * 
     * @param array $pages
     * @param string $filename
     * @param \Classes\Orientation $orientation $orientation
     * @throws Exception
     */
    protected function importPages(array $pages,$filename, Orientation $orientation){
        
        foreach ($pages as $page) {
            if (!$template = $this->fpdi->importPage($page)) {
                throw new Exception("Could not load page '$page' in PDF '$filename'. Check that the page exists.");
            }
            $size = $this->fpdi->getTemplateSize($template);

            $this->fpdi->AddPage($orientation, array($size['width'], $size['height']));
            $this->fpdi->useTemplate($template);
        }
    }
    /**
     * 
     * @param string $needle
     * @param array $haystack
     * @return boolean
     */
    protected function isValid($needle,array $haystack){
        return in_array($needle, $haystack);
    }
    /**
     * 
     * @param int $start
     * @param int $end
     * @return array
     */
    protected function parsePageRange($start,$end){
        
        $range = array();
        
        for($i= $start;$i<= $end;$i++){
            $range[] = $i;
        }
        
        return $range;
    }
    
}

