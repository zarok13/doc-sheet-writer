<?php

namespace Zarok13\SSWriter\Creator\XLSX;

class Cell
{

    public $value;

    public $dataType;

    /**
     * init cells
     *
     * @param [type] $value
     * @return void
     */
    public function setCell($value)
    {
        $this->value = $value;
        $this->setDataType();
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * set data type
     *
     * @param mix $value
     * @return void
     */
    public function setDataType(): void
    {
        $value = $this->value;
        switch ($value) {
            case $this->isString($value):
                $this->dataType = Type::TP_STRING;
                break;
            case $this->isNumeric($value):
                $this->dataType = Type::TP_NUMERIC;
                break;
            case $this->isBoolean($value):
                $this->dataType = Type::TP_BOOLEAN;
                break;
            case $this->isDate($value):
                $this->dataType = Type::TP_DATE;
                break;
            case $this->isEmpty($value):
                $this->dataType = Type::TP_EMPTY;
                break;
            default:
                $this->dataType = Type::TP_ERROR;
        }
    }

    /**
     * returns string type
     *
     * @param mix $value
     * @return boolean
     */
    public static function isString($value): bool
    {
        return (gettype($value) === 'string' && $value !== '');
    }

    /**
     * returns numeric type
     *
     * @param mix $value
     * @return boolean
     */
    private static function isNumeric($value): bool
    {
        $valueType = gettype($value);

        return ($valueType === 'integer' || $valueType === 'double');
    }

    /**
     * returns boolean type
     *
     * @param mix $value
     * @return boolean
     */
    private static function isBoolean($value): bool
    {
        return gettype($value) === 'boolean';
    }

    /**
     * returns date type
     *
     * @param mix $value
     * @return boolean
     */
    private static function isDate($value): bool
    {
        return ($value instanceof \DateTime || $value instanceof \DateInterval);
    }

    /**
     * returns empty type if value is empty
     *
     * @param mix $value
     * @return boolean
     */
    private static function isEmpty($value): bool
    {
        return ($value === null || $value === '');
    }

    /**
     * returns string type
     *
     * @param mix $value
     * @return boolean
     */
    public function getStringType(): bool
    {
        return $this->dataType === Type::TP_STRING;
    }

    public function getNumericType()
    {
        return $this->dataType === Type::TP_NUMERIC;
    }

    public function getBooleanType()
    {
        return $this->dataType === Type::TP_BOOLEAN;
    }

    public function getEmptyType()
    {
        return $this->dataType === Type::TP_EMPTY;
    }
}
