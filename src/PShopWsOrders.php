<?php

namespace pshopws;

/**
 * @author Marcos Redondo <kusflo at gmail.com>
 */
class PShopWsOrders extends PShopWs
{
    public function __construct($url, $key, $debug = false)
    {
        parent::__construct($url, $key, $debug);
    }

    public function getById($id)
    {
        $options['resource'] = "orders";
        $options['id'] = $id;
        $objects = $this->get($options);

        return ServiceSimpleXmlToArray::take($objects->order);
    }

    public function getList()
    {
        $options['resource'] = "orders";
        $options['display'] = "full";

        return $this->getOrders($options);
    }

    public function getListLastDays($days = 7)
    {
        $orders = array();
        $days = $this->getLastDays($days);
        foreach ($days as $day) {
            $result = $this->getListByDay($day);
            if ($result) {
                $orders [] = $result[0];
            }
        }

        return $orders;
    }

    public function getListToday()
    {
        return $this->getListByDay($this->getDateTimeNow()->format('Y-m-d'));
    }

    private function getListByDay($day)
    {
        $options['resource'] = "orders";
        $options['display'] = "full";
        $options['filter[date_add]'] = ServicePShopFilters::byDay($day);

        return $this->getOrders($options);
    }

    private function getLastDays($days)
    {
        $array = array();
        for ($i = 0; $i < $days; $i++) {
            $array[] = $this->getDateTimeNow()
                ->sub(new \DateInterval("P".$i."D"))->format('Y-m-d');
        }

        return $array;
    }

    /**
     * @param $options
     * @return array
     */
    private function getOrders($options)
    {
        $objects = $this->get($options);

        return ServiceSimpleXmlToArray::takeMultiple($objects->orders->order);
    }

    /**
     * @return \DateTime
     * @throws PShopWsException
     */
    private function getDateTimeNow()
    {
        if (!date_default_timezone_get()) {
            throw new PShopWsException("You must inform the time_zone in your php.ini");
        }

        return new \DateTime();
    }
}