<?php
    namespace Maradik\Testing;
    
    require_once '../vendor/autoload.php';
    
    $db = new PDO(
        "mysql:host=localhost;dbname=hinter;charset=UTF8", 
        'root', 
        ''
    );

    $cRepository = new CategoryRepository($db, 'category');
    $mqRepository = new QuestionRepository($db, 'mainquestion');
    $maRepository = new AnswerRepository($db, 'mainanswer');
    
    $q = new Query($cRepository);
    $q2 = $q->join($mqRepository)
        ->addLink(new DataLink('id', 'categoryId'))
        ->addFilter(new DataFilter('id', 10))
        ->join($maRepository)
        ->addLink(new DataLink('id', 'questionId'))
        /*->addFilter(new DataFilter('id', 3))*/;
      //  ->addFilter(new DataFilter('title', 'wertwert'));
      
    //$q = new Query($mqRepository);
    $q2 = $mqRepository->query()
        ->addFilterField('categoryId', 0, '>=') 
        ->addFilterField('categoryId', 4, '<=') 
        ->addSortField('categoryId', Query::SORT_DESC)
        ->setHidden()          
        ->join($cRepository, Query::JOIN_LEFT_OUTER)
        ->addLinkFields('categoryId', 'id')    
        ->addSortField('order', Query::SORT_DESC);      
        
    list($sql, $params) = $q2->buildSql();
    print $sql . "\n";      
    
    $res = $q2->get();
    print_r($res);
