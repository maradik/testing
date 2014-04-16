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
         * @var int $categoryId
         */
        public $categoryId;         

        /**
         * @var boolean $active
         */
        public $active;         
        
        /**                  
         * @param int $id         
         * @param string $title
         * @param string $description
         * @param int $categoryId                      
         * @param int $parentId  
         * @param int $order
         * @param int $createDate
         * @param int $userId 
         * @param boolean $active
         */
        public function __construct(
            $id             = 0,        
            $title          = "", 
            $description    = "",
            $categoryId     = 0, 
            $parentId       = 0,
            $order          = 0,            
            $createDate     = 0,
            $userId         = 0,
            $active         = false
        ) {
            parent::__construct($id, $title, $description, $order, $createDate, $userId);           

            $this->categoryId   = (int) $categoryId;
            $this->parentId     = (int) $parentId;   
            $this->active       = (boolean) $active;         
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
        
        /**
         * Возвращает экземпляр класса QuestionData, проинициализированный данными $json
         * 
         * @param array $json
         * @return QuestionData         
         */
        public static function createFromJson($json) 
        {
            $question = new QuestionData(
                empty($json['id'])          ? 0 : (int) $json['id'],
                empty($json['title'])       ? "" : (string) $json['title'],
                empty($json['description']) ? "" : (string) $json['description'],
                empty($json['categoryId'])  ? 0 : (int) $json['categoryId'],
                empty($json['parentId'])    ? 0 : (int) $json['parentId'],
                empty($json['order'])       ? 0 : (int) $json['order'],
                empty($json['createDate'])  ? 0 : (int) $json['createDate'],  
                empty($json['userId'])      ? 0 : (int) $json['userId'],
                empty($json['active'])      ? false : (boolean) $json['active']
            );
                                  
            return $question;
        }       
        
        /**
         * Возвращает объект в формате JSON
         * 
         * @return mixed         
         */        
        public function jsonSerialize()
        {
            return array(
                'id'            => $this->id,                
                'title'         => $this->title,
                'description'   => $this->description,   
                'categoryId'    => $this->categoryId,
                'parentId'      => $this->parentId,                
                'order'         => $this->order,
                'createDate'    => $this->createDate,   
                'userId'        => $this->userId,  
                'active'        => $this->active                                                   
            );
        }        
    }    
