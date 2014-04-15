<?php
    namespace Maradik\Testing;
    
    /**
     * Запрос к данным репозитория
     */    
    class Query 
    {
        const JOIN_INNER        = 0;
        const JOIN_LEFT_OUTER   = 1;
        const JOIN_RIGHT_OUTER  = 2;
         
        const SORT_ASC          = 0;
        const SORT_DESC         = 1; 
                
        /**
         * @var Query $joinQuery;
         */
        protected $joinQuery;
        
        /**
         * @var BaseRepository $repository
         */
        protected $repository;

        /**
         * @var int $joinMode
         */
        protected $joinMode;
        
        /**
         * @var DataLink[] $links 
         */
        protected $links = array();

        /**
         * @var DataFilter[] $filters 
         */
        protected $filters = array();
        
        /**
         * @var array Сортировка {Поле} => {Тип сортировки}
         */
        protected $sort = array();
        
        /**
         * @var int $joinLevel Уровень вложенности источника данных
         */
        protected $joinLevel = 0;
        
        /**
         * @var boolean $hidden Скрыть сущность в результатах
         */
        protected $hidden = false;
        
        /**
         * @param BaseRepository $repository
         * @param Query $joinQuery
         * @param int $joinMode
         */
        public function __construct(
            BaseRepository  $repository, 
            Query           $joinQuery  = null,
                            $joinMode   = Query::JOIN_INNER           
        ) 
        {
            $this->repository   = $repository;
            $this->joinQuery    = $joinQuery;
            $this->joinMode     = $joinMode;
            $this->joinLevel    = $this->joinQuery ? $this->joinQuery->getJoinLevel() + 1 : 0;
        }
        
        /**
         * @return string Способ присоединения источника данных в виде строки 
         */
        public function getJoinModeStr()
        {
            if ($this->getJoinLevel()) {
                switch ($this->joinMode) {
                    case Query::JOIN_INNER:
                        return 'INNER';
                    case Query::JOIN_LEFT_OUTER:
                        return 'LEFT OUTER';
                    case Query::JOIN_RIGHT_OUTER:
                        return 'RIGHT OUTER';                                     
                }
            }
            
            return '';            
        }
        
        /**
         * Настройка отображения сущности в результатах
         * 
         * @param boolean $hidden Скрыть сущность в результатах
         * @return Query
         */
        public function setHidden($hidden = true)
        {
            $this->hidden = $hidden;
            return $this;
        }
        
        /**
         * @return boolean Скрывать ли сущность в результатах
         */
        public function getHidden()
        {
            return $this->hidden;
        }        
        
        /**
         * @return int Уровень вложенности источника данных
         */
        public function getJoinLevel()
        {
            return $this->joinLevel;
        }
        
        /**
         * @return Query Источник данных, к которому привязан текущий
         */
        public function getJoinQuery()
        {
            return $this->joinQuery;
        }
        
        /**
         * @return BaseRepository Репозиторий
         */
        public function getRepository()
        {
            return $this->repository;
        }        
        
        /**
         * Добавить связь 
         * 
         * @param DataLink $link
         * @return Query
         */
        public function addLink(DataLink $link)
        {
            $this->links[] = $link;
            return $this;
        }
        
        /**
         * Добавить связь по полям
         * 
         * @param string $parentField Поле вышестоящей сущности
         * @param string $childField Поле сущности
         * @param string $relationType Тип связи
         * @return Query
         */
        public function addLinkFields($parentField, $childField, $relationType = '=')
        {
            return $this->addLink(new DataLink($parentField, $childField, $relationType));
        }        
        
        /**
         * @return DataLink
         */
        public function getLinks()
        {
            return $this->links;   
        }
        
        /**
         * @return Query
         */
        public function clearLinks()
        {
            $this->links = array();
            return $this;
        }
        
        /**
         * Добавить фильтр 
         * 
         * @param DataFilter $filter
         * @return Query
         */
        public function addFilter(DataFilter $filter)
        {
            $this->filters[] = $filter;
            return $this;
        }
        
        /**
         * Добавить фильтр по полю
         * 
         * @param string $field Поле
         * @param string $value Значение для фильтр
         * @param string $relationType Тип фильтра
         * @return Query
         */
        public function addFilterField($field, $value, $relationType = '=')
        {
            return $this->addFilter(new DataFilter($field, $value, $relationType));
        }        
        
        /**
         * @return DataFilter
         */
        public function getFilters()
        {
            return $this->filters;   
        }        
        
        /**
         * @return Query
         */
        public function clearFilters()
        {
            $this->filters = array();
            return $this;
        }        
        
        /**
         * Добавить сортировку по полю
         * 
         * @param string $field Поле
         * @param int $sortOrder Тип фильтра
         * @return Query
         */
        public function addSortField($field, $sortOrder = Query::SORT_ASC)
        {
            $sortOrderStr = 'ASC';
            
            if ($sortOrder == Query::SORT_DESC) {
                $sortOrderStr = 'DESC';   
            }

            $this->sort[$field] = $sortOrderStr;
            return $this;
        }        
        
        /**
         * @return array
         */
        public function getSort()
        {
            return $this->sort;   
        }        
        
        /**
         * @return Query
         */
        public function clearSort()
        {
            $this->sort = array();
            return $this;
        }             
        
        public function getAlias()
        {
            return $this->repository->tableFullName() . ($this->getJoinLevel() + 1);
        }
        
        /**
         * @return string Строка Sql-условия присоединения таблицы
         */
        public function buildSqlRelation()
        {
            $ret = '';
                
            if ($joinQuery = $this->getJoinQuery()) {
                foreach ($this->getLinks() as $link) {
                    $ret .= ($ret ? ' AND ' : '')
                        . "{$joinQuery->getAlias()}."
                        . "{$joinQuery->getRepository()->getTableField($link->getParentField())} "
                        . "{$link->getRelationType()} "
                        . "{$this->getAlias()}."
                        . "{$this->getRepository()->getTableField($link->getChildField())}";
                }
            }   
            
            return $ret;                         
        }        
        
        /**
         * @return array Строка Sql-условия WHERE с параметрами.
         */
        public function buildSqlFilter()
        {
            $sql = '';
            $params = array();
            
            foreach ($this->getFilters() as $key => $filter) {
                $tableField = $this->getRepository()->getTableField($filter->getField());
                $paramName = "{$this->getAlias()}_{$tableField}{$key}";
                $sql .= ($sql ? ' AND ' : '') 
                    . "{$this->getAlias()}.{$tableField} {$filter->getRelationType()} :{$paramName}";
                $params[$paramName] = $filter->getValue();
            }
            
            return array($sql, $params);
        }
        
        /**
         * @return string Часть Sql-выражения, отвечающая за сортировку
         */
        public function buildSqlSort()
        {
            $sql = '';

            foreach ($this->getSort() as $field => $sortOrder) {
                $sql .= ($sql ? ', ' : '')
                    . "{$this->getAlias()}.{$this->getRepository()->getTableField($field)} {$sortOrder}";
            }
            
            return $sql;
        }        
        
        /**
         * Формирует строку Sql-запроса (с учетом присоединенных источников данных) и массив параметров. 
         *
         * @return array Строка Sql-запроса с параметрами
         */
        public function buildSql()
        {
            $sql = '';
            $whereSql = '';
            $whereParams = array();
            $selectionList = array();             
            $sortSql = '';
            
            $query = $this;
            do {
                //ВНИМАНИЕ! Если будет проблема гибкости со списком полей, используй дополнительный запрос
                //SHOW FULL FIELDS FROM table_name --для получения списка полей таблицы
                if (!$query->getHidden()) {
                    $tableFields = array_values($query->getRepository()->getTableFields());    
                    array_walk($tableFields, function(&$field) use ($query) {
                        $field = "{$query->getAlias()}.{$field}";
                        $field .= " AS '{$field}'";
                    });
                    $selectionList = array_merge($tableFields, $selectionList);
                    unset($tableFields);
                }
                
                if ($query->getJoinQuery()) {
                    $relation = $query->buildSqlRelation();
                    $sql = $query->getJoinModeStr()
                        . " JOIN {$query->getRepository()->tableFullName()} {$query->getAlias()} "
                        . ($relation ? "ON {$relation}" : "")
                        . ($sql ? " {$sql}" : "");                        
                    unset($relation);
                } else {
                    $sql = "SELECT " . (!empty($selectionList) ? implode(', ', $selectionList) : "'x'")
                        . " FROM {$query->getRepository()->tableFullName()} {$query->getAlias()}"
                        . ($sql ? " {$sql}" : "");
                }
                list($whereSqlCur, $whereParamsCur) = $query->buildSqlFilter();
                $whereSql = $whereSqlCur . ($whereSqlCur && $whereSql ? ' AND ' : '') . $whereSql;
                $whereParams = array_merge($whereParamsCur, $whereParams);
                unset($whereSqlCur);
                unset($whereParamsCur);
                $sortSqlCur = $query->buildSqlSort();
                $sortSql = $sortSqlCur . ($sortSqlCur && $sortSql ? ', ' : '') . $sortSql;
                unset($sortSqlCur);
            } while ($query = $query->getJoinQuery());
            
            $sql .= ($whereSql ? " WHERE {$whereSql}" : '');
            $sql .= ($sortSql ? " ORDER BY {$sortSql}" : '');
            
            return array($sql, $whereParams);            
        }        
        
        /**
         * Присоединить репозиторий
         * 
         * @return Query
         */
        public function join(
            BaseRepository $repository,
            $joinMode = Query::JOIN_INNER
        )
        {
            $ret = new Query(
                $repository,
                $this, 
                $joinMode
            );
            
            return $ret;        
        }      
        
        /**
         * Преобразует ассоциативный массив в массив объектов BaseData
         * 
         * @param array Ассоциативный массив
         * @return BaseData[]
         */
        public function rowToObjects($row)
        {
            //TODO Попытаться упростить процедуру выделения интересующих полей 
            $fieldPrefix = "{$this->getAlias()}.";
            $fieldPrefixLen = strlen($fieldPrefix);
            $ret = array();
            
            if ($this->getJoinQuery()) {
                $ret = $this->getJoinQuery()->rowToObjects($row);
            }
            
            if ($this->getHidden()) {
                return $ret;
            }
            
            // определяем интересующие поля из всех кючей $row
            $fields = array_filter(array_keys($row), function($field) use ($fieldPrefix) {
                return strpos($field, $fieldPrefix) === 0; 
            });
            // оставляем для рассмотрения только элементы $row с интересующими ключами
            $subrow = array_intersect_key($row, array_flip($fields));
            
            // если все поля содержат NULL - добавляем к результату объект NULL
            if (count(array_diff($subrow, array(0 => null))) == 0) {
                $ret[] = null;
            } else {
                // удаляем из полей префикс из названия текущего источника данных (оставляем голые названия полей)
                array_walk($fields, function(&$field)  use ($fieldPrefixLen) {
                    $field = substr($field, $fieldPrefixLen); 
                }); 
                
                $ret[] = $this->getRepository()->rowToObject(array_combine($fields, $subrow));                
            }
                
            return $ret;
        }
        
        /**
         * Получить массив строк сущностей из БД
         * 
         * @param int $row_count Количество строк для выборки                
         * @param int $row_offset Смещение первой выбираемой позиции           
         * @return BaseData[][] Массив строк сущностей BaseData (первый индекс - номер строки, второй - номер сущности по порядку)
         */                
        public function get($row_count = 100, $row_offset = 0) 
        {            
            $ret = array();
            list($sql, $params) = $this->buildSql();            
            
            try {
                $q = $this->getRepository()->getDb()->prepare($sql . " LIMIT {$row_offset}, {$row_count}");
                $res = $q->execute($params);
                if ($res) {                 
                    $rows = $q->fetchAll(\PDO::FETCH_ASSOC);
                    foreach ($rows as $row) {
                        $ret[] = $this->rowToObjects($row);
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
         * Получить строку сущностей из БД
         * 
         * @return BaseData[] Набор сущностей (столбцы одной строки БД)
         */                
        public function getOne() 
        {                                    
            return current($this->get(1, 0));     
        }   
        
        /**
         * Получить из БД набор сущностей только одного типа
         * 
         * @param int $row_count Количество строк для выборки                
         * @param int $row_offset Смещение первой выбираемой позиции  
         * @return BaseData[] Набор сущностей (строки БД)
         */        
        public function getEntity($row_count = 100, $row_offset = 0)
        {
            return array_map('array_pop', $this->get($row_count, $row_offset));
        }                 
    }
