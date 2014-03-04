<?php
    namespace Maradik\Testing;
    
    /**
     * Класс-контейнер для хранения вопроса или ответа
     */
    abstract class QuestionAnswerData extends BaseData
    {
        /**
         * @var string $title Заголовок       
         */        
        public $title;
        
        /**
         * @var string $description Описание        
         */        
        public $description;        
        
        /**
         * @var int $createDate Timestamp
         */
        public $createDate;  
        
        /**
         * @var int $order Порядок сортировки
         */
        public $order;        
        
        public function __construct(
            $id             = 0,
            $title          = "",
            $description    = "",
            $order          = 0,
            $createDate     = 0
        ) {
            parent::__construct($id);
            
            $this->title        = $title;   
            $this->description  = $description; 
            $this->order        = (int) $order;
            $this->createDate   = (int) $createDate;
        }                         
    }
