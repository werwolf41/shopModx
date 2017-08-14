<?php

class msopModificationOption extends xPDOObject
{

    public static function getOptions(
        xPDO & $xpdo,
        $mid = null,
        $rid = null,
        $key = null,
        $process = false,
        $prefix = ''
    ) {
        $options = array();

        $classValue = 'msopModificationOption';
        $classMsOption = 'msOption';

        $q = $xpdo->newQuery($classValue);
        $q->leftJoin($classMsOption, $classMsOption, "{$classValue}.key = {$classMsOption}.key");

        if ($process) {
            $q->select($xpdo->getSelectColumns($classValue, $classValue));
            $q->select($xpdo->getSelectColumns($classMsOption, $classMsOption, '', array('caption'), false));
        } else {
            $q->select($xpdo->getSelectColumns($classValue, $classValue, '', array('key', 'value'), false));
            $q->select($xpdo->getSelectColumns($classMsOption, $classMsOption, '', array('caption'), false));
        }

        if (!is_null($mid)) {
            $q->where(array(
                "{$classValue}.mid" => "{$mid}",
            ));
        }
        if (!is_null($rid)) {
            $q->where(array(
                "{$classValue}.rid" => "{$rid}"
            ));
        }

        if ($q->prepare() AND $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $k = $prefix . $row['key'];
                if (isset($options[$k])) {
                    if (!is_array($options[$k])) {
                        $options[$k] = array($options[$k]);
                    }
                    $options[$k][] = $row['value'];
                } else {
                    $options[$k] = $row['value'];
                }

                if ($process) {
                    foreach ($row as $x => $value) {
                        $options[$k . '.' . $x] = $value;
                    }
                }
            }
        }

        if ($key AND !$process) {
            $options = $xpdo->getOption($key, $options, '', true);
        }

        return $options;
    }


    public static function removeOptions(xPDO & $xpdo, $mid = 0, $rid = 0, $key = '')
    {
        $table = $xpdo->getTableName('msopModificationOption');

        if (empty($key)) {
            $sql = "DELETE FROM {$table} WHERE `mid` = '{$mid}' AND `rid` = '{$rid}';";
        } else {
            $sql = "DELETE FROM {$table} WHERE `mid` = '{$mid}' AND `rid` = '{$rid}' AND `key` = '{$key}';";
        }

        $stmt = $xpdo->prepare($sql);
        $stmt->execute();
        $stmt->closeCursor();
    }

    public static function saveOptions(xPDO & $xpdo, $mid = 0, $rid = 0, array $options = array())
    {
        $table = $xpdo->getTableName('msopModificationOption');

        $sql = "INSERT INTO {$table} (`mid`, `rid`, `key`, `value`) VALUES (:mid, :rid, :key, :value);";
        $stmt = $xpdo->prepare($sql);
        foreach ($options as $key => $field) {
            if (!is_array($field)) {
                $field = array($field);
            }
            foreach ($field as $value) {
                $stmt->bindValue(':mid', $mid);
                $stmt->bindValue(':rid', $rid);
                $stmt->bindValue(':key', $key);
                $stmt->bindValue(':value', $value);
                $stmt->execute();
            }
        }
        $stmt->closeCursor();
    }


    public static function getProductOptions(
        xPDO & $xpdo,
        $rid = null,
        $key = null,
        $process = false,
        $prefix = ''
    ) {
        $options = array();

        $classValue = 'msProductOption';

        $q = $xpdo->newQuery($classValue);
        if ($process) {
            $q->select($xpdo->getSelectColumns($classValue, $classValue));
        } else {
            $q->select($xpdo->getSelectColumns($classValue, $classValue, '', array('key', 'value'), false));
        }

        $q->sortby('value', 'ASC');

        if (!is_null($rid)) {
            $q->where(array(
                "{$classValue}.product_id" => "{$rid}"
            ));
        }

        if ($q->prepare() AND $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $k = $prefix . $row['key'];
                if (isset($options[$k])) {
                    if (!is_array($options[$k])) {
                        $options[$k] = array($options[$k]);
                    }
                    $options[$k][] = $row['value'];
                } else {
                    $options[$k][] = $row['value'];
                }

                if ($process) {
                    foreach ($row as $x => $value) {
                        $options[$k . '.' . $x] = $value;
                    }
                }
            }
        }

        if ($key AND !$process) {
            $options = $xpdo->getOption($key, $options, '', true);
        }

        return $options;
    }


    public static function removeProductOptions(xPDO & $xpdo, $rid = 0, $key = '', $value = null)
    {
        $table = $xpdo->getTableName('msProductOption');

        switch (true) {
            case empty($key):
                $sql = "DELETE FROM {$table} WHERE `product_id` = '{$rid}';";
                break;
            case !empty($key) AND is_null($value):
                $sql = "DELETE FROM {$table} WHERE `product_id` = '{$rid}' AND `key` = '{$key}';";
                break;
            case !empty($key) AND !is_null($value):
                $sql = "DELETE FROM {$table} WHERE `product_id` = '{$rid}' AND `key` = '{$key}' AND `value` = '{$value}';";
                break;
        }

        $stmt = $xpdo->prepare($sql);
        $stmt->execute();
        $stmt->closeCursor();
    }

    public static function saveProductOptions(xPDO & $xpdo, $rid = 0, array $options = array())
    {
        $table = $xpdo->getTableName('msProductOption');

        $sql = "INSERT INTO {$table} (`product_id`, `key`, `value`) VALUES (:product_id, :key, :value);";
        $stmt = $xpdo->prepare($sql);
        foreach ($options as $key => $field) {
            if (!is_array($field)) {
                $field = array($field);
            }
            foreach ($field as $value) {
                $stmt->bindValue(':product_id', $rid);
                $stmt->bindValue(':key', $key);
                $stmt->bindValue(':value', $value);
                $stmt->execute();
            }
        }
        $stmt->closeCursor();
    }
    
}