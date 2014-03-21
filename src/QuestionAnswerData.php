<?php
    namespace Maradik\Testing;
    
    /**
     * Класс-контейнер для хранения вопроса или ответа
     */
    abstract class QuestionAnswerData extends BaseData //implements JsonSerializable
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
        
        /**
         * @var int $userId
         */
        public $userId;           
        
        public function __construct(
            $id             = 0,
            $title          = "",
            $description    = "",
            $order          = 0,
            $createDate     = 0,
            $userId         = 0
        ) {
            parent::__construct($id);
            
            $this->title        = $title;   
            $this->description  = $description; 
            $this->order        = (int) $order;
            $this->createDate   = (int) $createDate;
            $this->userId       = (int) $userId;
        }                  
        
        /**
         * Возвращает экземпляр класса QuestionAnswerData, проинициализированный данными $json
         * 
         * @param array $json
         * @return QuestionAnswerData         
         */
        abstract public static function createFromJson($json);
        
        /**
         * Возвращает объект QuestionAnswerData в формате JSON
         * 
         * @return mixed         
         */        
        abstract public function jsonSerialize();
    }
