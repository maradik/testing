<?php
    namespace Maradik\Testing;
    
    require_once '../vendor/autoload.php';
    
    $pdo = new PDO("mysql:host=localhost;dbname=voprosnik;charset=UTF8", "root", "");
    
    $sql = "CREATE TABLE IF NOT EXISTS `test_question` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `title` varchar(255) NOT NULL,
              `description` varchar(1000) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    
    $pdo->query($sql);
               
    $qr = new QuestionRepository($pdo, "question", "test");
    
    $arr = $qr->get(array('title' => 'Вопрос И добавочка еще ;)', 'id' => '3'));
    
    var_dump($arr);
    
    //$pdo->query("drop table `test_question`");
?>