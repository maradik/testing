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
         * @var array $tableFields Соответствие полей сущности (ключ) - полям таблицы (значение)
         */
         
        private $tableFields;
        
        /**
         * @var array $entityFields Соответствие полей таблицы (ключ) - полям сущности (значение)
         */
         
        private $entityFields;     
        
        /**
         * @var callable $onDelete
         */
        protected $onDelete;   
                
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
            
            $this->tableFields = $this->tableFields();
            $this->entityFields = array_flip($this->tableFields);
            
            $this->onDelete = function($id) { return true; };
        }     
        
        /**
         * Принимает функцию вида 
         * 
         * function($id) {return true;}
         * 
         * Эта функция будет вызвана в момент вызова метода delete 
         * и ей будет передан параметром идентификатор удаляемой сущности $id
         * 
         * @param callable $callback function($id) {return true;}
         */
        public function setOnDelete($callback)
        {
            $this->onDelete = $callback;
        }
        
        /**
         * @return \PDO
         */                
        public function getDb()
        {
            return $this->db;
        }           
        
        /**
         * @return string Полное имя таблицы с префиксом
         */
        public function tableFullName()
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
        public function getCollection(array $filter = array(), $row_count = 100, $row_offset = 0) 
        {                                    
            return $this->get(
                $filter, //TODO преобразование фильтра из полей сущностей в поля таблицы
                $row_count,
                $row_offset                 
            );     
        }      
        
        /**
         * Получить сущность из БД
         * 
         * @param array $filter Ассоциативный массив для наложения фильтра.        
         * @return BaseData Сущность
         */                
        public function getOne(array $filter = array()) 
        {                                    
            return current($this->get(
                $filter, //TODO преобразование фильтра из полей сущностей в поля таблицы
                1,
                0                 
            ));     
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

            //TODO разобраться с транзакциями. Если delete() вызывает delete() у другого репозитори (но с этим же PDO) - ошибка повторного открытия транзакции
            //$this->transactionBegin();
            
            try {
                $q = $this->db->prepare(
                    "delete from `{$this->tableFullName()}` where `id` = ?"
                );
                $ret = $q->execute(array($id));
                $q->closeCursor();
            } catch (\Exception $err) {
                //$this->transactionRollBack();                
                throw new \Exception(ERROR_TEXT_DB, 0, $err);              
            }
            $ret = $ret && (!isset($this->onDelete) ? true : call_user_func($this->onDelete, $id));
            /*
            if ($ret) {
                $this->transactionCommit();
            } else {
                $this->transactionRollBack();
            }            
            */
            return $ret;            
        }
        
        /**
         * Получить название поля сущности по названию поля таблицы 
         * 
         * @return string Название поля сущности
         */
        public function getEntityField($tableField)
        {
            if (!empty($this->entityFields[$tableField])) {
                return $this->entityFields[$tableField];
            }
                        
            return "";              
        }
        
        /**
         * Получить название поля таблицы по названию поля сущности 
         * 
         * @return string Название поля таблицы
         */        
        public function getTableField($entityField)
        {
            if (!empty($this->tableFields[$entityField])) {
                return $this->tableFields[$entityField];
            }
                        
            return "";                        
        }
                
        
        public function transactionBegin()
        {
            return $this->db->beginTransaction();
        }
        
        public function transactionCommit()
        {
            return $this->db->commit();
        }
        
        public function transactionRollBack()
        {
            return $this->db->rollBack();
        }
        
        public function getTableFields()
        {
            return $this->tableFields;
        }
        
        public function query()
        {
            return new Query($this);
        }
        
        /**
         * Преобразование объекта сущности в ассоциативный массив (строка таблицы)
         * 
         * @param BaseData $object  
         * @return array
         */
        protected function objectToRow(BaseData $entity)
        {            
            $row = array();
            
            foreach ($this->tableFields as $entityField => $tableField) {
                $row[$tableField] = $entity->$entityField;
            }
            
            return $row;
        }         
        
        /**
         * Преобразование ассоциативного массива (строка таблицы) в объект сущности
         * 
         * @param array $row Ассоциативный массив, где каждая пара (ключ; значение) = (название поля; значение поля)
         * @return BaseData 
         */
        abstract public function rowToObject(array $row);

        /**
         * Создание необходимых таблиц в БД и первичная настройка
         *          
         * @return boolean true в случае успеха, иначе false
         */
        abstract public function install();  
        
        /**
         * Используется для задания правила отображения поля сущности в поле таблицы. 
         *
         * @return array Массив["поле_сущности"] = "поле_таблицы"
         */
        abstract protected function tableFields();
    }

