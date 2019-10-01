<?php

/**
 * A simplistic ActiveRecord implementation for those who want one
 */

abstract class Record
{
    protected $_table = null; // Set in the child class
    private $_columns = array();

    protected function db()
    {
        return LightRailPDOInstance::getInstance();
    }

    public function __construct($id = null)
    {
        if (!empty($id) && is_numeric($id) && !empty($this->_table)) {
            $id = intval($id);
            if ($id > 0) {
                $sql = 'SELECT * FROM ' . $this->_table . ' WHERE id=' . $id;
                $sth = $this->db()->prepare($sql);
                if ($sth->execute()) {
                    $row = $sth->fetch(PDO::FETCH_ASSOC);
                    if ($row) {
                        foreach ($row as $key => $val) {
                            $this->$key = $val;
                            $this->_columns[] = $key;
                        }
                    }
                }
            }
        }
    }

    protected function getColumns()
    {
        if (!count($this->columns) && !empty($this->_table)) {
            $sth = $this->db()->prepare('DESCRIBE ' . $this->_table);
            if ($sth->execute()) {
                while ($row = $sth->fetch(PDO::FETCH_NUM)) {
                    $this->_columns[] = $row[0];
                }
            }
        }

        return $this->_columns;
    }

    protected function validate()
    {
        return true;
    }

    protected function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $values = array();
        foreach ($this->getColumns() as $col) {
            if (property_exists($this, $col)) {
                $values[$col] = $this->db()->quote($this->$col);
            }
        }

        if (!count($values)) {
            return false;
        }

        $save_type = empty($this->id) ? 'insert' : 'update';

        if ($save_type == 'insert') {
            $sql = 'INSERT INTO ' . $this->_table . '(' . implode(',', array_keys($values)) . ') VALUES (' . implode(',', $values) . ')';
        } else {
            $pairs = array();
            foreach ($values as $key => $val) {
                if ($key != 'id') {
                    $pairs[] = "$key=$val";
                }
            }

            $sql = 'UPDATE ' . $this->_table . ' SET ' . implode(', ', $pairs) . ' WHERE id=' . $this->db()->quote($this->id);
        }

        $sth = $this->db()->prepare($sql);
        if (!$sth->execute()) {
            return false;
        }

        if ($save_type == 'insert') {
            $this->id = $this->db()->lastInsertId();
        }
    }
}
