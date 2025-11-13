<?php

use PHPUnit\Framework\TestCase;

include_once("api/env.php");
include_once("api/donnees.php");
include_once("api/matchinfos.php");


class MatchsTest extends TestCase
{
    private static $donnees;
    public Matchs $matchs;
    public MatchInfos $matchInfos;

    /**
     * Create database for all tests 
     * 
     */
    public static function setUpBeforeClass(): void    
    {
        loadEnv("tests/.env");
        self::$donnees = new Donnees();
        
        $sql= file_get_contents("config/createdb.sql");
        self::$donnees->db->exec($sql);      

        //ajout donnees de test
        self::$donnees->db->exec("INSERT INTO matchs(equipe, jour, titre, score,collation,otm,maillots,adresse,horaire,rendezvous) ".
                        "VALUES(1,'2025-09-27','match2','24/8','gontran','geo trouvetou','machine à laver','quelque part','12h15','11h20')");
        self::$donnees->db->exec("INSERT INTO matchs(equipe, jour, titre, score,collation,otm,maillots,adresse,horaire,rendezvous) ".
                        "VALUES(1,'2025-09-20','match1','0/0','donald','picsou','nobody','ici ou la bas','12h00','11h00')");
        self::$donnees->db->exec("INSERT INTO matchs(equipe, jour, titre, score,collation,otm,maillots) ".
                        "VALUES(2,'2025-10-05','match3','24/8','flagada','flairsou','rapetou')");

        self::$donnees->db->exec("INSERT INTO users(prenom,equipe,nom,licence,otm,charte) VALUES('riri',1,  'duck','BC011001',1,1)");
        self::$donnees->db->exec("INSERT INTO users(prenom,equipe,nom,licence,otm,charte) VALUES('fifi',1,  'duck','BC011002',0,1)");
        self::$donnees->db->exec("INSERT INTO users(prenom,equipe,nom,licence,otm,charte) VALUES('loulou',2,'duck','BC011003',1,0)");

        self::$donnees->db->exec("INSERT INTO selections(user,match,val) VALUES(1,1,1)");
        self::$donnees->db->exec("INSERT INTO selections(user,match,val) VALUES(2,1,1)");
        self::$donnees->db->exec("INSERT INTO selections(user,match,val) VALUES(3,1,1)");
        
        self::$donnees->db->exec("INSERT INTO matchinfos(user,match,opposition) VALUES(3,1,'A')");
        self::$donnees->db->exec("INSERT INTO matchinfos(user,match,opposition) VALUES(2,1,'B')");

    }

    /**
     * Initialize before each test
     * Create a new instance
     */
    protected function setUp(): void {
        $this->matchInfos = new MatchInfos(self::$donnees);
        $this->matchs = new Matchs(self::$donnees, $this->matchInfos);
    }

    /**
     * Pour tester l'interface on verifie que l'on genere bien le json de sortie 
     * definie dans le repertoire data et qui est utilisé par les tests du front (vitest)
     */
    public function testGet() 
    {
        ob_start();
        $this->matchs->get();
        $output = ob_get_clean();

        $json=json_decode($output,true);

        $expected = json_decode(file_get_contents('tests/data/matchs.json'),true);
        $this->assertEquals($expected, $json);
   
    }

    /**
     * Pour tester l'interface on verifie que l'on genere bien le json de sortie 
     * definie dans le repertoire data et qui est utilisé par les tests du front (vitest)
     */
    public function testGetAvecSelections() 
    {
        ob_start();
        $this->matchs->getAvecSelections();
        $output = ob_get_clean();

        $json=json_decode($output,true);

        $expected = json_decode(file_get_contents('tests/data/matchsavecsel.json'),true);
        $this->assertEquals($expected, $json);
   
    }


    /**
     * GetArray
     */
    public function testGetArrayAllMatchs()
    {
        $result = $this->matchs->getArray();

        // On doit avoir 3 enregistrement de l'objet Matchs
        $this->assertIsArray($result);
        $this->assertEquals(3,count($result));
      
        //On verifie que les champs du 1er enreg sont ok
        $this->assertEquals(1,$result[0]->equipe);
        $this->assertEquals("2025-09-20",$result[0]->jour);
        $this->assertEquals("match1",$result[0]->titre);
        $this->assertEquals("0/0",$result[0]->score);
        $this->assertEquals("donald",$result[0]->collation);
        $this->assertEquals("picsou",$result[0]->otm);
        $this->assertEquals("nobody",$result[0]->maillots);
        $this->assertEquals("ici ou la bas",$result[0]->adresse);
        $this->assertEquals("12h00",$result[0]->horaire);
        $this->assertEquals("11h00",$result[0]->rendezvous);
        //L'ordre des enregistrements 
        $this->assertEquals(2,$result[0]->id);
        $this->assertEquals(1,$result[1]->id);
        $this->assertEquals(3,$result[2]->id);
    }

