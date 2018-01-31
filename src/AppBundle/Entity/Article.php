<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Article
 */
class Article extends AbstractEntity
{
    /**
     * @var integer
     */
    private $categoryId;

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
    private $thumbnail;

    /**
     * @var \DateTime
     */
    private $publishtime;

    /**
     * @var boolean
     */
    private $recommend='0';

    /**
     * @var integer
     */
    private $rank='0';

    /**
     * @var integer
     */
    private $flow='1';

    /**
     * @var boolean
     */
    private $deleted='0';

    /**
     * @var boolean
     */
    private $published='1';

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var integer
     */
    private $id;

    /** @var  Category */
    private $category;

    /**
     * @var array
     */
    private $tags;

    /**
     * Set categoryId
     *
     * @param integer $categoryId
     * @return Article
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Get categoryId
     *
     * @return integer 
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Article
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
     * @return Article
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
     * @return Article
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
     * Set thumbnail
     *
     * @param string $thumbnail
     * @return Article
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * Get thumbnail
     *
     * @return string 
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Set publishtime
     *
     * @param \DateTime $publishtime
     * @return Article
     */
    public function setPublishtime($publishtime)
    {
        $this->publishtime = $publishtime;

        return $this;
    }

    /**
     * Get publishtime
     *
     * @return \DateTime 
     */
    public function getPublishtime()
    {
        return $this->publishtime;
    }

    /**
     * Set recommend
     *
     * @param boolean $recommend
     * @return Article
     */
    public function setRecommend($recommend)
    {
        $this->recommend = (boolean)$recommend;

        return $this;
    }

    /**
     * Get recommend
     *
     * @return boolean 
     */
    public function getRecommend()
    {
        return $this->recommend;
    }

    /**
     * Set rank
     *
     * @param integer $rank
     * @return Article
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return integer 
     */
    public function getRank()
    {
        return $this->rank;
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
     * Set deleted
     *
     * @param boolean $deleted
     * @return Article
     */
    public function setDeleted($deleted)
    {
        $this->deleted = (boolean)$deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return Article
     */
    public function setPublished($published)
    {
        $this->published = (boolean)$published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdat
     * @return Article
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

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set tags
     *
     * @param $tags
     *
     * @return Article
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * Get tags
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }
}
