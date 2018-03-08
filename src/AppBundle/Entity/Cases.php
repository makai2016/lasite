<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cases
 */
class Cases extends AbstractEntity
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
    private $thumbnail;

    /**
     * @var  string
     */
    private $banner;

    /**
     * @var string
     */
    private $introduce;

    /**
     * @var string
     */
    private $content;

    /**
     * @var boolean
     */
    private $deleted = '0';

    /**
     * @var boolean
     */
    private $recommend='0';

    /**
     * @var boolean
     */
    private $published = '1';

    /** @var string  */
    private $price = '1';

    /** @var integer  */
    private $showPrice = 0;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var integer
     */
    private $id;
	
	/**
	 * @var string
	 */
	private $video;

    /**
     * @var integer
     */
	private $rank =1;

    /**
     * @var  Category
     */
    private $category;

    /**
     * @var array
     */
    private $tags;

    /**
     * Set categoryId
     *
     * @param integer $categoryId
     * @return Cases
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
     * @return Cases
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
     * Set thumbnail
     *
     * @param string $thumbnail
     * @return Cases
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
     * Set banner
     *
     * @param $banner
     *
     * @return Cases
     */
    public function setBanner($banner)
    {
        $this->banner = $banner;
        return $this;
    }

    /**
     * Get banner
     *
     * @return string
     */
    public function getBanner()
    {
        return $this->banner;
    }

    /**
     * Set introduce
     *
     * @param string $introduce
     * @return Cases
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
     * @return Cases
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
     * Set deleted
     *
     * @param boolean $deleted
     * @return Cases
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
     * Set recommend
     *
     * @param boolean $recommend
     * @return Cases
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
     * Set published
     *
     * @param boolean $published
     * @return Cases
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
     * @param \DateTime $createdAt
     * @return Cases
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Set price
     *
     * @param $price
     *
     * @return Cases
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set showPrice
     *
     * @param $showPrice
     *
     * @return Cases
     */
    public function setShowPrice($showPrice)
    {
        $this->showPrice = $showPrice;
        return $this;
    }

    /**
     * @return int
     */
    public function getShowPrice()
    {
        return $this->showPrice;
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
	 * @return string
	*/
	public function getVideo()
	{
	    return $this->video;
	}
	
	/**
	 * @return Cases
	*/
	public function setVideo($video)
	{
		$this->video = $video;
		return $this;
	}

    /**
     * Get rank
     *
     * @return int
     */
	public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set rank
     *
     * @param $rank
     *
     * @return Cases
     */
    public function setRank($rank)
    {
        $this->rank = $rank;
        return $this;
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

    /**
     * Set category
     *
     * @param $category
     *
     * @return Cases
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
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
     * @return Cases
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
