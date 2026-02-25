<?php

namespace Basket;
require_once('utils.php');

use Basket\Matchs;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Feuille {
	private $matchs;

	public function __construct() {
		$this->matchs = new Matchs();        
	}

    public function get($match) {
        $match = $this->matchs->getArray($match);

        $spreadsheet = new Spreadsheet();
        $inputFileName = getenv("TEMPLATE_FILE");

        $spreadsheet = IOFactory::load($inputFileName);
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setCellValue('C3', $match->numero);
        $date = \DateTime::createFromFormat('Y-m-d', $match->jour);
        $activeWorksheet->setCellValue('E3', $date->format('d/m/Y'));
        
        $this->writeopposition($activeWorksheet,$match->oppositions->B,8);
        $this->writeopposition($activeWorksheet,$match->oppositions->A,18);
        $this->writestaff($activeWorksheet,$match->entraineurs,27);
        //$this->writestaff($activeWorksheet,$match->otm,32);
        $writer = new Xlsx($spreadsheet);
        
        //$writer->save("../feuille_match_$match->numero.xlsx");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="feuille_match_' . $match->numero . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');        

    }

    private function writeopposition($sheet,$opp,$start) 
    {
        $numligne=$start;
        foreach($opp as $joueur) {
            $sheet->setCellValue("A$numligne", $joueur->numero);
            $sheet->setCellValue("B$numligne", $joueur->licence);
            $sheet->setCellValue("C$numligne", $joueur->nom);
            $sheet->setCellValue("D$numligne", $joueur->prenom);
            $numligne++;
        }
    }

    private function writestaff($sheet,$coachs,$start) 
    {
        $numligne=$start;
        foreach($coachs as $coach) {
            $sheet->setCellValue("A$numligne", $coach->licence);
            $sheet->setCellValue("C$numligne", $coach->nom);
            $sheet->setCellValue("D$numligne", $coach->prenom);
            $numligne++;
        }
    }

}



