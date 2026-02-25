<?php

use PHPUnit\Framework\TestCase;

include_once("api/env.php");
include_once("api/utils.php");

require_once __DIR__ . '/../vendor/autoload.php';

use dao\BaseDAO;
use Basket\Feuille;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


class FeuilleTest extends TestCase
{
    private static $donnees;
    public Feuille $feuille;
    private int $outputBufferLevel;

    /**
     * Create database for all tests 
     * 
     */
    public static function setUpBeforeClass(): void    
    {
        loadEnv("tests/.env");
        loginfo("START FeuilleTest");
        self::$donnees = new BaseDAO();
        
        $sql= file_get_contents("config/createdb.sql");
        self::$donnees->exec($sql);      

        //ajout donnees de test
        self::$donnees->exec("INSERT INTO matchs(numero,equipe, jour, titre, score,collation,otm,maillots,adresse,horaire,rendezvous) ".
                        "VALUES('1234',1,'2025-09-27','match2','24/8','gontran','geo trouvetou','machine à laver','quelque part','12h15','11h20')");
        self::$donnees->exec("INSERT INTO matchs(numero,equipe, jour, titre, score,collation,otm,maillots,adresse,horaire,rendezvous) ".
                        "VALUES('1235',1,'2025-09-20','match1','0/0','donald','picsou','nobody','ici ou la bas','12h00','11h00')");
        self::$donnees->exec("INSERT INTO matchs(numero,equipe, jour, titre, score,collation,otm,maillots) ".
                        "VALUES('1236',2,'2025-10-05','match3','24/8','flagada','flairsou','rapetou')");

        self::$donnees->exec("INSERT INTO users(prenom,equipe,nom,licence,otm,charte) VALUES('riri',1,  'duck','BC011001',1,1)");
        self::$donnees->exec("INSERT INTO users(prenom,equipe,nom,licence,otm,charte) VALUES('fifi',1,  'duck','BC011002',0,1)");
        self::$donnees->exec("INSERT INTO users(prenom,equipe,nom,licence,otm,charte) VALUES('loulou',2,'duck','BC011003',1,0)");

        self::$donnees->exec("INSERT INTO selections(user,match,val) VALUES(1,1,1)");
        self::$donnees->exec("INSERT INTO selections(user,match,val) VALUES(2,1,1)");
        self::$donnees->exec("INSERT INTO selections(user,match,val) VALUES(3,1,1)");
        
        self::$donnees->exec("INSERT INTO matchinfos(user,match,opposition,numero) VALUES(3,1,'A',5)");
        self::$donnees->exec("INSERT INTO matchinfos(user,match,opposition,numero) VALUES(2,1,'B',10)");

        self::$donnees->exec("INSERT INTO staff(nom,prenom,licence,role) VALUES('zidane','zinedine','JH70356','entraineur')");
        self::$donnees->exec("INSERT INTO staff(nom,prenom,licence,role) VALUES('dus','jean-claude','JH48111','otm')");
        self::$donnees->exec("INSERT INTO staffmatchs(match,staff) VALUES(1,1)");

    }

    protected function tearDown(): void
    {
        // Clean up any remaining output buffers created during test
        while (ob_get_level() > $this->outputBufferLevel) {
            ob_end_clean();
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (isset(self::$donnees) && self::$donnees) {
            self::$donnees->close();
        }
        loginfo("STOP FeuilleTest");
    }

    /**
     * Initialize before each test
     * Create a new instance
     */
    protected function setUp(): void {
        $this->feuille = new Feuille();
        // record current output buffer level so tearDown can clean only what we created
        $this->outputBufferLevel = ob_get_level();
    }

    /**
     * GetArray
     */
    public function testGet()
    {
        // capture any output (the method writes the xlsx to php://output)
        ob_start();
        $this->feuille->get(1);
        $output = ob_get_clean();

        // write captured binary to a temporary file and load it with PhpSpreadsheet
        $tmp = tempnam(sys_get_temp_dir(), 'feu');
        file_put_contents($tmp, $output);

        $spreadsheet = IOFactory::load($tmp);
        // cleanup temp file
        unlink($tmp);

        $activeWorksheet = $spreadsheet->getActiveSheet();

        $this->assertEquals('1234', (string)$activeWorksheet->getCell('C3')->getValue());
        $this->assertEquals('27/09/2025', (string)$activeWorksheet->getCell('E3')->getValue());

        $this->assertEquals('BC011002', (string)$activeWorksheet->getCell('B8')->getValue());
        $this->assertEquals('duck', (string)$activeWorksheet->getCell('C8')->getValue());
        $this->assertEquals('fifi', (string)$activeWorksheet->getCell('D8')->getValue());

        $this->assertEquals('BC011003', (string)$activeWorksheet->getCell('B18')->getValue());
        $this->assertEquals('duck', (string)$activeWorksheet->getCell('C18')->getValue());
        $this->assertEquals('loulou', (string)$activeWorksheet->getCell('D18')->getValue());

        $this->assertEquals('JH70356', (string)$activeWorksheet->getCell('A27')->getValue());
        $this->assertEquals('zidane', (string)$activeWorksheet->getCell('C27')->getValue());
        $this->assertEquals('zinedine', (string)$activeWorksheet->getCell('D27')->getValue());

    }

}
