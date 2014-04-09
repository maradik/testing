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
         * @var int $joinLevel Уровень вложенности источника данных
         */
        protected $joinLevel = 0;
        
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
                        . "{$joinQuery->getAlias()}.{$joinQuery->repository->getTableField($link->getParentField())} "
                        . "{$link->getRelationType} "
                        . "{$this->getAlias()}.{$this->repository->getTableField($link->getChildField())}";
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
                $tableField = $this->repository->getTableField($filter->getField());
                $paramName = "{$this->getAlias()}_{$tableField}{$key}";
                $sql .= ($sql ? ' AND ' : '') 
                    . "{$tableField} {$filter->getRelationType()} :{$paramName}";
                $params[$paramName] = $filter->getValue();
            }
            
            return array($sql, $params);
        }
        
        /**
         * @return array Строка Sql-запроса с параметрами
         */
        public function buildSql()
        {
            $sql = '';
            $whereSql = '';
            $whereParams = array();
            $selectionList = array();
            
            $query = $this;
            do {
                $selectionList[] = $query->getAlias() . '.*';
                if ($query->getJoinQuery()) {
                    $relation = $query->buildSqlRelation();
                    $sql = $query->getJoinModeStr()
                        . " JOIN {$query->getRepository()->tableFullName()} {$query->getAlias()} "
                        . ($relation ? "ON {$relation}" : "");
                    unset($relation);
                } else {
                    $sql = "SELECT " . implode(', ', array_reverse($selectionList))
                        . " FROM {$query->getRepository()->tableFullName()} {$query->getAlias()} "
                        . $sql;
                }
                list($whereSqlCur, $whereParamsCur) = $query->buildSqlFilter();
                $whereSql = $whereSqlCur + ($whereSql ? ' AND ' : '') + $whereSql;
                $whereParams = array_merge($whereParams, $whereParamsCur);
                unset($whereSqlCur);
                unset($whereParamsCur);
            } while ($query = $query->getJoinQuery());
            
            $sql .= ($whereSql ? " WHERE {$whereSql}" : '');
            
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
         * Получить массив строк сущностей из БД
         * 
         * @param int $row_count Количество строк для выборки                
         * @param int $row_offset Смещение первой выбираемой позиции           
         * @return BaseData[][] Массив строк сущностей BaseData
         */                
        protected function get($row_count = 100, $row_offset = 0) 
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
         * Получить строку сущностей из БД
         * 
         * @return BaseData[] Сущность
         */                
        public function getOne() 
        {                                    
            return current($this->get(1, 0));     
        }                    
    }
