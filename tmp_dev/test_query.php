<?php
    /*
    define('CLASS_DIR', '../src/');
    set_include_path(get_include_path().PATH_SEPARATOR.CLASS_DIR);
    spl_autoload_extensions('.php');
    spl_autoload_register();
    */
    function autoload($class_name) {
        $class_name = str_replace('Maradik\\Testing\\', '', $class_name);        
        require_once __DIR__.'/../src/' . $class_name . '.php';
    }

    spl_autoload_register('autoload');
   

    use Maradik\Testing\Query;
    use Maradik\Testing\CategoryRepository;
    use Maradik\Testing\QuestionRepository;
    use Maradik\Testing\AnswerRepository;
    use Maradik\Testing\DataFilter;
    use Maradik\Testing\DataLink;
    
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
        ->addSortField('order', Query::SORT_DESC)
        ->setHidden();      
        
    list($sql, $params) = $q2->buildSql();
    print $sql . "\n";      
    
    $res = $q2->getEntity();
    print_r($res);
