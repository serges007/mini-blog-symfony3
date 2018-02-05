<?php

namespace Apps\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdvertSkill
 *
 * @ORM\Table(name="advert_skill")
 * @ORM\Entity(repositoryClass="Apps\PlatformBundle\Repository\AdvertSkillRepository")
 */
class AdvertSkill {
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="level", type="string", length=255)
     */
    private $level;

    /**
     * @ORM\ManyToOne(targetEntity="Apps\PlatformBundle\Entity\Advert")
     * @ORM\JoinColumn(nullable=false)
     */
    private $advert;

    /**
     * @ORM\ManyToOne(targetEntity="Apps\PlatformBundle\Entity\Skill")
     * @ORM\JoinColumn(nullable=false)
     */
    private $skill;

    /**
     * @return integer
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @param string $level
     */
    public function setLevel($level){
        $this->level = $level;
    }

    /**
     * @return string
     */
    public function getLevel(){
        return $this->level;
    }

    /**
     * @param Advert $advert
     */
    public function setAdvert(Advert $advert){
        $this->advert = $advert;
    }

    /**
     * @return Advert
     */
    public function getAdvert(){
        return $this->advert;
    }

    /**
     * @param Skill $skill
     */
    public function setSkill(Skill $skill){
        $this->skill = $skill;
    }

    /**
     * @return Skill
     */
    public function getSkill() {
      return $this->skill;
    }
}
