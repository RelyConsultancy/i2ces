<?php

namespace Evaluation\EvaluationBundle\EventListener;

use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

/**
 * Class EvaluationPreSerializeListener
 *
 * @package Evaluation\EvaluationBundle\EventListener
 *
 * @Service("evaluation_evaluation.evaluation_jms_post_serializer")
 * @Tag("jms_serializer.event_subscriber")
 */
class EvaluationPostSerializeListener implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event' => 'serializer.post_serialize',
                'class' => 'Evaluation\EvaluationBundle\Entity\Evaluation',
                'method' => 'onPostSerialize'),
        );
    }

    /**
     * @param ObjectEvent $event
     */
    public function onPostSerialize(ObjectEvent $event)
    {
        $customEntities = $event->getObject()->getCustomEntities();
        foreach ($customEntities as $key => $customEntity) {
            $event->getVisitor()->addData($key, $customEntity);
        }
    }
}
