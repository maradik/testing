<?php
    require_once __DIR__.'/../src/BaseData.php';
    require_once __DIR__.'/../src/QuestionAnswerData.php';    
    require_once __DIR__.'/../src/AnswerData.php';        
    require_once __DIR__.'/../src/QuestionData.php';
    require_once __DIR__.'/../src/BaseRepository.php';
    require_once __DIR__.'/../src/QuestionRepository.php';
    require_once __DIR__.'/../src/AnswerRepository.php';
    
    use \Maradik\Testing\AnswerRepository;
    use \Maradik\Testing\AnswerData;
    
    $pdo = new PDO("mysql:host=localhost;dbname=voprosnik;charset=UTF8", "root", "");
    /*
    $sql = "CREATE TABLE IF NOT EXISTS `test_answer` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `title` varchar(255) NOT NULL,
              `description` varchar(1000) NOT NULL,
              `questionId` int(10) unsigned NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    
    $pdo->query($sql);
    */
    
        
    /**
     * @var \Maradik\Testing\AnswerRepository $qr
     */        
    $qr = new AnswerRepository($pdo, "answer", "test");
    $qr->install();
    /**
     * @var \Maradik\Testing\AnswerData $answer
     */      
     
    $answer = new AnswerData(0, "Ответ", "Описание-уточнение ответа", 1, 2, 123);
    echo $qr->insert($answer)."\n";
    var_dump($answer);    
    
    $answer->title .= " И добавочка еще ;)";
    $answer->questionId++;
    $qr->update($answer);
    var_dump($answer);    
    
    $answer = $qr->getById($answer->id);
    var_dump($answer);
    
    //$pdo->query("drop table `test_answer`");
?>