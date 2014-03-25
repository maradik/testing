<?php
    namespace Maradik\Testing;        
   
    class CategoryRepository extends BaseRepository
    {               
        /**
         * @param int $id
         * @return CategoryData
         */
        public function getById($id)
        {
            return parent::getById($id);                        
        }        

        /**
         * @param CategoryData $category
         * @return boolean
         */
        public function insert(CategoryData $category)
        {
            return parent::insert($category);  
        }

        /**
         * @param CategoryData $category
         * @return boolean
         */
        public function update(CategoryData $category)
        {
            return parent::update($category);                       
        }                     
    
        /**
         * @return CategoryData
         */
        protected function rowToObject(array $row)
        {
            $categoryData = new CategoryData(
                $row['id'],                
                $row['title'],
                $row['description'],
                $row['order'],
                $row['parentId']                                
            );
            
            return $categoryData;
        }
        
        /**
         * @param BaseData $category
         * @return array
         */
        protected function objectToRow(BaseData $category)
        {
            if (!($category instanceof CategoryData))
                throw new InvalidArgumentException('Неверный параметр $category');
                        
            $row = array();
            $row['id']          = $category->id;
            $row['title']       = $category->title;
            $row['description'] = $category->description;
            $row['order']       = $category->order;    
            $row['parentId']    = $category->parentId;
            
            return $row;
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
                          `title` varchar(255) NOT NULL,
                          `description` varchar(1000) NOT NULL,
                          `order` int(11) NOT NULL,
                          `parentId` int(10) unsigned NOT NULL, 
                          PRIMARY KEY (`id`),
                          KEY `{$this->tableFullName()}_parentId` (`parentId`) 
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";        
                $ret = $this->db->query($sql) !== false;            
            } catch (\Exception $err) {
                throw new \Exception(ERROR_TEXT_DB, 0, $err);              
            }     
            
            return $ret;           
        }        
    }
