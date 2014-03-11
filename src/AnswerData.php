<?php
    namespace Maradik\Testing; 
    
    /**
     * Ответ на вопрос
     */
    class AnswerData extends QuestionAnswerData
    {               
        /**
         * @var int $questionId
         */        
        public $questionId;
               
        /**
         * @param int $id           
         * @param string $title
         * @param string $description         
         * @param int $questionId
         * @param int $createDate
         */
        public function __construct(
            $id             = 0, 
            $title          = "", 
            $description    = "",             
            $questionId     = 0, 
            $order          = 0,            
            $createDate     = 0
        ) {
            parent::__construct($id, $title, $description, $order, $createDate);            

            $this->questionId = (int) $questionId;
        }    
        
        /**
         * Возвращает экземпляр класса AnswerData, проинициализированный данными $json
         * 
         * @param array $json
         * @return AnswerData         
         */
        public static function createFromJson($json) 
        {
            $answer = new AnswerData(
                empty($json['id'])          ? 0 : (int) $json['id'],
                empty($json['title'])       ? "" : (string) $json['title'],
                empty($json['description']) ? "" : (string) $json['description'],
                empty($json['questionId'])  ? 0 : (int) $json['questionId'],
                empty($json['order'])       ? 0 : (int) $json['order'],
                empty($json['createDate'])  ? 0 : (int) $json['createDate']
            );                                                             
                                  
            return $answer;
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
                'questionId'    => $this->questionId,
                'order'         => $this->order,                
                'createDate'    => $this->createDate                                                     
            );            
        }
    }
