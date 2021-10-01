<?php


namespace App\Event;


use App\Entity\Entry;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class EntrySubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [Events::prePersist, Events::preUpdate];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->createMoment($args);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->createMoment($args);
    }

    private function createMoment(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();
        if (!$object instanceof Entry) {
            return;
        }

        $min = (int) $object->time->format('i');
        $min = $min - ($min % 5);

        $hour = $object->time->format('H');
        $object->moment = sprintf('%02d%02d', $hour, $min);
    }
}