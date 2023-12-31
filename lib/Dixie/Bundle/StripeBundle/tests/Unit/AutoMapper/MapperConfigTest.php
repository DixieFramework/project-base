<?php

namespace Talav\StripeBundle\Tests\AutoMapper;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\MapperInterface;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Talav\StripeBundle\AutoMapper\MapperConfig;
use Talav\StripeBundle\Entity\Customer;
use Talav\StripeBundle\Entity\Price;
use Talav\StripeBundle\Entity\PriceRecurring;
use Talav\StripeBundle\Entity\Product;
use Talav\StripeBundle\Enum\RecurringInterval;
use Talav\StripeBundle\Event\EventExtractor;
use Talav\StripeBundle\Tests\Helper\FileContentRequest;

class MapperConfigTest extends TestCase
{
    use FileContentRequest;

    protected MapperInterface $mapper;

    protected EventExtractor $extractor;

    public function setUp(): void
    {
        $product = new Product();
        $em = $this->createStub(EntityManager::class);
        $em->method('getReference')
            ->with($this->equalTo(Product::class), $this->equalTo('prod_Leyyy3KhUWphiQ'))
            ->willReturn($product);

        $config = new AutoMapperConfig();
        $config->getOptions()->createUnregisteredMappings();
        $config->getOptions()->ignoreNullProperties();
        $configurator = new MapperConfig($em, ['product' => Product::class, 'price' => Price::class]);
        $configurator->configure($config);
        $this->mapper = new AutoMapper($config);
        $this->extractor = new EventExtractor(null);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_maps_price_created_event_to_new_price_entity()
    {
        $price = new Price();
        $eventData = $this->getEventData('price.created');
        $this->mapper->mapToObject($eventData, $price);
        self::assertEquals($eventData->id, $price->getId());
        self::assertEquals($eventData->active, $price->isActive());
        self::assertEquals($eventData->billing_scheme, $price->getBillingScheme()->value);
        self::assertEquals($eventData->created, $price->getCreated());
        self::assertEquals($eventData->currency, $price->getCurrency());
        self::assertEquals($eventData->recurring['interval'], $price->getRecurring()->getInterval()->value);
        self::assertEquals($eventData->recurring['usage_type'], $price->getRecurring()->getUsageType()->value);
        self::assertEquals($eventData->recurring['interval_count'], $price->getRecurring()->getIntervalCount());
        self::assertEquals($eventData->tax_behavior, $price->getTaxBehavior()->value);
        self::assertEquals($eventData->type, $price->getType()->value);
        self::assertEquals($eventData->unit_amount, $price->getUnitAmount());
        self::assertEquals($eventData->unit_amount_decimal, $price->getUnitAmountDecimal());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_maps_price_created_event_and_overrides_embedded_object()
    {
        $price = new Price();
        $recurring = new PriceRecurring();
        $recurring->setIntervalCount(2);
        $recurring->setInterval(RecurringInterval::WEEK);
        $eventData = $this->getEventData('price.created');
        $this->mapper->mapToObject($eventData, $price);
        self::assertEquals($eventData->recurring['interval'], $price->getRecurring()->getInterval()->value);
        self::assertEquals($eventData->recurring['interval_count'], $price->getRecurring()->getIntervalCount());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_maps_customer_created_event()
    {
        $customer = new Customer();
        $eventData = $this->getEventData('customer.created');
        $this->mapper->mapToObject($eventData, $customer);
        self::assertEquals($eventData->id, $customer->getId());
        self::assertEquals($eventData->balance, $customer->getBalance());
        self::assertEquals($eventData->created, $customer->getCreated());
        self::assertEquals($eventData->delinquent, $customer->isDelinquent());
        self::assertEquals($eventData->email, $customer->getEmail());
        self::assertEquals($eventData->invoice_prefix, $customer->getInvoicePrefix());
        self::assertEquals($eventData->livemode, $customer->isLivemode());
        self::assertEquals($eventData->name, $customer->getName());
    }

    private function getEventData($name): \stdClass
    {
        $event = $this->extractor->extract($this->getEventFromFile($name));

        return (object) $event->data->object->toArray();
    }
}
