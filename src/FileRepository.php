<?php
    namespace Maradik\Testing;        
   
    class FileRepository extends BaseRepository
    {               
        /**
         * @param int $id
         * @return FileData
         */
        public function getById($id)
        {
            return parent::getById($id);                        
        }        

        /**
         * @param FileData $fileData
         * @return boolean
         */
        public function insert(FileData $fileData)
        {
            return parent::insert($fileData);  
        }

        /**
         * @param FileData $fileData
         * @return boolean
         */
        public function update(FileData $fileData)
        {
            return parent::update($fileData);                       
        }                     
    
        /**
         * @return FileData
         */
        public function rowToObject(array $row)
        {
            $fileData = new FileData(
                $row['id'],
                $row['fileName'],
                $row['origFileName'],    
                $row['size'],                            
                $row['title'],
                $row['description'],
                $row['order'],
                $row['parentType'],
                $row['parentId'],   
                $row['createDate'],
                $row['userId'],
                $row['type']                             
            );
            
            return $fileData;
        }
        
        /**
         * Используется для задания правила отображения поля сущности в поле таблицы. 
         *
         * @return array Массив["поле_сущности"] = "поле_таблицы"
         */
        protected function tableFields()
        {
            return array(
                'id'            => 'id',
                'fileName'      => 'fileName',
                'origFileName'  => 'origFileName',
                'size'          => 'size',
                'title'         => 'title',
                'description'   => 'description',
                'order'         => 'order',
                'parentType'    => 'parentType',
                'parentId'      => 'parentId',
                'createDate'    => 'createDate',
                'userId'        => 'userId',
                'type'          => 'type'
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
                          `fileName` varchar(255) NOT NULL,
                          `origFileName` varchar(255) NOT NULL,
                          `size` int(10) unsigned NOT NULL, 
                          `type` int(10) unsigned NOT NULL, 
                          `order` int(11) NOT NULL,
                          `parentType` int(10) unsigned NOT NULL, 
                          `parentId` int(10) unsigned NOT NULL,
                          `title` varchar(255) NOT NULL,
                          `description` varchar(1000) NOT NULL,
                          `createDate` int(10) unsigned NOT NULL, 
                          `userId` int(10) unsigned NOT NULL,                         
                          PRIMARY KEY (`id`),
                          KEY `{$this->tableFullName()}_parent` (`parentType`, `parentId`, `order`),
                          KEY `{$this->tableFullName()}_fileName` (`fileName`), 
                          KEY `{$this->tableFullName()}_userId` (`userId`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";        
                $ret = $this->db->query($sql) !== false;            
            } catch (\Exception $err) {
                throw new \Exception(ERROR_TEXT_DB, 0, $err);              
            }     
            
            return $ret;           
        }        
    }
