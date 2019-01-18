<?php

namespace Rq\Driver\Traits;

/**
 * 搓比版 后续完善
 * Trait MysqlBuilder
 * @package src\Driver\Traits
 */
trait MysqlBuilder
{
    private $preSql = '';
    private $whereSql = '';
    private $whereParams = [];
    private $data = [];
    private $sql = '';
    private $op = '';
    private $limit = '';
    private $orderby = '';
    private $groupby = '';
    private $tableName = '';
    private $field = '*';
    private $select = 'SELECT';
    private $insert = 'INSERT';
    private $update = 'UPDATE';
    private $delete = 'DELETE';

    public function select($table, array $fields = [])
    {
        $this->tableName = $table;
        $this->op = $this->select;
        $this->setFields($fields);
        return $this;
    }

    public function update($table, array $data = [])
    {
        $this->tableName = $table;
        $this->op = $this->update;
        $this->data = $data;
        $this->setUpdatePreSql();
        return $this;
    }

    public function delete($table)
    {
        $this->tableName = $table;
        $this->op = $this->delete;
        return $this;
    }

    public function insert($table, array $data = [])
    {
        $this->tableName = $table;
        $this->op = $this->insert;
        $this->setInsertPreSql($data);
        return $this;
    }

    public function insertBatch($table, array $data = [])
    {
        $this->tableName = $table;
        $this->op = $this->insert;
        $this->setInsertPreSql($data, true);
        return $this;
    }

    public function setWhere($whereSql = '')
    {
        $this->whereSql = ' WHERE ' . $whereSql;
        return $this;
    }

    public function setParams(array $params)
    {
        $this->whereParams = $params;
        return $this;
    }

    public function limit()
    {

        return $this;
    }

    /**
     * @param array $orderby
     * @return $this
     */
    public function orderby(array $orderby = [])
    {
        if (!empty($orderby)) {
            $this->orderby = ' ORDER BY ';
            $orderbySqlList = [];
            foreach ($orderby as $k => $v) {
                $orderbySqlList[$k] = $k . ' ' . $v;
            }
            $this->orderby .= implode(',', $orderbySqlList);
        }

        return $this;
    }

    /**
     * @param array $groupby
     * @return $this
     */
    public function groupby(array $groupby = [])
    {
        if (!empty($groupby)) {
            $groupbySql = implode(',', $groupby);
            $this->groupby = ' GROUP BY ' . $groupbySql;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getSql()
    {
        $this->constructSql();
        return $this->sql;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->whereParams;
    }

    /**
     *
     */
    private function constructSql()
    {
        switch ($this->op) {
            case $this->select:
                $sqlList = [
                    $this->select,
                    $this->field,
                    ' FROM ',
                    $this->tableName,
                    $this->whereSql,
                    $this->orderby,
                    $this->groupby,
                    $this->limit,
                ];
                break;
            case $this->delete:
                $sqlList = [
                    $this->delete,
                    ' FROM ',
                    $this->tableName,
                    $this->whereSql
                ];
                break;
            case $this->update:
                $sqlList = [
                    $this->update,
                    $this->tableName,
                    ' SET ',
                    $this->preSql,
                    $this->whereSql
                ];

                break;
            case $this->insert:
                $sqlList = [
                    $this->insert,
                    ' INTO ',
                    $this->tableName,
                    $this->preSql
                ];
                break;
            default:
                $sqlList = [];
                break;
        }
        $this->sql = implode(' ', $sqlList);
    }

    /**
     *构造更新语句
     */
    private function setUpdatePreSql()
    {
        $data = [];
        foreach ($this->data as $k => $v) {
            $k = (string)$k;
            if (is_null($v)) {
                continue;
            }
            $data[$k] = $data[$k] . ' = ' . $v;
        }
        $this->preSql = implode(',', $data);
    }

    /**
     * 构造插入语句
     * @param array $data
     * @param boolean $isBatch 是否是批量
     */
    private function setInsertPreSql(array $data, $isBatch = false)
    {
        if ($isBatch) {
            $field = array_keys($data[0]);
            foreach ($field as $k => $v) {
                $field[$k] = '`' . $v . '`';
            }
            $preSql = "(%s) values";
            $this->preSql = sprintf($preSql, implode(',', $field));
            $preSql = "(%s),";
            foreach ($data as $k => $v) {
                $this->preSql .= sprintf($preSql, implode(',', $v));
            }
            $this->preSql = trim($this->preSql, ',');
        } else {
            $field = array_keys($data);
            foreach ($field as $k => $v) {
                $field[$k] = '`' . $v . '`';
            }
            $preSql = "(%s) value(%s)";
            $this->preSql = sprintf($preSql, implode(',', $field), implode(',', $data));
        }
    }

    /**
     * 执行完后，重置变量
     */
    public function resetBuilder()
    {
        $this->whereSql = '';
        $this->whereParams = [];
        $this->data = [];
        $this->sql = '';
        $this->op = '';
        $this->limit = '';
        $this->orderby = '';
        $this->groupby = '';
        $this->tableName = '';
        $this->field = '*';
    }

    private function setFields(array $fields = [])
    {
        if (!empty($fields)) {
            $field = implode(',', $fields);
            $this->field = $field;
        }
        return $this;
    }

}