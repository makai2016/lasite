<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-1-26
 * Time: 下午2:39
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractEntity
{
    private static $file = '/../Resources/config/uploadMapping.yml';
    private static $constraint = ['property', 'save_path', 'mimeType'];
    protected $image;
    private $path;
    protected $property;
    protected $planimage;

    /**
     * @param UploadedFile|null $image
     */
    public function setImage($image=null)
    {
        $this->image = $image;
        if($image instanceof UploadedFile){
            self::imageMapping();
        }
    }

    /**
     * @return UploadedFile
     */
    public function getImage()
    {
        return $this->image;
    }

    public function imageMapping()
    {
        $config = self::mapingCheck();
        if(!$config){
            return;
        }
        $fileType = self::getImage()->getMimeType();
        if(is_string($config['mimeType'])){
            if($fileType != $config['mimeType']){
                throw new \Exception('上传的文件不合法，要求 '.$config['mimeType']);
            }
            if(!in_array($fileType, $config['mimeType'])){
                throw new \Exception('上传的文件不合法，要求 '.join(', ', $config['mimeType']));
            }
        }
        $fliename =uniqid().'.'.self::getImage()->guessExtension();

        self::getImage()->move($this->path, $fliename);
        $this->planimage = $this->{'get'.ucfirst($config['property'])}();
        $this->{'set'.ucfirst($config['property'])}($config['save_path'].$fliename);
    }

    /**
     * @return array|void
     */
    private function mapingCheck()
    {
        $key  = get_class($this);

        $config = Yaml::parse(__DIR__.self::$file);
        $mapping = key_exists($key, $config)?$config[$key]:false;
        if($mapping){
            $result = array_intersect_key(array_flip(self::$constraint), $mapping);
            if(count(self::$constraint) != count($result)){
                throw new \Exception('无效的设置: '.join(' ', self::$constraint));
            }
        }
        if(!is_array($mapping['mimeType']) && !is_string($mapping['mimeType'])){

            throw new \Exception('无效的设置: mimeType ');
        }
        $this->path = __DIR__.'/../../../';
        $first_str =substr($mapping['save_path'], 0, 1 );
        $end_str = substr($mapping['save_path'], -1);
        if($first_str != '/'){
            $this->path .= '/';
        }
        if($end_str != '/'){
            $mapping['save_path'].='/';
        }
        $this->path .= $mapping['save_path'];
        $this->property ='set'.ucfirst($mapping['property']);
        return $mapping;
    }


    public function createdAtHandle()
    {
        if(property_exists($this, 'createdAt')){
            $this->setCreatedAt(new \DateTime());
        }
    }

    public function updatedAtHandle()
    {
        if(property_exists($this, 'updatedAt')){
            $this->setUpdatedAt(new \DateTime());
        }
    }

    public function toArray()
    {
        //反射下将当前的entity对象转换成对应的数组格式 属性名对应键名 属性值对应数组值
        $ref = new \ReflectionClass($this);
        $props = $ref->getProperties();
        $tmpArr = [];
        array_walk($props,function($val,$key)use (&$tmpArr){
            $func = 'get'. ucfirst($val->name);
            $tmpArr[$val->name] = call_user_func(array($this,$func));
        });
        return $tmpArr;
    }
}