<?php
    namespace Maradik\Testing;
    
    class DataFilter extends DataRelation
    {
        protected $field;
        protected $value;
        
        public function getField()
        {
            return $this->field;    
        }
        
        protected function setField($field)
        {
            $this->field = $field;
        }        
        
        public function getValue()
        {
            return $this->value;    
        }       
        
        protected function setValue($value)
        {
            $this->value = $value;            
        }    
        
        public function __construct($field, $value, $relationType = '=')
        {
            parent::__construct($relationType);
            $this->setField($field);
            $this->setValue($value);
        }
        
        public function __toString()
        {
            return "{$this->field} {$this->relationType} {$this->value}";
        }
    }        
