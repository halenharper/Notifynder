<?php
use Fenos\Notifynder\Notifications\NotificationManager;

/**
 * Class NotificationTest
 */
class NotificationTest extends TestCaseDB {

    use CreateModels;

    /**
     * @var NotificationManager
     */
    protected $notification;

    /**
     * @var int
     */
    protected $multiNotificationsNumber = 10;

    /**
     * @var array
     */
    protected $to = [
        'id' => 1,
        'type' => 'Fenos\Tests\Models\User'
    ];

    /**
     * Set Up Test
     */
    public function setUp()
    {
        parent::setUp();
        $this->notification = app('notifynder.notification');
    }

    /** @test */
    function it_retrieve_notification_with_parsed_body()
    {
        $category = $this->createCategory(['text' => 'parse this {extra} value']);

        $notification = $this->createNotification(['extra' => 'Amazing','category_id' => $category->id]);

        $notifications = $this->notification->getNotRead($notification->to->id);

        $bodyParsed = 'parse this Amazing value';
        $this->assertEquals($bodyParsed,$notifications[0]->body->text);
    }

    /** @test */
    function it_retrieve_notification_by_limiting_the_number()
    {
        $this->createMultipleNotifications();

        // set polymorphic to true
        app('config')->set('notifynder.polymorphic',true);

        $notification = $this->createNotification(['extra' => 'Amazing']);

        $notifications = $this->notification->entity($this->to['type'])
            ->getAll($notification->to->id);

        $this->assertCount(1,$notifications);
    }

    /** @test */
    function it_retrieve_notification_by_paginating_the_number()
    {
        app('config')->set('notifynder.polymorphic',false);

        $category = $this->createCategory(['text' => 'parse this {extra} value']);

        $notification = $this->createNotification(['extra' => 'Amazing','category_id' => $category->id]);

        $notifications = $this->notification->getNotRead($notification->to->id,10,true);

        $bodyParsed = 'parse this Amazing value';
        $this->assertEquals($bodyParsed,$notifications['data'][0]['body']['text']);
    }
}