<?php

namespace Dolphin\Freesample\Model;

// use Dolphin\Freesample\Api\Data\GridInterface;

class Grid extends \Magento\Framework\Model\AbstractModel
{
	const CACHE_TAG = 'dolphin_freesample';
	protected $_cacheTag = 'dolphin_freesample';
	protected $_eventPrefix = 'dolphin_freesample';
	protected function _construct()
	{
		$this->_init('Dolphin\Freesample\Model\ResourceModel\Grid');
	}
}
