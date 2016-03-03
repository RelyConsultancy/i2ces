<?php

namespace Evaluation\UtilBundle\Exception;

/**
 * Class FormException
 *
 * @package Evaluation\UtilBundle\Exception
 */
class FormException extends \Exception
{
    /**
     * @var array
     */
    protected $errors;

    /**
     * FormException constructor.
     *
     * @param array           $errors
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct($errors, $code = 409, \Exception $previous = null)
    {
        $this->errors = $errors;
        parent::__construct("There were some errors", $code, $previous);
    }

    /**
     * @return array|string
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }
}
