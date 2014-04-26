<?php
    namespace Maradik\Testing;
    
    use Respect\Validation\Validator;    
    
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
         * @param string[] $fields Названия полей для проверки.
         * @return \Respect\Validation\Validatable[] Возвращает массив валидаторов.
         */
        protected function validators($fields) 
        {
            $v = parent::validators($fields);
            
            if (in_array($f = 'title', $fields)) {
                $v[$f] = Validator::attribute($f, Validator::string()->notEmpty()->length(1, 150))
                    ->setName($f)
                    ->setTemplate('Заголовок должен быть длиной от 1 до 150 символов.');
            }

            if (in_array($f = 'description', $fields)) {
                $v[$f] = Validator::attribute($f, Validator::string()->length(0, 1000))
                    ->setName($f)
                    ->setTemplate('Описание должно быть не более 1000 символов.');
            }    
            
            if (in_array($f = 'order', $fields)) {
                $v[$f] = Validator::attribute($f, Validator::int())
                    ->setName($f)
                    ->setTemplate('Некорректное значение в поле Порядок сортировки.');
            }            
            
            if (in_array($f = 'createDate', $fields)) {
                $v[$f] = Validator::attribute($f, Validator::int()->notEmpty()->min(0))
                    ->setName($f)
                    ->setTemplate('Некорректное значение в поле Дата создания.');
            }     
            
            if (in_array($f = 'userId', $fields)) {
                $v[$f] = Validator::attribute($f, Validator::int()->notEmpty()->min(0))
                    ->setName($f)
                    ->setTemplate('Некорректное значение в поле Автор.');
            }                             
            
            return $v;
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
