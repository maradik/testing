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
         * @var string $title Заголовок
         */
        public $title;
        
        /**
         * @var string $description Описание
         */
        public $description;
        
        /**
         * @var int $parentType Тип объекта, к которому привязан файл
         */
        public $parentType;            
        
        /**
         * @var int $parentId Id объекта, к которому привязан файл
         */
        public $parentId;
        
        /**
         * @var int $order Порядок сортировки
         */
        public $order;
        
        /**
         * @var int $createDate Timestamp
         */
        public $createDate;        
        
        /**
         * @var int $userId Владелец/Автор
         */
        public $userId;    
        
        /**
         * @var int $size Размер в КБ
         */
        public $size;               
        
        /**
         * @param int $id
         * @param string $fileName
         * @param string $origFileName
         * @param int $size
         * @param string $title
         * @param string $description
         * @param int $order
         * @param int $parentType
         * @param int $parentId
         * @param int $createDate
         * @param int $userId
         * @param int $type
         */
        public function __construct(
            $id             = 0,
            $fileName       = "",
            $origFileName   = "",
            $size           = 0,
            $title          = "",
            $description    = "",
            $order          = 0,
            $parentType     = 0,
            $parentId       = 0,
            $createDate     = 0,
            $userId         = 0,            
            $type           = self::TYPE_OTHER
        ) {
            parent::__construct($id);
            
            $this->fileName     = $fileName;
            $this->origFileName = $origFileName;
            $this->title        = $title;
            $this->description  = $description;
            $this->size         = (int) $size;
            $this->order        = (int) $order;
            $this->parentType   = (int) $parentType;
            $this->parentId     = (int) $parentId;
            $this->createDate   = (int) $createDate;
            $this->userId       = (int) $userId;
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
                        ->not(Validator::oneOf(Validator::contains('\/'), Validator::contains('\\')))
                    )
                    ->setName($f)
                    ->setTemplate('Имя файла должно быть длиной до 255 символов, без разделителей каталогов.');
            }   

            if (in_array($f = 'origFileName', $fields)) {
                $v[$f] = Validator::attribute(
                    $f, 
                    Validator::string()->notEmpty()->length(1, 255)
                        ->not(Validator::oneOf(Validator::contains('\/'), Validator::contains('\\')))
                    )
                    ->setName($f)
                    ->setTemplate('Оригинальное имя файла должно быть до 255 символов, без разделителей каталогов.');
            }                   
            
            if (in_array($f = 'size', $fields)) {
                $v[$f] = Validator::attribute($f, Validator::int()->notEmpty()->min(0))
                    ->setName($f)
                    ->setTemplate('Некорректный размер файла.');
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

            if (in_array($f = 'createDate', $fields)) {
                $v[$f] = Validator::attribute($f, Validator::int()->notEmpty()->min(0))
                    ->setName($f)
                    ->setTemplate('Некорректное значение в поле Дата создания.');
            }

            if (in_array($f = 'type', $fields)) {
                $v[$f] = Validator::attribute($f, Validator::int()->in(array(self::TYPE_OTHER, self::TYPE_IMAGE)))
                    ->setName($f)
                    ->setTemplate('Некорректный тип файла.');
            } 

            if (in_array($f = 'userId', $fields)) {
                $v[$f] = Validator::attribute($f, Validator::int()->notEmpty()->min(0))
                    ->setName($f)
                    ->setTemplate('Некорректное значение в поле Автор.');
            }   

            return $v;
        }          
    }
