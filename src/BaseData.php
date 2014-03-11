<?php
    namespace Maradik\Testing; 

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
    }
