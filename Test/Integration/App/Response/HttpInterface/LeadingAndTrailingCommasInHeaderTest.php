<?php
namespace MageSuite\ImproveVarnishPurgePerformance\Test\Integration\App\Response\HttpInterface;

class LeadingAndTrailingCommasInHeaderTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/products.php
     * @magentoCache full_page enabled
     * @magentoConfigFixture system/full_page_cache/caching_application 2
     * @return void
     */
    public function testCommasAreAddedToHeaderWhenVarnishFpcIsEnabled()
    {
        $cache = $this->_objectManager->get(\Magento\Framework\App\Cache\Manager::class);
        $cache->clean(['full_page']);
        $this->dispatch('catalog/product/view/id/1');
        $hTags = $this->getResponse()->getHeader('X-Magento-Tags');
        $this->assertMatchesRegularExpression('/^,.*cat_p_1,cat_p.*,$/', $hTags->getFieldValue());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/products.php
     * @magentoCache full_page enabled
     * @magentoConfigFixture system/full_page_cache/caching_application 1
     * @return void
     */
    public function testCommasAreNotAddedForNonVarnishFpc()
    {
        $cache = $this->_objectManager->get(\Magento\Framework\App\Cache\Manager::class);
        $cache->clean(['full_page']);
        $this->dispatch('catalog/product/view/id/1');
        $hTags = $this->getResponse()->getHeader('X-Magento-Tags');
        $this->assertStringStartsNotWith(',', $hTags->getFieldValue());
        $this->assertStringEndsNotWith(',', $hTags->getFieldValue());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/products.php
     * @magentoCache full_page disabled
     * @return void
     */
    public function testHeaderIsNotPresentWhenFpcIsDisabled()
    {
        $this->dispatch('catalog/product/view/id/1');
        $hTags = $this->getResponse()->getHeader('X-Magento-Tags');
        $this->assertIsBool($hTags);
    }
}
