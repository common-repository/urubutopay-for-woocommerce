<?php
if (!defined('ABSPATH')) {
    exit;
}

class UPGFC_Validation
{

    public static function upgfc_test_input($value)
    {
        $value = trim($value);
        $value = stripslashes($value);
        $value = htmlspecialchars($value);
        return $value;
    }

    public function is_required($field)
    {
        return $field . ' is required';
    }

    public function validate_auth($arg)
    {
        $errors = array();

        $username = UPGFC_Validation::upgfc_test_input($arg->user_name);
        $password = UPGFC_Validation::upgfc_test_input($arg->password);

        if (empty($username)) {
            array_push($errors, array('user_name' => UPGFC_Validation::is_required('username')));
        }

        if (empty($password)) {
            array_push($errors, array('password' => UPGFC_Validation::is_required('password')));
        }

        return $errors;
    }

    public function validate_payment_callback($arg)
    {

        $errors = array();
        $transaction_id = $this->upgfc_test_input($arg->transaction_id);

        $internal_transaction_id = $this->upgfc_test_input($arg->internal_transaction_id);

        $transaction_status = $this->upgfc_test_input($arg->transaction_status);

        $payer_code = $this->upgfc_test_input($arg->payer_code);

        if (empty($transaction_id)) {
            array_push($errors, array('transaction_id' => $this->is_required('Transaction ID')));
        }

        if (empty($internal_transaction_id)) {
            array_push($errors, array('internal_transaction_id' => $this->is_required('Internal transaction ID')));
        }

        if (empty($transaction_status)) {
            array_push($errors, array('transaction_status' => $this->is_required('Transaction Status')));
        }

        if (empty($payer_code)) {
            array_push($errors, array('payer_code' => $this->is_required('Payer Code')));
        }

        return $errors;
    }

    public function validate_check_transaction($args)
    {
        $errors = array();
        $transaction_id = $this->upgfc_test_input($args->transaction_id);

        if (empty($transaction_id)) {
            array_push($errors, array('transaction_id' => $this->is_required('Transaction ID')));
        }

        return $errors;
    }
}
