<?php
    namespace Maradik\Testing; 
    
    use Respect\Validation\Validator;

    /**
     * Базовый класс-контейнер для оперирования сущностями, хранящимися в БД
     */
    abstract class BaseData
    {
        /**
         * @var int $id
         */
        public $id;
        
        /**
         * @param int $id
         */
        public function __construct($id = 0) 
        {
            $this->id = (int) $id;
        }                
        
        /**
         * Проверка валидности данных в поле объекта. Принимает произвольное число аргументов (названия полей).
         *
         * @param string|string[]|null $fields Если не задано - проверяются все поля.
         * @return boolean|string[] Возвращает true в случае успеха, иначе - массив ошибок
         */
        final public function validate($fields = null) 
        {
            $allFields = array_keys(get_object_vars($this));
            $args = is_array($fields) ? $fields : func_get_args();
                
            if (!empty($args) && array_diff($args, $allFields)) {
                throw new \InvalidArgumentException('Некорректные аргументы в методе ' . __METHOD__);
            }              

            $fieldNames = empty($args) ? $allFields : $args;   
            unset($allFields);
            unset($args);
                
            $v = $this->validators($fieldNames);               
                
            try {
                Validator::allOf($v)->assert($this);
            } catch(\Respect\Validation\Exceptions\ValidationException $e) {
                return array_filter($e->findMessages($fieldNames), function($item) { return !empty($item); });
            }                 
                
            return true;               
        }    
        
        /**
         * @param string[] $fields Названия полей для проверки.
         * @return \Respect\Validation\Validatable[] Возвращает массив валидаторов.
         */
        protected function validators($fields) 
        {
            $v = array();
            
            if (in_array($f = 'id', $fields)) {
                $v[$f] = Validator::attribute($f, Validator::int()->min(0, true))
                    ->setName($f)
                    ->setTemplate('ID должно быть целым числом не меньше 0.');
            }
            
            return $v;
        }          
    }
