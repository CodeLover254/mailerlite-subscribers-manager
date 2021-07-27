<?php


namespace App\Services;


use stdClass;

class SubscriberSortFilterService
{
    /**
     * @param string $searchTerm
     * @param array $data
     * @return array
     * The method a search term and the array os subscriber objects
     * and performs an array_filter by comparing all required datatable fields
     * with the parsed search term
     */
    public function filterData(string $searchTerm, array $data):array
    {
        return array_values(array_filter($data,function (stdClass $subscriberObject)use($searchTerm){
            $searchTerm = strtolower($searchTerm);
            $country = strtolower($this->getSubscriberCountry($subscriberObject->fields));
            return str_contains(strtolower($subscriberObject->name),$searchTerm)
                || str_contains(strtolower($subscriberObject->email),$searchTerm)
                || str_contains($country,$searchTerm)
                || str_contains($subscriberObject->date_subscribe,$searchTerm);
        }));
    }

    /**
     * @param int $start
     * @param int $length
     * @param array $data
     * @return array
     * The method slices an array to give a slice that is of required length
     * unless the remaining slice is less than the length in which case
     * that slice is returned
     */
    public function pageArray(int $start, int $length, array $data):array
    {
        return array_slice($data,$start,$length);
    }

    /**
     * @param int $columnId
     * @param array $data
     * @param string $direction
     * The method orders the array of subscribers given the column id and direction
     */
    public function orderData(int $columnId, array &$data, string $direction)
    {
        usort($data,function (stdClass $subscriber1, stdClass $subscriber2)use ($columnId,$direction){
            $ascending = $direction=='asc';

            if($columnId==0){
                //emails sorting
                if($ascending){
                    return strcmp($subscriber1->email,$subscriber2->email) >= 0;
                }
                return strcmp($subscriber2->email,$subscriber1->email) > 0;
            }elseif($columnId==1){
                //name sorting
                if($ascending){
                    return strcmp($subscriber1->name,$subscriber2->name) >= 0;
                }
                return strcmp($subscriber2->name,$subscriber1->name) > 0;
            }elseif ($columnId==2){
                //country sorting
                $subscriber1Country = $this->getSubscriberCountry($subscriber1->fields);
                $subscriber2Country = $this->getSubscriberCountry($subscriber2->fields);
                if($ascending){
                    return strcmp($subscriber1Country,$subscriber2Country) >= 0;
                }
                return strcmp($subscriber2Country,$subscriber1Country) > 0;
            }elseif ($columnId==3){
                //sort dates
                $subscriber1Date = strtotime(date('d/m/Y',strtotime($subscriber1->date_subscribe)));
                $subscriber2Date = strtotime(date('d/m/Y',strtotime($subscriber2->date_subscribe)));
                if($ascending){
                    return $subscriber1Date >= $subscriber2Date;
                }
                return $subscriber1Date < $subscriber2Date;
            }else{
                //sort subscribe time
                $subscriber1Time = strtotime(date('H:i:s',strtotime($subscriber1->date_subscribe)));
                $subscriber2Time = strtotime(date('H:i:s',strtotime($subscriber2->date_subscribe)));
                if($ascending){
                    return $subscriber1Time >= $subscriber2Time;
                }
                return $subscriber1Time < $subscriber2Time;
            }
        });
    }

    /**
     * @param array $fields
     * @return string
     * Given an array of field, the method returns the subscriber's country name
     */
    public function getSubscriberCountry(array $fields):string
    {
        return array_values(array_filter($fields,function ($field){
            return strtolower($field->key)=='country';
        }))[0]->value;
    }
}
