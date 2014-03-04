<?php
    require_once __DIR__.'/../src/BaseData.php';
    require_once __DIR__.'/../src/RelData.php';    
    require_once __DIR__.'/../src/AnswerData.php';        
    require_once __DIR__.'/../src/QuestionData.php';
    require_once __DIR__.'/../src/BaseRepository.php';
    require_once __DIR__.'/../src/QuestionRepository.php';
    require_once __DIR__.'/../src/AnswerRepository.php';
    require_once __DIR__.'/../src/RelRepository.php';
    
    use \Maradik\Testing\RelRepository;
    use \Maradik\Testing\RelData;
    
    $pdo = new PDO("mysql:host=localhost;dbname=voprosnik;charset=UTF8", "root", "");
    /*
    $sql = "CREATE TABLE IF NOT EXISTS `test_rel` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `parentId` int(10) unsigned NOT NULL,
              `childId` int(10) unsigned NOT NULL,
              PRIMARY KEY (`id`),
              KEY `rel_parentId` (`parentId`),
              KEY `rel_childId` (`childId`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    
    $pdo->query($sql);
    */
        
    /**
     * @var \Maradik\Testing\RelRepository $qr
     */        
    $qr = new RelRepository($pdo, "rel", "test");
    $qr->install();
    /**
     * @var \Maradik\Testing\RelData $answer
     */      
    $rel = new RelData(0, 10, 20);
    echo $qr->insert($rel)."\n";
    var_dump($rel);    
    
    $rel->parentId++;
    $rel->childId++;
    $qr->update($rel);
    var_dump($rel);    
    
    $rel = $qr->getById($rel->id);
    var_dump($rel);
    
    //$pdo->query("drop table `test_rel`");
?>