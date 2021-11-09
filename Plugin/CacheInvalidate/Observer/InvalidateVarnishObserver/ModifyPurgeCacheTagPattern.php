<?php
namespace MageSuite\ImproveVarnishPurgePerformance\Plugin\CacheInvalidate\Observer\InvalidateVarnishObserver;

class ModifyPurgeCacheTagPattern
{
    /**
     * @var \Magento\PageCache\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\CacheInvalidate\Model\PurgeCache
     */
    protected $purgeCache;

    /**
     * @var \Magento\Framework\App\Cache\Tag\Resolver
     */
    protected $tagResolver;

    public function __construct(
        \Magento\PageCache\Model\Config $config,
        \Magento\CacheInvalidate\Model\PurgeCache $purgeCache,
        \Magento\Framework\App\Cache\Tag\Resolver $tagResolver
    ) {
        $this->config = $config;
        $this->purgeCache = $purgeCache;
        $this->tagResolver = $tagResolver;
    }

    public function aroundExecute(
        \Magento\CacheInvalidate\Observer\InvalidateVarnishObserver $subject,
        callable $proceed,
        \Magento\Framework\Event\Observer $observer
    ) {
        $object = $observer->getEvent()->getObject();

        if (!is_object($object)) {
            return;
        }

        if ((int)$this->config->getType() === \Magento\PageCache\Model\Config::VARNISH && $this->config->isEnabled()) {
            $bareTags = $this->tagResolver->getTags($object);

            $tags = [];
            $pattern = ',%s,';
            foreach ($bareTags as $tag) {
                $tags[] = sprintf($pattern, $tag);
            }
            if (!empty($tags)) {
                $this->purgeCache->sendPurgeRequest(array_unique($tags));
            }
        }
    }
}
