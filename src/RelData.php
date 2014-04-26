<?php
    namespace Maradik\Testing; 
    
    use Respect\Validation\Validator;    
    
    /**
     * Класс-контейнер для оперирования сущностями, представляющими собой связь Parent-Child
     */    
    class RelData extends BaseData    
    {
        /**
         * @var int $parentId Ссылка на родительский элемент
         */
        public $parentId;
        
        /**
         * @var int $childId Ссылка на дочерний элемент
         */
        public $childId;
        
        /**
         * @param int $id
         * @param int $parentId
         * @param int $childId
         */
        public function __construct($id = 0, $parentId = 0, $childId = 0)
        {
            parent::__construct($id);
            
            $this->parentId = (int) $parentId;
            $this->childId  = (int) $childId;
        }
        
        /**
         * @param string[] $fields Названия полей для проверки.
         * @return \Respect\Validation\Validatable[] Возвращает массив валидаторов.
         */
        protected function validators($fields) 
        {
            $v = parent::validators($fields);
            
            if (in_array($f = 'parentId', $fields)) {
                $v[$f] = Validator::attribute($f, Validator::int()->notEmpty()->min(0, true))
                    ->setName($f)
                    ->setTemplate('Некорректная ссылка на родительскую сущность.');
            }  
            
            if (in_array($f = 'childId', $fields)) {
                $v[$f] = Validator::attribute($f, Validator::int()->notEmpty()->min(0, true))
                    ->setName($f)
                    ->setTemplate('Некорректная ссылка на дочернюю сущность.');
            }                  

            return $v;
        }          
    }
