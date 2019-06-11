<?php
/**
 * User: babybus zhili
 * Date: 2019-05-08 14:41
 * Email: <zealiemai@gmail.com>
 */

namespace FilterEloquent;


use Exception;

/**
 * 通用查询器
 * http://md.baby-bus.com/web/#/16?page_id=1187
 * Class Filter
 * @package App\HttpCore
 */
class Filter
{
    public $query;
    public $q;
    public $model;
    public $operator_mapping = [
        '' => '=',      // 等于
        'eq' => '=',    // 等于
        'ne' => '!=',   // 不等于
        'gt' => '>',    // 大于
        'ge' => '>=',   // 大于等于
        'lt' => '<',    // 小于
        'le' => '<=',   // 小于等于
        'like' => 'like',   // 包含
        'in' => 'whereIn',  // IN
        'not_in' => 'whereNotIn' // NOT IN
    ];

    public function __construct($model, $query, $q)
    {
        $this->model = $model;
        $this->query = $query;
        $this->q = $q;
    }

    public function filteredQuery()
    {
        $qs = $this->getFilterParams();
        foreach ($qs as $q) {
            $field = $q[0];
            $operator = $q[1];
            $value = $q[2];
            if (is_array($field)) {
                $this->associatedQuery($field, $operator, $value);

            } else {
                $this->commonQuery($field, $operator, $value);
            }

        }
        return $this->query;
    }

    /**
     * 关联查询处理
     * @param $field
     * @param $operator
     * @param $value
     */
    public function associatedQuery($field, $operator, $value)
    {
        $column = array_last($field);
        array_pop($field);
        $_field = implode("->", $field);


        if (in_array($operator, ['whereIn', 'whereNotIn'])) {
            $this->query = $this->query->whereHas($_field, function ($query) use ($column, $value, $operator) {
                $query->$operator($column, $value);
            });
        } else {
            $this->query = $this->query->whereHas($_field, function ($query) use ($column, $value, $operator) {

                $query->where($column, $operator, $value);

            });
        }

    }

    /**
     * 普通查询处理
     * @param $field
     * @param $operator
     * @param $value
     */
    public function commonQuery($field, $operator, $value)
    {
        if (in_array($operator, ['whereIn', 'whereNotIn'])) {
            $this->query = $this->query->$operator($field, $value);
        } else {
            $this->query = $this->query->where($field, $operator, $value);
        }
    }

    /**
     * 获取查询参数
     * @return array
     */
    public function getFilterParams()
    {
        $res = [];
        if (!is_null($this->q)) {
            $qs = explode(',', $this->q);

        } else {
            return [];
        }

        foreach ($qs as $queries) {
            array_push($res, $this->querystringProcessor(trim($queries)));
        }
        return $res;
    }

    /**
     * 查询q语句解析
     * @param $queries
     * @return array|null
     */
    private function querystringProcessor($queries)
    {
        $queries_explode = explode('=', $queries);
        try {
            $key = $queries_explode[0];
            $value = $queries_explode[1];
            if (strpos($key, '__')) {
                $key_explode = explode('__', $key);
                $field = $key_explode[0];
                $operator = $key_explode[1];

            } else {
                $field = $key;
                $operator = '';
            }

            return [
                $this->fieldProcessor($field, $operator, $value),
                $this->operatorProcessor($field, $operator, $value),
                $this->valueProcessor($field, $operator, $value),
            ];
        } catch (Exception $e) {
            return $e;
        }
    }

    /**
     * 处理查询字段
     * @param $field
     * @param $operator
     * @param $value
     * @return array
     */
    private function fieldProcessor($field, $operator, $value)
    {
        $_field = explode('.', $field);
        if (count($_field) > 1) {
            return $_field;
        } else {
            return $field;

        }
    }

    /**
     * 处理查询操作符
     * @param $field
     * @param $operator
     * @param $value
     * @return mixed
     */
    private function operatorProcessor($field, $operator, $value)
    {
        if (array_key_exists($operator, $this->operator_mapping)) {
            return $this->operator_mapping[$operator];
        }

    }

    /**
     * 处理查询条件值
     * @param $field
     * @param $operator
     * @param $value
     * @return array
     */
    private function valueProcessor($field, $operator, $value)
    {
        if (in_array($operator, ['in', 'not_in'])) {
            if ($value) {
                return explode('|', $value);
            } else {
                return [];
            }
        } else {
            return $value;

        }
    }
}