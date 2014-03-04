<?php
    namespace Maradik\Testing;   
    
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
    }
