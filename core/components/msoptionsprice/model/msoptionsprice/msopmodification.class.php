<?php

class msopModification extends xPDOSimpleObject
{

    public static function getProductModification(xPDO & $xpdo, $rid = 0, $withOptions = true)
    {
        $modifications = array();

        $classModification = 'msopModification';
        $q = $xpdo->newQuery($classModification);
        $q->select($xpdo->getSelectColumns($classModification, $classModification, '', array(), true));
        $q->sortby("rank", "ASC");
        $q->where(array(
            "{$classModification}.rid" => "{$rid}"
        ));

        if ($q->prepare() AND $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($withOptions) {
                    $row['options'] = $xpdo->call('msopModificationOption', 'getOptions',
                        array(&$xpdo, $row['id'], $rid));
                }
                $modifications[] = $row;
            }
        }

        return $modifications;
    }

    public static function saveProductModification(xPDO & $xpdo, $rid = 0, array $modifications = array())
    {
        $classModification = 'msopModification';

        foreach ($modifications as $row) {
            $options = isset($row['options']) ? $row['options'] : array();
            if (empty($options)) {
                continue;
            }

            $row['rid'] = $rid;
            unset($row['id'], $row['rank'], $row['options']);

            if ($xpdo->getCount($classModification, $row)) {
                continue;
            }

            /** @var msopModification $modification */
            $modification = $xpdo->newObject($classModification);
            $modification->fromArray($row, '', true, true);
            if ($modification->save()) {
                $xpdo->call('msopModificationOption', 'saveOptions',
                    array(&$xpdo, $modification->get('id'), $rid, $options));
            }
        }

        return self::getProductModification($xpdo, $rid);
    }

    /**
     * @param null $cacheFlag
     *
     * @return bool
     */
    public function save($cacheFlag = null)
    {
        if ($this->isNew()) {
            $q = $this->xpdo->newQuery('msopModification');
            $this->set('rank', $this->xpdo->getCount('msopModification', $q));
        }

        $saved = parent:: save($cacheFlag);

        return $saved;
    }

    public function remove(array $ancestors = array())
    {
        $remove = parent::remove($ancestors);

        if ($remove) {
            $this->xpdo->call('msopModificationOption', 'removeOptions',
                array(&$this->xpdo, $this->get('id'), $this->get('rid')));
        }

        return $remove;
    }

}