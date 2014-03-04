<?php
    namespace Maradik\Testing; 
    
    /**
     * Вопрос
     */
    class QuestionData extends QuestionAnswerData
    {        
        /**
         * @var int $parentId         
         */        
        public $parentId;
        
        /**
         * @var int $userId
         */
        public $userId;           

        /**
         * @var int $categoryId
         */
        public $categoryId;         
        
        /**                  
         * @param int $id         
         * @param string $title
         * @param string $description
         * @param int $categoryId                      
         * @param int $parentId  
         * @param int $order
         * @param int $createDate
         * @param int $userId 
         */
        public function __construct(
            $id             = 0,        
            $title          = "", 
            $description    = "",
            $categoryId     = 0, 
            $parentId       = 0,
            $order          = 0,            
            $createDate     = 0,
            $userId         = 0
        ) {
            parent::__construct($id, $title, $description, $order, $createDate);           

            $this->categoryId   = (int) $categoryId;
            $this->parentId     = (int) $parentId;
            $this->userId       = (int) $userId;
        }           
        
        /**
         * Представляет главный вопрос
         * 
         * @return boolean true - если вопрос главный, если вложенный - false
         */
        public function isMain()
        {
            return empty($this->parentId);
        }                            
    }    
