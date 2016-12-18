<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 03.12.16
 * Time: 13:33
 */
namespace AppBundle\EventSubscriber;

use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PostReviewListener implements EventSubscriberInterface {
    public function rejectPub( Event $event ) {
        /** @var AppBundle\Entity\Post $post */
        $event->getMarking()->getPlaces();
        foreach ( $event->getMarking()->getPlaces() as $k => $v ) {
            $event->getMarking()->unmark( $k );
        }
        
    }

    public static function getSubscribedEvents() {
        return array (
            'workflow.blog_publishing.guard.rejectpub' => array ( 'rejectPub' ),
        );
    }
}