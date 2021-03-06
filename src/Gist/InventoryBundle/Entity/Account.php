<?php

namespace Gist\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Gist\CoreBundle\Template\Entity\HasGeneratedID;
use Gist\CoreBundle\Template\Entity\TrackCreate;
use Gist\CoreBundle\Template\Entity\HasName;

/**
 * @ORM\Entity
 * @ORM\Table(name="inv_account")
 */
class Account
{
    use HasGeneratedID;
    use TrackCreate;
    use HasName;

    /** @ORM\Column(type="boolean") */
    protected $allow_negative;

    /**
     * @ORM\OneToOne(targetEntity="Account", inversedBy="dmg_container")
     * @ORM\JoinColumn(name="damaged_items_container_id", referencedColumnName="id")
     **/
    protected $damaged_items_container;

    /** @ORM\OneToOne(targetEntity="Account", mappedBy="damaged_items_container") */
    protected $dmg_container;

    /**
     * @ORM\OneToOne(targetEntity="Account", inversedBy="mis_container")
     * @ORM\JoinColumn(name="missing_items_container_id", referencedColumnName="id")
     **/
    protected $missing_items_container;

    /** @ORM\OneToOne(targetEntity="Account", mappedBy="missing_items_container") */
    protected $mis_container;

    /**
     * @ORM\OneToOne(targetEntity="Account", inversedBy="tester_container")
     * @ORM\JoinColumn(name="tester_items_container_id", referencedColumnName="id")
     **/
    protected $tester_items_container;

    /** @ORM\OneToOne(targetEntity="Account", mappedBy="tester_items_container") */
    protected $tester_container;

    public function __construct()
    {
        $this->initHasGeneratedID();
        $this->initTrackCreate();
        $this->initHasName();

        $this->allow_negative = false;
    }

    public function setAllowNegative($allow = true)
    {
        $this->allow_negative = $allow;
        return $this;
    }

    public function canAllowNegative()
    {
        if ($this->allow_negative)
            return true;

        return false;
    }

    public function setDamagedContainer($damaged_items_container)
    {
        $this->damaged_items_container = $damaged_items_container;
        return $this;
    }

    public function getDamagedContainer()
    {
        return $this->damaged_items_container;
    }

    public function setMissingContainer($missing_items_container)
    {
        $this->missing_items_container = $missing_items_container;
        return $this;
    }

    public function getMissingContainer()
    {
        return $this->missing_items_container;
    }

    public function setTesterContainer($tester_items_container)
    {
        $this->tester_items_container = $tester_items_container;
        return $this;
    }

    public function getTesterContainer()
    {
        return $this->tester_items_container;
    }

    public function toData()
    {
        $data = new \stdClass();

        $this->dataHasGeneratedID($data);
        $this->dataTrackCreate($data);
        $this->dataHasName($data);

        return $data;
    }
}
