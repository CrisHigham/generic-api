<?php

namespace GenericApi;

class FilterHelper
{
    /**
     * Function to take query parameters and create a where filter clause.
     * Paremters are in the form field=operator:term
     * @param array $fieldMapping
     * @param array $parameters
     * @return array
     */
    public static function buildFilterClause(object $class, array $parameters): array
    {
        // Build the where clause
        $where["sql"] = "";
        $where["data"] = [];
        foreach ($parameters as $key => $value) {
            if($key == "limit" || $key == "offset"){
                continue;
            }
            if (!property_exists($class, $key)) {
                continue;
            }
            // This is what the line should be, but due to a problem in Tina4 Orm I have rebuilt the function.
            // @todo reinstate this usage once Tina4 is fixed
            //$columnName = $class->getFieldName($key, $class->fieldMapping);
            $columnName = Utilities::getFieldName($key, $class->fieldMapping);
            $valueArray = explode(":", $value);
            $operator = $valueArray[0];
            $term = $valueArray[1];
            switch($operator){
                case "eq":
                    $where["sql"] .= $columnName . " = ? and ";
                    $where["data"][] = $term;
                    break;
                case "ne":
                    $where["sql"] .= $columnName . " != ? and ";
                    $where["data"][] = $term;
                    break;
            }
        }
        $where["sql"] = rtrim($where["sql"], "and ");

        return $where;
    }
}