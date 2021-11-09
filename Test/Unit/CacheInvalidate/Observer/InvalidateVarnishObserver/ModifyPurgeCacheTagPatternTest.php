<?php
namespace MageSuite\ImproveVarnishPurgePerformance\Test\Unit\CacheInvalidate\Observer\InvalidateVarnishObserver;

class ModifyPurgeCacheTagPatternTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\ImproveVarnishPurgePerformance\Plugin\CacheInvalidate\Observer\InvalidateVarnishObserver\ModifyPurgeCacheTagPattern
     */
    protected $plugin;

    /**
     * @var \Magento\PageCache\Model\Config|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\CacheInvalidate\Model\PurgeCache|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $purgeCacheMock;

    /**
     * @var \Magento\Framework\App\Cache\Tag\Resolver|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $tagResolverMock;

    /**
     * @var \Magento\Framework\Event\Observer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $observerMock;

    /**
     * @var \Magento\CacheInvalidate\Observer\InvalidateVarnishObserver
     */
    protected $modelMock;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $observerObject;

    protected function setUp(): void
    {
        $this->configMock = $this->createPartialMock(\Magento\PageCache\Model\Config::class, ['getType', 'isEnabled']);
        $this->purgeCacheMock = $this->createMock(\Magento\CacheInvalidate\Model\PurgeCache::class);
        $this->tagResolverMock = $this->createMock(\Magento\Framework\App\Cache\Tag\Resolver::class);

        $this->observerMock = $this->createPartialMock(\Magento\Framework\Event\Observer::class, ['getEvent']);
        $this->observerObject = $this->createMock(\Magento\Store\Model\Store::class);

        $this->modelMock = $this->createMock(\Magento\CacheInvalidate\Observer\InvalidateVarnishObserver::class);

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->plugin = $objectManager->getObject(
            \MageSuite\ImproveVarnishPurgePerformance\Plugin\CacheInvalidate\Observer\InvalidateVarnishObserver\ModifyPurgeCacheTagPattern::class,
            [
                'config' => $this->configMock,
                'purgeCache' => $this->purgeCacheMock,
                'tagResolver' => $this->tagResolverMock
            ]
        );
    }

    public function testInvalidateVarnish()
    {
        $tags = ['cache_1', 'cache_group'];
        $pattern = [',cache_1,', ',cache_group,'];

        $this->configMock->expects($this->once())->method('isEnabled')->willReturn(true);
        $this->configMock->expects(
            $this->once()
        )->method(
            'getType'
        )->willReturn(
            \Magento\PageCache\Model\Config::VARNISH
        );

        $eventMock = $this->getMockBuilder(\Magento\Framework\Event::class)
            ->addMethods(['getObject'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($this->once())->method('getObject')->willReturn($this->observerObject);
        $this->observerMock->expects($this->once())->method('getEvent')->willReturn($eventMock);
        $this->tagResolverMock->expects($this->once())->method('getTags')->with($this->observerObject)
            ->willReturn($tags);
        $this->purgeCacheMock->expects($this->once())->method('sendPurgeRequest')->with($pattern);

        $this->plugin->aroundExecute(
            $this->modelMock,
            function () {
            },
            $this->observerMock
        );
    }
}
