<?php
    namespace Maradik\Testing;        
    
    class QuestionRepository extends BaseRepository
    {               
        /**
         * @param int $id
         * @return QuestionData
         */
        public function getById($id)
        {
            return parent::getById($id);                        
        }        

        /**
         * @param QuestionData $question
         * @return boolean
         */
        public function insert(QuestionData $question)
        {
            return parent::insert($question);  
        }

        /**
         * @param QuestionData $question
         * @return boolean
         */
        public function update(QuestionData $question)
        {
            return parent::update($question);                       
        }                     
    
        /**
         * @return QuestionData
         */
        protected function rowToObject(array $row)
        {
            $questionData = new QuestionData(
                $row['id'],
                $row['title'],
                $row['description'],
                $row['categoryId'],
                $row['parentId'],
                $row['order'],
                $row['createDate'],
                $row['userId']
            );
            
            return $questionData;
        }
        
        /**
         * @param BaseData $question
         * @return array
         */
        protected function objectToRow(BaseData $question)
        {
            if (!($question instanceof QuestionData))
                throw new InvalidArgumentException('Неверный параметр $question');
                        
            $row = array();
            $row['id']          = $question->id;
            $row['title']       = $question->title;
            $row['description'] = $question->description;
            $row['categoryId']  = $question->categoryId;
            $row['parentId']    = $question->parentId;
            $row['order']       = $question->order;
            $row['createDate']  = $question->createDate;
            $row['userId']      = $question->userId;            
            
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
                          `parentId` int(10) unsigned NOT NULL,
                          `createDate` int(10) unsigned NOT NULL,
                          `userId` int(10) unsigned NOT NULL,
                          `categoryId` int(10) unsigned NOT NULL,
                          `order` int(11) NOT NULL,
                          PRIMARY KEY (`id`),
                          KEY `{$this->tableFullName()}_parentId` (`parentId`),
                          KEY `{$this->tableFullName()}_userId` (`userId`),
                          KEY `{$this->tableFullName()}_categoryId` (`categoryId`)  
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";        
                $ret = $this->db->query($sql) !== false;            
            } catch (\Exception $err) {
                throw new \Exception(ERROR_TEXT_DB, 0, $err);              
            }     
            
            return $ret;           
        }          
    }
