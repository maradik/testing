<?php
    namespace Maradik\Testing;
    
    class DataLink extends DataRelation
    {
        protected $parentField;
        protected $childField;
        
        public function getParentField()
        {
            return $this->parentField;    
        }
        
        protected function setParentField($parentField)
        {
            $this->parentField  = $parentField;
        }        
        
        public function getChildField()
        {
            return $this->childField;    
        }       
        
        protected function setChildField($childField)
        {
            $this->childField   = $childField;            
        }    
        
        public function __construct($parentField, $childField, $relationType = '=')
        {
            parent::__construct($relationType);
            $this->setParentField($parentField);
            $this->setChildField($childField);
        }
        
        public function __toString()
        {
            return "{$this->field} {$this->relationType} {$this->value}";
        }        
    }        
