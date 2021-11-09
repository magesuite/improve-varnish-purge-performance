<?php
namespace MageSuite\ImproveVarnishPurgePerformance\Plugin\Framework\App\Response\HttpInterface;

class AddCommasToXMagentoTagsHeader
{
    const HEADER_X_MAGENTO_TAGS = 'X-Magento-Tags';

    /**
     * @var \Magento\PageCache\Model\Config
     */
    protected $cacheConfig;

    public function __construct(\Magento\PageCache\Model\Config $cacheConfig)
    {
        $this->cacheConfig = $cacheConfig;
    }

    public function beforeSetHeader(
        \Magento\Framework\App\Response\HttpInterface $subject,
        $name,
        $value,
        $replace = false
    ) {
        if (!$this->shouldModifyHeader($name, $value)) {
            return null;
        }
        if (substr($value, 0, 1) !== ',') {
            $value = ',' . $value;
        }
        if (substr($value, -1, 1) !== ',') {
            $value .= ',';
        }
        return [$name, $value, $replace];
    }

    protected function shouldModifyHeader($name, $value)
    {
        if ($name !== self::HEADER_X_MAGENTO_TAGS) {
            return false;
        }
        if (empty($value)) {
            return false;
        }
        if (!$this->cacheConfig->isEnabled()) {
            return false;
        }
        if ((int)$this->cacheConfig->getType() !== \Magento\PageCache\Model\Config::VARNISH) {
            return false;
        }
        return true;
    }
}
