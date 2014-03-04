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
    }
