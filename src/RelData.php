<?php
    namespace Maradik\Testing; 
    
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
    }