    public function testGetArrayOneMatch()
    {
        $result = $this->matchs->getArray(1);

        // On doit avoir 1 enregistrement de l'objet Matchs
        $this->assertIsObject($result);
      
        //On verifie que les champs sont ok
        $this->assertEquals(1,$result->id);
        $this->assertEquals(1,$result->equipe);
        $this->assertEquals("2025-09-27",$result->jour);
        $this->assertEquals("match2",$result->titre);
        $this->assertEquals("24/8",$result->score);
        $this->assertEquals("gontran",$result->collation);
        $this->assertEquals("geo trouvetou",$result->otm);
        $this->assertEquals("machine à laver",$result->maillots);
        $this->assertEquals("quelque part",$result->adresse);
        $this->assertEquals("12h15",$result->horaire);
        $this->assertEquals("11h20",$result->rendezvous);

        $this->assertEquals(3,$result->oppositions->A[0]->user);
        $this->assertEquals(2,$result->oppositions->B[0]->user);
    }
 


    /**
     * Doit retourner la liste des matchs par jour
     * Avec la liste des joueurs dans chaque opposition
     */
    function test_getMatchsAvecOppositionsArray() {

        $results = $this->matchs->getAvecOppositionsArray();

        // On doit avoir 3 enregistrement de l'objet Matchs
        $this->assertIsArray($results);
        $this->assertEquals(3,count($results));
        $this->assertInstanceOf(stdClass::class, $results[0]);
        
        //On verifie que l'ordre est croissant
        $this->assertEquals('2025-09-20',$results[0]->jour);
        $this->assertEquals('2025-09-27',$results[1]->jour);
        $this->assertEquals('2025-10-05',$results[2]->jour);

        //Il y a un match par jour
        $this->assertIsArray($results[0]->matchs);
        $this->assertEquals(1,count($results[0]->matchs));        
        $this->assertIsArray($results[1]->matchs);
        $this->assertEquals(1,count($results[1]->matchs));
        $this->assertIsArray($results[2]->matchs);
        $this->assertEquals(1,count($results[2]->matchs));
        
        //On verifie que c'est les bon match
        $this->assertEquals(2,$results[0]->matchs[0]->id);
        $this->assertEquals(1,$results[1]->matchs[0]->id);
        $this->assertEquals(3,$results[2]->matchs[0]->id);

        //Pour le match du 1er jour il n'y a aucun joueur    
        //$this->assertIsArray($results[0]->matchs[0]->oppositions);
        $this->assertIsArray($results[0]->matchs[0]->oppositions->A);
        $this->assertIsArray($results[0]->matchs[0]->oppositions->B);
        $this->assertIsArray($results[0]->matchs[0]->oppositions->Autres);
        $this->assertEquals(0,count($results[0]->matchs[0]->oppositions->A));
        $this->assertEquals(0,count($results[0]->matchs[0]->oppositions->B));
        $this->assertEquals(0,count($results[0]->matchs[0]->oppositions->Autres));

        //Pour le match du 2e jour les 1 joueurs n'est pas positionnés dans une opposition
        //les 2 autres sont chacun dans une opposition
        $this->assertEquals(1,count($results[1]->matchs[0]->oppositions->A));
        $this->assertEquals(1,count($results[1]->matchs[0]->oppositions->B));
        $this->assertEquals(1,count($results[1]->matchs[0]->oppositions->Autres));

        //Verifie les joueurs de l'opposition autres du match du 2e jour        
        $this->assertEquals(2,$results[1]->matchs[0]->oppositions->B[0]->user);
        $this->assertEquals('fifi',$results[1]->matchs[0]->oppositions->B[0]->prenom);
        $this->assertEquals(3,$results[1]->matchs[0]->oppositions->A[0]->user);
        $this->assertEquals('loulou',$results[1]->matchs[0]->oppositions->A[0]->prenom);
        $this->assertEquals(1,$results[1]->matchs[0]->oppositions->Autres[0]->user);
        $this->assertEquals('riri',$results[1]->matchs[0]->oppositions->Autres[0]->prenom);

        //Pour le match du 3e jour il n'y a aucun joueur
        $this->assertEquals(0,count($results[2]->matchs[0]->oppositions->A));
        $this->assertEquals(0,count($results[2]->matchs[0]->oppositions->B));
        $this->assertEquals(0,count($results[2]->matchs[0]->oppositions->Autres));
    }



