<?php
    namespace Maradik\Testing; 
    
    use Respect\Validation\Validator;    
    
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
         * @var string $linkUrl
         */
        public $linkUrl;
        
        /**
         * @var string $linkTitle
         */        
        public $linkTitle;
               
        /**
         * @param int $id           
         * @param string $title
         * @param string $description         
         * @param int $questionId
         * @param int $createDate
         * @param int $userId
         * @param string $linkUrl
         * @param string $linkTitle
         */
        public function __construct(
            $id             = 0, 
            $title          = "", 
            $description    = "",             
            $questionId     = 0, 
            $order          = 0,            
            $createDate     = 0,
            $userId         = 0,
            $linkUrl        = '',
            $linkTitle      = ''
        ) {
            parent::__construct($id, $title, $description, $order, $createDate, $userId);            

            $this->questionId = (int) $questionId;
            $this->linkUrl    = (string) $linkUrl;
            $this->linkTitle  = (string) $linkTitle;
        }    
        
        /**
         * @param string[] $fields Названия полей для проверки.
         * @return \Respect\Validation\Validatable[] Возвращает массив валидаторов.
         */
        protected function validators($fields) 
        {
            $v = parent::validators($fields);
            
            if (in_array($f = 'title', $fields)) {
                $v[$f] = Validator::attribute($f, Validator::string()->notEmpty()->length(1, 50))
                    ->setName($f)
                    ->setTemplate('Ответ должен быть длиной от 1 до 50 символов.');
            }

            if (in_array($f = 'description', $fields)) {
                $v[$f] = Validator::attribute($f, Validator::string()->length(0, 500))
                    ->setName($f)
                    ->setTemplate('Описание должно быть не более 500 символов.');
            }            
            
            if (in_array($f = 'questionId', $fields)) {
                $v[$f] = Validator::attribute($f, Validator::int()->notEmpty()->min(0))
                    ->setName($f)
                    ->setTemplate('Некорректное значение в поле Ссылка на вопрос.');
            }
            
            if (in_array($f = 'linkUrl', $fields)) {
                $v[$f] = Validator::attribute(
                    $f, 
                    Validator::oneOf(
                        Validator::string()->length(0), 
                        Validator::string()->length(1, 2000)->startsWith('http://')
                    ))
                    ->setName($f)
                    ->setTemplate('Ссылка должна быть не более 2000 символов и начинаться с \'http://\'.');
            }            

            if (in_array($f = 'linkTitle', $fields) && !empty($this->linkUrl)) {
                $v[$f] = Validator::attribute($f, Validator::string()->notEmpty()->length(1, 100))
                    ->setName($f)
                    ->setTemplate('Заголовок ссылки должен быть от 1 до 100 символов.');
            } 

            return $v;
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
                empty($json['createDate'])  ? 0 : (int) $json['createDate'],
                empty($json['userId'])      ? 0 : (int) $json['userId'],
                empty($json['linkUrl'])     ? "" : (string) $json['linkUrl'],
                empty($json['linkTitle'])   ? "" : (string) $json['linkTitle']
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
                'createDate'    => $this->createDate,
                'userId'        => $this->userId,       
                'linkUrl'       => $this->linkUrl,
                'linkTitle'     => $this->linkTitle
            );            
        }
    }
