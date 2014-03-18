<?php
    namespace Maradik\Testing;

    /**
     * Репозиторий для взаимодействия с БД
     */
    abstract class BaseRepository
    {
        const ERROR_TEXT_DB = "Операция с БД вызвала ошибку!";
        
        /**
         * @var \PDO $db
         */
        protected $db;        
        
        /**
         * @var string $tablePrefix
         */        
        protected $tablePrefix;
        
        /**
         * @var string $tableName
         */         
        protected $tableName;          
                
        /**
         * @param \PDO $pdo Объект для взаимодействия с БД
         * @param string $tableName Наименование таблицы
         * @param string $tablePrefix Префикс таблицы         
         */        
        public function __construct(\PDO $pdo, $tableName, $tablePrefix = "")
        {
            if (empty($pdo) || !($pdo instanceof \PDO)) {
                throw new \InvalidArgumentException('Invalid parameter $pdo');
            }              
            
            $this->db = $pdo;
            $this->tableName = $tableName;
            $this->tablePrefix = $tablePrefix;
        }     
        
        /**
         * return string Полное имя таблицы с префиксом
         */
        protected function tableFullName()
        {
            return empty($this->tablePrefix) ? $this->tableName : "{$this->tablePrefix}_{$this->tableName}";
        }                  
        
        /**
         * Получить набор Сущностей из БД
         * 
         * @param array $filter Ассоциативный массив для наложения фильтра. {$key => $value} как {field => filter}
         * @param int $row_count Количество строк для выборки                
         * @param int $row_offset Смещение первой выбираемой позиции           
         * @return BaseData[] Массив сущностей BaseData
         */                
        protected function get(array $filter, $row_count = 100, $row_offset = 0) 
        {            
            $ret = array();
            
            $sql_where = "";                                                
            foreach ($filter as $key => $val) {
                $sql_where .= (empty($sql_where) ? "" : " AND ") . "`{$key}` = :{$key}";                           
            }        
            $sql_where = (empty($sql_where) ? "" : " where ") . $sql_where;                        
                                                                           
            try {
                $q = $this->db->prepare(
                    "select * from `{$this->tableFullName()}`{$sql_where} limit {$row_offset}, {$row_count}"
                );
                $res = $q->execute($filter);
                if ($res) {                 
                    $rows = $q->fetchAll(\PDO::FETCH_ASSOC);
                    foreach ($rows as $row) {
                        $ret[] = $this->rowToObject($row);
                    }
                    unset($rows);
                } 
                $q->closeCursor();
            } catch (\Exception $err) {
                throw new \Exception(ERROR_TEXT_DB, 0, $err);              
            }                        
            
            return $ret;     
        }        
        
        /**
         * Получить Сущность из БД
         * 
         * @param int $id Идентификатор сущности
         * @return BaseData Сущность, соответствующая идентификатору
         */                
        public function getById($id) 
        {
            try {
                $q = $this->db->prepare(
                    "select * from `{$this->tableFullName()}` where `id` = ? limit 1"
                );
                $res = $q->execute(array($id));
                if ($res) {                 
                    $row = $q->fetch(\PDO::FETCH_ASSOC);
                } 
                $q->closeCursor();
            } catch (\Exception $err) {
                throw new \Exception(ERROR_TEXT_DB, 0, $err);              
            }                        
            
            return $res !== false && $row !== false ? $this->rowToObject($row) : null;     
        }        
        
        /**
         * Получить список сущностей из БД
         * 
         * @param array $filter Ассоциативный массив для наложения фильтра.
         * @param int $row_count Количество строк для выборки                
         * @param int $row_offset Смещение первой выбираемой позиции              
         * @return BaseData[] Массив сущностей
         */                
        public function getCollection(array $filter = null, $row_count = 100, $row_offset = 0) 
        {                                    
            return $this->get(
                array(), 
                $row_offset, 
                $row_count
            );     
        }        
        
        /**
         * Вставить сущность в БД
         * 
         * @param BaseData $object Сущность для вставки
         * @return boolean
         */
        public function insert(BaseData $object) 
        {          
            $sql_fields = $sql_values = "";             
            $row = $this->objectToRow($object);            
            
            foreach ($row as $key => $val) {
                if ($key != "id") {
                    $sql_fields .= (empty($sql_fields) ? "" : ", ") . "`{$key}`";
                    $sql_values .= (empty($sql_values) ? "" : ", ") . ":{$key}";
                } else {
                    unset($row[$key]);
                }                                
            }            
            
            $sql = "insert into `{$this->tableFullName()}` ({$sql_fields}) values ({$sql_values})";                             
            unset($sql_fields);
            unset($sql_values);
            
            try {
                $q = $this->db->prepare($sql);
                $ret = $q->execute($row);
                if ($ret) {                               
                    $object->id = (int)$this->db->lastInsertId(); //TODO возможны коллизии, т.к. не атомарно!
                }              
                $q->closeCursor();   
            } catch (\Exception $err) {
                throw new \Exception(ERROR_TEXT_DB, 0, $err);              
            }
            
            return $ret;
        }
        
        /**
         * Обновить сущность в БД
         * 
         * @param BaseData $object Сущность для обновления
         * @return boolean
         */        
        public function update(BaseData $object) 
        {            
            $sql_fields = "";
            $row = $this->objectToRow($object);            
            
            foreach ($row as $key => $val) {
                if ($key != "id") {
                    $sql_fields .= (empty($sql_fields) ? "" : ", ") . "`{$key}` = :{$key}";
                }
            }            
            
            $sql = "update `{$this->tableFullName()}` set {$sql_fields} where `id` = :id";
            unset($sql_fields);
            
            try {
                $q = $this->db->prepare($sql);
                $ret = $q->execute($row);
                $q->closeCursor();
            } catch (\Exception $err) {
                throw new \Exception(ERROR_TEXT_DB, 0, $err);              
            }
            
            return $ret;            
        }
        
        /**
         * Удалить сущность из БД
         * 
         * @param int $id Идентификатор удаляемой сущности
         * @return boolean
         */                
        public function delete($id) 
        {
            $ret = false;            
            
            try {
                $q = $this->db->prepare(
                    "delete from `{$this->tableFullName()}` where `id` = ?"
                );
                $ret = $q->execute(array($id));
                $q->closeCursor();
            } catch (\Exception $err) {
                throw new \Exception(ERROR_TEXT_DB, 0, $err);              
            }
            
            return $ret;            
        }
        
        /**
         * Преобразование ассоциативного массива (строка таблицы) в объект сущности
         * 
         * @param array $row Ассоциативный массив, где каждая пара (ключ; значение) = (название поля; значение поля)
         * @return BaseData 
         */
        abstract protected function rowToObject(array $row);

        /**
         * Преобразование объекта сущности в ассоциативный массив (строка таблицы)
         * 
         * @param BaseData $object  
         * @return array
         */
        abstract protected function objectToRow(BaseData $object);        

        /**
         * Создание необходимых таблиц в БД и первичная настройка
         *          
         * @return boolean true в случае успеха, иначе false
         */
        abstract public function install();  
    }

