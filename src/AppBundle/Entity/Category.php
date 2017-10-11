<?php
/**
 * @file
 */

namespace AppBundle\Entity;

use AppBundle\Utils\Jobeet;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CategoryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Category
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * One category has many jobs.
     *
     * @ORM\OneToMany(targetEntity="Job", mappedBy="category")
     */
    private $jobs;

    /**
     * Many categories have many affiliates.
     *
     * @ORM\ManyToMany(targetEntity="Affiliate", mappedBy="categories")
     */
    private $affiliates;

    /**
     * @var
     */
    private $activeJobs;

    /**
     * @var
     */
    private $moreJobs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->jobs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->affiliates = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add jobs
     *
     * @param \AppBundle\Entity\Job $jobs
     * @return Category
     */
    public function addJob(\AppBundle\Entity\Job $jobs)
    {
        $this->jobs[] = $jobs;

        return $this;
    }

    /**
     * Remove jobs
     *
     * @param \AppBundle\Entity\Job $jobs
     */
    public function removeJob(\AppBundle\Entity\Job $jobs)
    {
        $this->jobs->removeElement($jobs);
    }

    /**
     * Get jobs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * Add affiliates
     *
     * @param \AppBundle\Entity\Affiliate $affiliates
     * @return Category
     */
    public function addAffiliate(\AppBundle\Entity\Affiliate $affiliates)
    {
        $this->affiliates[] = $affiliates;

        return $this;
    }

    /**
     * Remove affiliates
     *
     * @param \AppBundle\Entity\Affiliate $affiliates
     */
    public function removeAffiliate(\AppBundle\Entity\Affiliate $affiliates)
    {
        $this->affiliates->removeElement($affiliates);
    }

    /**
     * Get affiliates
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAffiliates()
    {
        return $this->affiliates;
    }

    public function __toString()
    {
        return $this->getName() ? $this->getName() : '';
    }

    public function setActiveJobs($jobs)
    {
        $this->activeJobs = $jobs;
    }

    public function getActiveJobs()
    {
        return $this->activeJobs;
    }

    public function setMoreJobs($jobs)
    {
        $this->moreJobs = $jobs >= 0 ? $jobs : 0;
    }

    public function getMoreJobs()
    {
        return $this->moreJobs;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Category
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @ORM\PrePersist
     */
    public function setSlugValue()
    {
        $this->slug = Jobeet::slugify($this->getName());
    }
}
