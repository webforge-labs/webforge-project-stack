<?php

namespace Webforge\ProjectStack\Serializer;

use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\Context;
use DateTimeZone;
use Webforge\Common\DateTime\DateTime;

class DateTimeHandler implements SubscribingHandlerInterface {

  protected $format;

  public static function getSubscribingMethods() {
    return array(
      array(
        'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
        'format' => 'json',
        'type' => 'WebforgeDateTime',
        'method' => 'serializeDateTime',
      ),

      array(
        'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
        'format' => 'json',
        'type' => 'WebforgeDateTime',
        'method' => 'deserializeDateTime',
      ),
    );
  }

  public function __construct($format) {
    $this->format = $format;
  }

  public function serializeDateTime(JsonSerializationVisitor $visitor, DateTime $date, array $type, Context $context) {
    if ($this->format === 'json') {
      return (object) array(
        'date'=>$date->format('Y-m-d H:i:s'),
        'timezone'=>$date->getTimezone()->getName()
        );
    } else {
      return $date->format(DateTime::ISO8601);
    }
  }

  public function deserializeDateTime(JsonDeserializationVisitor $visitor, $json, array $type, Context $context) {
    if ($this->format === 'json') {
      $json = (object) $json;
      return DateTime::parse('Y-m-d H:i:s', $json->date, new DateTimeZone($json->timezone));
    } else {
      return new DateTime(strtotime($json)); // php you're kidding ... 
    }
  }
}
