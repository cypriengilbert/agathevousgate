<?php

namespace AdminBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class StockList
 * @package AdminBundle\Model
 *
 * @ORM\Entity()
 */
class StockList
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="CommerceBundle\Stock")
     */
    private $stocks;

    public function __construct()
    {
        $this->stocks = new ArrayCollection();
    }

    /**
     * @param CommerceBundle\Entity\Stock $stock
     * @return $this
     */
    public function addStock(\CommerceBundle\Entity\Stock $stock)
    {
        $this->stocks[] = $stock;

        return $this;
    }

    /**
     * @param CommerceBundle\Entity\Stock $stock
     * @return $this
     */
    public function removeStock(\CommerceBundle\Entity\Stock $stock)
    {
        $this->stocks->remove($stock);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getStocks()
    {
        return $this->stocks;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $stocks
     * @return $this
     */
    public function setStocks(\Doctrine\Common\Collections\Collection $stocks)
    {
        $this->stocks = $stocks;

        return $this;
    }

    /**
     * @param \Knp\Component\Pager\Pagination\PaginationInterface $pagination
     * @return $this
     */
    public function setFromPagination(\Knp\Component\Pager\Pagination\PaginationInterface $pagination)
    {
        foreach ($pagination as $stock) {
            $this->addStock($stock);
        }

        return $this;
    }
}
