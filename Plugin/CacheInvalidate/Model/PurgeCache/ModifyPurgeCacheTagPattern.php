<?php
namespace MageSuite\ImproveVarnishPurgePerformance\Plugin\CacheInvalidate\Model\PurgeCache;

class ModifyPurgeCacheTagPattern
{
    public function beforeSendPurgeRequest(\Magento\CacheInvalidate\Model\PurgeCache $subject, $tags)
    {
        if (is_string($tags)) {
            $tags = [$tags];
        }

        $tags = preg_replace('/\(\(\^\|\,\)(.*)\(\,\|\$\)\)/', ',$1,', $tags);

        return $tags;
    }
}
