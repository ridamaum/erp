<?php

namespace Gist\AccountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gist\AccountingBundle\Entity\JournalEntryAbstract;

/**
 * @ORM\Entity
 */
class CDJJournalEntry extends JournalEntryAbstract
{
    
    public function toData()
    {
        $data = parent::toData();

        return $data;
    }
}
