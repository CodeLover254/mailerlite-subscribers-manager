<?php

namespace Tests\Unit;

use App\Services\SubscriberSortFilterService;
use PHPUnit\Framework\TestCase;


class SubscriberSortFilterTest extends TestCase
{
    private $subscribers;
    private $sortFilterService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sortFilterService = new SubscriberSortFilterService();
        $this->subscribers=[
            (object)['name'=>'Amos','email'=>'amos@example.com','fields'=>[(object)['key'=>'country','value'=>'Ukraine']],'date_subscribe'=>'2016-04-04 11:14:18'],
            (object)['name'=>'Zippy','email'=>'goodgirl@example.com','fields'=>[(object)['key'=>'country','value'=>'Norway']],'date_subscribe'=>'2018-05-04 09:10:23'],
            (object)['name'=>'Paul','email'=>'aga@example.com','fields'=>[(object)['key'=>'country','value'=>'Zambia']],'date_subscribe'=>'2019-05-04 10:12:45'],
            (object)['name'=>'Ben','email'=>'ben@example.com','fields'=>[(object)['key'=>'country','value'=>'Algeria']],'date_subscribe'=>'2019-06-06 13:15:45'],
            (object)['name'=>'Dennis','email'=>'dennis@example.com','fields'=>[(object)['key'=>'country','value'=>'Kenya']],'date_subscribe'=>'2012-01-01 15:15:15'],
            (object)['name'=>'John','email'=>'john@example.com','fields'=>[(object)['key'=>'country','value'=>'Spain']],'date_subscribe'=>'2020-05-01 04:10:30'],
        ];
    }

    /**
     * tests if the correct country name is returned
     */
    public function test_gives_correct_country()
    {
        $this->assertEquals('Ukraine',$this->sortFilterService->getSubscriberCountry($this->subscribers[0]->fields));
        $this->assertEquals('Kenya',$this->sortFilterService->getSubscriberCountry($this->subscribers[4]->fields));
    }

    /**
     * test if filtering is done correctly using different search terms
     */
    public function test_filters_data_correctly()
    {
        $sampleSearch1 = 'exam';
        $result = $this->sortFilterService->filterData($sampleSearch1,$this->subscribers);
        $this->assertCount(count($this->subscribers),$result);

        $sampleSearch2 = 'Paul';
        $result = $this->sortFilterService->filterData($sampleSearch2,$this->subscribers);
        $this->assertEquals($sampleSearch2,$result[0]->name);

        $sampleSearch3 = 'Kenya';
        $result = $this->sortFilterService->filterData($sampleSearch3,$this->subscribers);
        $this->assertEquals($sampleSearch3,$result[0]->fields[0]->value);
    }

    /**
     * test if names are ordered correctly
     */
    public function test_orders_names_correctly()
    {
        $this->sortFilterService->orderData(1,$this->subscribers,'asc');
        $this->assertEquals('Amos',$this->subscribers[0]->name);
        $this->assertEquals('Zippy',$this->subscribers[count($this->subscribers)-1]->name);
    }

    /**
     * test if emails are ordered correctly
     */
    public function test_orders_emails_correctly()
    {
        $this->sortFilterService->orderData(0,$this->subscribers,'asc');
        $this->assertEquals('aga@example.com',$this->subscribers[0]->email);
        $this->assertEquals('john@example.com',$this->subscribers[count($this->subscribers)-1]->email);
    }

    /**
     * test if country is ordered correctly
     */
    public function test_orders_country_correctly()
    {
        $this->sortFilterService->orderData(2,$this->subscribers,'asc');
        $this->assertEquals('Algeria',$this->subscribers[0]->fields[0]->value);
        $this->assertEquals('Zambia',$this->subscribers[count($this->subscribers)-1]->fields[0]->value);
    }

    /**
     * test if dates are ordered correctly
     */
    public function test_orders_date_correctly()
    {
        $this->sortFilterService->orderData(3,$this->subscribers,'asc');
        $this->assertEquals('2012-01-01 15:15:15',$this->subscribers[0]->date_subscribe);
        $this->assertEquals('2020-05-01 04:10:30',$this->subscribers[count($this->subscribers)-1]->date_subscribe);
    }

    /**
     * test if time is ordered correctly
     */
    public function test_orders_time_correctly()
    {
        $this->sortFilterService->orderData(4,$this->subscribers,'asc');
        $this->assertEquals('2020-05-01 04:10:30',$this->subscribers[0]->date_subscribe);
        $this->assertEquals('2012-01-01 15:15:15',$this->subscribers[count($this->subscribers)-1]->date_subscribe);
    }

    /**
     * test if the array is slices correctly
     */
    public function test_slices_array_correctly()
    {
        $result = $this->sortFilterService->pageArray(0,2,$this->subscribers);
        $this->assertCount(2,$result);
    }
}
