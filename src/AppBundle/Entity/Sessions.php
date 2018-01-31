<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-1-31
 * Time: 下午3:57
 */

namespace AppBundle\Entity;


class Sessions
{

    /**
     * @var binary
     */
    private $sessId;

    /**
     * @var string
     */
    private $sessData;

    /**
     * @var integer
     */
    private $sessLifetime;

    /**
     * @var integer
     */
    private $sessTime;


    /**
     * Get sessId
     *
     * @return binary 
     */
    public function getSessId()
    {
        return $this->sessId;
    }

    /**
     * Set sessData
     *
     * @param string $sessData
     * @return Sessions
     */
    public function setSessData($sessData)
    {
        $this->sessData = $sessData;

        return $this;
    }

    /**
     * Get sessData
     *
     * @return string 
     */
    public function getSessData()
    {
        return $this->sessData;
    }

    /**
     * Set sessLifetime
     *
     * @param integer $sessLifetime
     * @return Sessions
     */
    public function setSessLifetime($sessLifetime)
    {
        $this->sessLifetime = $sessLifetime;

        return $this;
    }

    /**
     * Get sessLifetime
     *
     * @return integer 
     */
    public function getSessLifetime()
    {
        return $this->sessLifetime;
    }

    /**
     * Set sessTime
     *
     * @param integer $sessTime
     * @return Sessions
     */
    public function setSessTime($sessTime)
    {
        $this->sessTime = $sessTime;

        return $this;
    }

    /**
     * Get sessTime
     *
     * @return integer 
     */
    public function getSessTime()
    {
        return $this->sessTime;
    }
}
