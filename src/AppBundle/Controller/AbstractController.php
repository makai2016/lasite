<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-1-26
 * Time: 下午2:58
 */

namespace AppBundle\Controller;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Validator\ConstraintViolationList;
use AppBundle\Exception\ValidationException;


abstract class AbstractController extends FOSRestController
{
    /**
     * 校检 Entity对象
     *
     * @param $entities
     * @throws ValidationException
     */
    protected function validate($entities)
    {
        $errors = new ConstraintViolationList();
        switch (gettype($entities)) {
            case 'array':
                array_walk($entities, function ($entity, $key) use (&$errors) {
                    $errors->addAll(self::get('validator')->validate($entity));
                });
                break;
            default:
                $errors = self::get('validator')->validate($entities);
                break;
        }

        if ($errors->count()) {
            throw new ValidationException($errors);
        }
    }

    /**
     * 存储entity对象到数据库 支持多个entity对象落地
     *
     * @param $entity
     * @return void
     */
    protected function save($entity)
    {
        switch (gettype($entity)) {
            case 'array':
                array_walk($entity, function ($item, $key) {
                    $this->getDoctrine()->getManager()->persist($item);
                });
                break;
            case 'object':
                $this->getDoctrine()->getManager()->persist($entity);
                break;
        }

        $this->getDoctrine()->getManager()->flush();
    }

    /**
     * 删除entity对象
     * @param $entity
     * @return bool
     */
    protected function delete($entity)
    {
        switch (gettype($entity)) {
            case 'array':
                array_walk($entity, function ($item, $key) {
                    $this->getDoctrine()->getManager()->remove($item);
                });
                break;
            case 'object':
                $this->getDoctrine()->getManager()->remove($entity);
                break;
        }
        $this->getDoctrine()->getManager()->flush();
        return true;
    }

    /**
     * @param $entity
     * @param $data
     * @param $options
     * exp:
     * $request->request->all()['user_name'] = 'test';
     * $entity has name => user_name
     * $this->mapping($entity, $postData, ['map' => ['user_name' => ’name‘]])
     */
    protected function mapping(&$entity, array $data, $options = [])
    {
        if (!is_object($entity)) {
            throw new \InvalidArgumentException('mapping的entity必须为entity对象');
        }

        array_walk($data, function ($val, $key) use ($entity, $options) {
            if ((key_exists('only', $options) && !in_array($key, $options['only'])) || (key_exists('filter', $options) && in_array($key, $options['filter']))) {
                return;
            }
            $method = 'set' . self::_underline2camel($key);
            if (method_exists($entity, $method)) {
                $entity->$method($val);
            }
        });

    }

    /**
     * 下划线转驼峰
     * @param $haystack
     * @return string
     */
    public static function _underline2camel($haystack)
    {
        $haystackArray = explode('_', $haystack);
        $dist = '';
        array_walk($haystackArray, function ($val, $key) use (&$dist) {
            $dist .= $key ? ucfirst($val) : $val;
        });
        return $dist;
    }

    /**
     * 抛出验证器异常
     *
     * @param ValidationException $e
     * @param array $map
     *
     * @return \FOS\RestBundle\View\View
     */
    protected function errorValidate($e, array $map = [])
    {
        $errors = $e->getErrors();
        $data = [];
        foreach ($errors as $error) {
            $key = $error->getPropertyPath();
            if (isset($map[$key])) {
                $data[$map[$key]] = $error->getMessage();
            } else {
                $data[$key] = $error->getMessage();
            }

        }
        return $this->view(['message' => $data], 400);
    }

    /**
     * @param null $data
     * @param int $code
     * @return \FOS\RestBundle\View\View
     */
    protected function error($data = null, $code = 400)
    {
        return $this->view($data, $code);
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getManager()
    {
        return $this->get('doctrine.orm.entity_manager');
    }

    /**
     * Get QueryBuilder from doctrine manager
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getQueryBuilder()
    {
        return $this->getManager()->createQueryBuilder();
    }

    /**
     * @param $target
     * @param int $page
     * @param int $limit
     * @param array $options
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    protected function pagination($target, $page = 1, $limit = 10, array $options = array())
    {
        return $this->get('knp_paginator')->paginate($target,$page,$limit,$options);

    }
    
}