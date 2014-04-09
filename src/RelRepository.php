<?php
    namespace Maradik\Testing;        
    
    class RelRepository extends BaseRepository
    {                              
        /**
         * @param int $id
         * @return RelData
         */
        public function getById($id)
        {
            return parent::getById($id);                        
        }        

        /**
         * @param int $parentId
         * @return RelData
         */
        public function getByParentId($parentId)
        {
            return parent::getById($id);                        
        }  

        /**
         * @param RelData $rel
         * @return boolean
         */
        public function insert(RelData $rel)
        {
            return parent::insert($rel);  
        }

        /**
         * @param RelData $rel
         * @return boolean
         */
        public function update(RelData $rel)
        {
            return parent::update($rel);                       
        }                     
    
        /**
         * @return RelData
         */
        protected function rowToObject(array $row)
        {
            $relData = new RelData(
                $row['id'],
                $row['parentId'],
                $row['childId']
            );
            
            return $relData;
        }
        
        //TODO Удалить метод, определен в родителе
        /**
         * @param RelData $rel
         * @return array
         */
        /*
        protected function objectToRow(BaseData $rel)
        {            
            $row = array();
            $row['id']       = $rel->id;
            $row['parentId'] = $rel->parentId;
            $row['childId']  = $rel->childId;
            
            return $row;
        }   
        */     
        
       /**
         * Используется для задания правила отображения поля сущности в поле таблицы. 
         *
         * @return array Массив["поле_сущности"] = "поле_таблицы"
         */
        protected function tableFields()
        {
            return array(
                'id'          => 'id',
                'parentId'    => 'parentId',
                'childId'     => 'childId'
            );            
        }          

        /**
         * Создание необходимых таблиц в БД и первичная настройка
         *          
         * @return boolean true в случае успеха, иначе false
         */        
        public function install()
        {                       
            try {
                $sql = "CREATE TABLE IF NOT EXISTS `{$this->tableFullName()}` (
                          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                          `parentId` int(10) unsigned NOT NULL,
                          `childId` int(10) unsigned NOT NULL,
                          PRIMARY KEY (`id`),
                          KEY `{$this->tableFullName()}_parentId` (`parentId`),
                          KEY `{$this->tableFullName()}_childId` (`childId`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";        
                $ret = $this->db->query($sql) !== false;            
            } catch (\Exception $err) {
                throw new \Exception(ERROR_TEXT_DB, 0, $err);              
            }     
            
            return $ret;           
        }
    }
