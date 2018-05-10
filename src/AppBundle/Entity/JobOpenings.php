<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * JobOpenings
 */
class JobOpenings extends AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $introduce;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $flow;

    /**
     * @var \DateTime
     */
    private $createdAt;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return JobOpenings
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set introduce
     *
     * @param string $introduce
     * @return JobOpenings
     */
    public function setIntroduce($introduce)
    {
        $this->introduce = $introduce;

        return $this;
    }

    /**
     * Get introduce
     *
     * @return string 
     */
    public function getIntroduce()
    {
        return $this->introduce;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return JobOpenings
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set flow
     *
     * @param integer $flow
     * @return Article
     */
    public function setFlow($flow)
    {
        $this->flow = $flow;

        return $this;
    }

    /**
     * Get flow
     *
     * @return integer
     */
    public function getFlow()
    {
        return $this->flow;
    }


    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return JobOpenings
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

}
