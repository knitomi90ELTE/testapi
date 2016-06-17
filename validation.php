<?php

function validate($input, $rules, &$data, &$errors) {
    $errors = [];
    $filtered_inputs = filter_var_array($input, $rules);
    foreach ($filtered_inputs as $key => $value) {
        $data[$key] = null;
        if (is_null($value) || empty($input[$key])) {
            if (isset($rules[$key]['default'])) {
                $data[$key] = $rules[$key]['default'];
            } else {
                $errors[] = "{$key} hiányzik";
            }
        } else if ($value === false) {
            $errors[] = $rules[$key]['errormsg'];
        } else {
            $data[$key] = $value;
        }
    }
    return !(bool)$errors;
}
