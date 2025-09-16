<?php

namespace GenericApi;

class Utilities
{
    /**
     * Gets the field mapping in the database eg -> lastName maps to last_name
     * @param string $name Name of field required
     * @param array $fieldMapping Array of field mapping
     * @param bool $ignoreMapping Ignore the field mapping
     * @return string|null Required field name from database
     */
    public static function getFieldName(string $name, array $fieldMapping = [], bool $ignoreMapping = false): ?string
    {
        if (strpos($name, "_")) {
            return $name;
        }

        // Check if a field map was parsed and that it mustn't be ignored
        if (!empty($fieldMapping) && !$ignoreMapping) {
            $prefix = '';

            // Check if the parsed name has a prefix (e.g t.column_name)
            if (strpos($name, ".")) {
                $parts = explode('.', $name, 2);
                $prefix = "{$parts[0]}.";
                $name = $parts[1];
            }

            // Check if the column needs to be mapped from the ORM value
            if (isset($fieldMapping[$name])) {
                return ($prefix . $fieldMapping[$name]);
            }

            // Check if the column has already been mapped.
            // explode is used for ordering (e.g. column_name desc)
            if (array_key_exists(explode(" ", trim($name))[0], array_flip($fieldMapping))) {
                return $prefix . $name;
            }
        }

        $fieldName = "";

        for ($i = 0, $iMax = strlen($name); $i < $iMax; $i++) {
            if (\ctype_upper($name[$i]) && $i != 0 && $i < strlen($name) - 1 && (!\ctype_upper($name[$i - 1]) || !\ctype_upper($name[$i + 1]))) {
                $fieldName .= "_" . strtolower($name[$i]);
            } else {
                $fieldName .= $name[$i];
            }
        }

        return ($fieldName);
    }
}