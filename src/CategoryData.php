<?php
    namespace Maradik\Testing;   
    
    use Respect\Validation\Validator;    
    
    /**
     * Категория
     */    
    class CategoryData extends BaseData
    {
        /**
         * @var string $title
         */
        public $title;
        
        /**
         * @var string $description
         */
        public $description;
        
        /**
         * @var int $parentId
         */
        public $parentId;
        
        /**
         * @var int $order
         */
        public $order;
        
        /**
         * @param int $id
         * @param string $title
         * @param string $description
         * @param int $order
         */
        public function __construct(
            $id             = 0,
            $title          = "",
            $description    = "",
            $order          = 0,
            $parentId       = 0
        ) {
            parent::__construct($id);
            
            $this->title        = $title;
            $this->description  = $description;
            $this->order        = (int) $order;
            $this->parentId     = (int) $parentId;
        }
        
        /**
         * @param string[] $fields Названия полей для проверки.
         * @return \Respect\Validation\Validatable[] Возвращает массив валидаторов.
         */
        protected function validators($fields) 
        {
            $v = parent::validators($fields);
            
            if (in_array($f = 'title', $fields)) {
                $v[$f] = Validator::attribute($f, Validator::string()->notEmpty()->length(1, 30))
                    ->setName($f)
                    ->setTemplate('Заголовок должен быть длиной от 1 до 30 символов.');
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
            
            if (in_array($f = 'parentId', $fields)) {
                $v[$f] = Validator::attribute($f, Validator::int()->min(0, true))
                    ->setName($f)
                    ->setTemplate('Некорректная ссылка.');
            }       

            return $v;
        }          
    }
