<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-1-28
 * Time: 下午3:11
 */

namespace AppBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationList;

class ValidationException extends \Exception
{
    /**
     * @var ConstraintViolationList
     */
    protected $_errors = null;

    public function __construct(ConstraintViolationList $errors, $message='', $code = 0, \Exception $previous = null)
    {
        $this->_errors = $errors;
        parent::__construct($message, $code, $previous);
    }

    /**
     * 返回多数据错误对象
     *
     * @return \Symfony\Component\Validator\ConstraintViolationList
     */
    public function getErrors()
    {
        return $this->_errors;
    }
}