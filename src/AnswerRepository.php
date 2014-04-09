<?php
    namespace Maradik\Testing;        
    
    class AnswerRepository extends BaseRepository
    {               
        /**
         * @param int $id
         * @return AnswerData
         */
        public function getById($id)
        {
            return parent::getById($id);                        
        }        

        /**
         * @param AnswerData $answer
         * @return boolean
         */
        public function insert(AnswerData $answer)
        {
            return parent::insert($answer);  
        }

        /**
         * @param AnswerData $answer
         * @return boolean
         */
        public function update(AnswerData $answer)
        {
            return parent::update($answer);                       
        }                     
    
        /**
         * @return AnswerData
         */
        protected function rowToObject(array $row)
        {
            $answerData = new AnswerData(
                $row['id'],                
                $row['title'],
                $row['description'],
                $row['questionId'],
                $row['order'],
                $row['createDate'],
                $row['userId']                                                
            );
            
            return $answerData;
        }

        //TODO Удалить метод, определен в родителе        
        /**
         * @param BaseData $answer
         * @return array
         */
        /*
        protected function objectToRow(BaseData $answer)
        {
            if (!($answer instanceof AnswerData))
                throw new InvalidArgumentException('Неверный параметр $answer');            
                        
            $row = array();
            $row['id']          = $answer->id;
            $row['title']       = $answer->title;
            $row['description'] = $answer->description;
            $row['questionId']  = $answer->questionId;
            $row['order']       = $answer->order;
            $row['createDate']  = $answer->createDate;
            $row['userId']      = $answer->userId;
            
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
                'title'       => 'title',
                'description' => 'description',
                'questionId'  => 'questionId',
                'order'       => 'order',
                'createDate'  => 'createDate',
                'userId'      => 'userId'
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
                          `title` varchar(255) NOT NULL,
                          `description` varchar(1000) NOT NULL,
                          `questionId` int(10) unsigned NOT NULL,
                          `createDate` int(10) unsigned NOT NULL,
                          `order` int(11) NOT NULL,
                          `userId` int(10) unsigned NOT NULL,
                          PRIMARY KEY (`id`),
                          KEY `{$this->tableFullName()}_questionId` (`questionId`) 
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";        
                $ret = $this->db->query($sql) !== false;            
            } catch (\Exception $err) {
                throw new \Exception(ERROR_TEXT_DB, 0, $err);              
            }     
            
            return $ret;           
        }        
    }
