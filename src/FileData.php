<?php
    namespace Maradik\Testing;   
    
    use Respect\Validation\Validator;    
    
    /**
     * Meta-данные файла
     */    
    class FileData extends BaseData
    {
        const TYPE_OTHER = 0;
        const TYPE_IMAGE = 1;
        
        /**
         * @var string $fileName Имя файла на сервере
         */
        public $fileName;      
        
        /**
         * @var string $origFileName Имя файла на клиенте при аплоаде
         */
        public $origFileName; 
        
        /**
         * @var int $type Тип файла
         */
        public $type;          
        
        /**
         * @var string $title
         */
        public $title;
        
        /**
         * @var string $description
         */
        public $description;
        
        /**
         * @var int $parentType
         */
        public $parentType;            
        
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
         * @param string $fileName
         * @param string $origFileName
         * @param string $title
         * @param string $description
         * @param int $order
         * @param int $parentType
         * @param int $parentId
         * @param int $type
         */
        public function __construct(
            $id             = 0,
            $fileName       = "",
            $origFileName   = "",
            $title          = "",
            $description    = "",
            $order          = 0,
            $parentType     = 0,
            $parentId       = 0,
            $type           = self::TYPE_OTHER
        ) {
            parent::__construct($id);
            
            $this->fileName     = $fileName;
            $this->origFileName = $origFileName;
            $this->title        = $title;
            $this->description  = $description;
            $this->order        = (int) $order;
            $this->parentType   = (int) $parentType;
            $this->parentId     = (int) $parentId;
            $this->type         = (int) $type;
        }
        
        /**
         * @param string[] $fields Названия полей для проверки.
         * @return \Respect\Validation\Validatable[] Возвращает массив валидаторов.
         */
        protected function validators($fields) 
        {
            $v = parent::validators($fields);
            
            if (in_array($f = 'fileName', $fields)) {
                $v[$f] = Validator::attribute(
                    $f, 
                    Validator::string()->notEmpty()->length(1, 255)
                        ->not(Validator::contains('/'))->not(Validator::contains('\\'))
                    )
                    ->setName($f)
                    ->setTemplate('Имя файла должно быть длиной до 255 символов, без разделителей каталогов.');
            }   

            if (in_array($f = 'origFileName', $fields)) {
                $v[$f] = Validator::attribute(
                    $f, 
                    Validator::string()->notEmpty()->length(1, 255)
                        ->not(Validator::contains('/'))->not(Validator::contains('\\'))
                    )
                    ->setName($f)
                    ->setTemplate('Оригинальное имя файла должно быть до 255 символов, без разделителей каталогов.');
            }                       
            
            if (in_array($f = 'title', $fields)) {
                $v[$f] = Validator::attribute($f, Validator::string()->notEmpty()->length(1, 100))
                    ->setName($f)
                    ->setTemplate('Заголовок должен быть длиной от 1 до 100 символов.');
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
            
            if (in_array($f = 'parentType', $fields)) {
                $v[$f] = Validator::attribute($f, Validator::int()->min(0, true))
                    ->setName($f)
                    ->setTemplate('Некорректный тип ссылки.');
            }            
            
            if (in_array($f = 'parentId', $fields)) {
                $v[$f] = Validator::attribute($f, Validator::int()->notEmpty()->min(0))
                    ->setName($f)
                    ->setTemplate('Некорректная ссылка.');
            }       

            if (in_array($f = 'type', $fields)) {
                $v[$f] = Validator::attribute($f, Validator::int()->in(array(self::TYPE_OTHER, self::TYPE_IMAGE)))
                    ->setName($f)
                    ->setTemplate('Некорректный тип файла.');
            } 

            return $v;
        }          
    }