    /**
     * Doit retourner la liste des matchs par jours 
     * Avec la liste des joueurs selectionés (c'est à dire qu'ils sont soit dans la A soit dans la B)
     */
    function test_getMatchsAvecSelectionsArray() {

        $results = $this->matchs->getAvecSelectionsArray();

        // On doit avoir 3 enregistrement de l'objet Matchs
        $this->assertIsArray($results);
        $this->assertEquals(3,count($results));
        $this->assertIsObject($results[0]);
        
        //On verifie que l'ordre est croissant
        $this->assertEquals('2025-09-20',$results[0]->jour);
        $this->assertEquals('2025-09-27',$results[1]->jour);
        $this->assertEquals('2025-10-05',$results[2]->jour);

        //Il y a un match par jour
        $this->assertIsArray($results[0]->matchs);
        $this->assertEquals(1,count($results[0]->matchs));        
        $this->assertIsArray($results[1]->matchs);
        $this->assertEquals(1,count($results[1]->matchs));
        $this->assertIsArray($results[2]->matchs);
        $this->assertEquals(1,count($results[2]->matchs));
        
        //On verifie que c'est les bon match
        $this->assertEquals(2,$results[0]->matchs[0]->id);
        $this->assertEquals(1,$results[1]->matchs[0]->id);
        $this->assertEquals(3,$results[2]->matchs[0]->id);

        //Pour le match du 1er jour il n'y a aucun joueur selectionné  
        $this->assertIsArray($results[0]->matchs[0]->selections);
        $this->assertEquals(0,count($results[0]->matchs[0]->selections));

        //Pour le match du 2e jour 2 joueurs sont selectionnés
        $this->assertIsArray($results[1]->matchs[0]->selections);
        $this->assertEquals(2,count($results[1]->matchs[0]->selections));

        //Verifie les joueurs de l'opposition autres du match du 2e jour 
        $this->assertIsObject($results[1]->matchs[0]->selections[0]);
        $this->assertEquals(2,$results[1]->matchs[0]->selections[0]->user);
        $this->assertEquals('fifi',$results[1]->matchs[0]->selections[0]->prenom);
        $this->assertIsObject($results[1]->matchs[0]->selections[1]);
        $this->assertEquals(3,$results[1]->matchs[0]->selections[1]->user);
        $this->assertEquals('loulou',$results[1]->matchs[0]->selections[1]->prenom);

        //Pour le match du 3e jour il n'y a aucun joueur
        $this->assertIsArray($results[2]->matchs[0]->selections);
        $this->assertEquals(0,count($results[2]->matchs[0]->selections));
    }


    public function test_updateMatch() {
        
        // Call protected method using Reflection
        $reflection = new ReflectionClass(get_class($this->matchs));
        $method = $reflection->getMethod('update');
        $method->setAccessible(true);
        $method->invoke($this->matchs,1,2,'match numero 1','8/9','2025-10-06',"germaine","personne","gigi","la bas","15h30", "Au gymnase");

        $result = $this->matchs->getArray(1);
     
        //On verifie que les champs sont ok
        $this->assertEquals(1,$result->id);
        $this->assertEquals(2,$result->equipe);
        $this->assertEquals("2025-10-06",$result->jour);
        $this->assertEquals("match numero 1",$result->titre);
        $this->assertEquals("8/9",$result->score);
        $this->assertEquals("germaine",$result->collation);
        $this->assertEquals("personne",$result->otm);
        $this->assertEquals("gigi",$result->maillots);
        $this->assertEquals("la bas",$result->adresse);
        $this->assertEquals("15h30",$result->horaire);
        $this->assertEquals("Au gymnase",$result->rendezvous);        
    }


    public function test_ajouteMatch() {
        
        // Call protected method using Reflection
        $reflection = new ReflectionClass(get_class($this->matchs));
        $method = $reflection->getMethod('ajoute');
        $method->setAccessible(true);
        $method->invoke($this->matchs,2,'match numero 4','5/9','2025-11-08',"bubulle","jojo","coco","ici","11h","quelquepart");

        $result = $this->matchs->getArray(4);
     
        //On verifie que les champs sont ok
        $this->assertEquals(4,$result->id);
        $this->assertEquals(2,$result->equipe);
        $this->assertEquals("2025-11-08",$result->jour);
        $this->assertEquals("match numero 4",$result->titre);
        $this->assertEquals("5/9",$result->score);
        $this->assertEquals("bubulle",$result->collation);
        $this->assertEquals("jojo",$result->otm);
        $this->assertEquals("coco",$result->maillots);
        $this->assertEquals("ici",$result->adresse);
        $this->assertEquals("11h",$result->horaire);
        $this->assertEquals("quelquepart",$result->rendezvous);
        
        //Supprime cet enregistrement
        self::$donnees->db->exec("DELETE FROM matchs WHERE id=4");
    }


    public function test_supprimeMatch() {

        self::$donnees->db->exec("INSERT INTO matchs(id,equipe,titre) VALUES(99,3,'to be deleted')");
        self::$donnees->db->exec("INSERT INTO selections(match,user,val) VALUES(99,1,1)");
        self::$donnees->db->exec("INSERT INTO matchinfos(match,user) VALUES(99,1)");

        // Call protected method using Reflection
        $reflection = new ReflectionClass(get_class($this->matchs));
        $method = $reflection->getMethod('supprime');
        $method->setAccessible(true);
        $method->invoke($this->matchs,99);

        // Verifie que les enregistrements ont disparus dans les 3 tables
        $results=self::$donnees->db->query("SELECT count(*) from matchs where id=99");
        while ($row = $results->fetchArray()) {
            $this->assertEquals(0,$row[0]);
        }
        $results=self::$donnees->db->query("SELECT count(*) from selections where match=99");
        while ($row = $results->fetchArray()) {
            $this->assertEquals(0,$row[0]);
        }
        $results=self::$donnees->db->query("SELECT count(*) from matchinfos where match=99");
        while ($row = $results->fetchArray()) {
            $this->assertEquals(0,$row[0]);
        }
    }

}
