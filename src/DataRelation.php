<?php
    namespace Maradik\Testing;
    
    class DataRelation
    {
        protected $relationType;        
        
        public function getRelationType()
        {
            return $this->relationType;    
        }        
        
        protected function setRelationType($relationType)
        {
            switch ($relationType) {
                case '=':
                case '>':                    
                case '<':
                case '>=':
                case '<=':
                case '<>':                    
                    $this->relationType = $relationType;                     
                    break;                                    
                default:
                    $this->relationType = '=';
                    break;
            }
        }
        
        public function __construct($relationType = '=')
        {
            $this->setRelationType($relationType);
        }                  
    }
