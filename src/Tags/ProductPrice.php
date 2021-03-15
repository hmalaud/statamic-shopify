<?php

namespace Jackabox\Shopify\Tags;

use Jackabox\Shopify\Traits\HasProductVariants;
use Statamic\Tags\Tags;

class ProductPrice extends Tags
{
    use HasProductVariants;

    /**
     * @return string|array
     */
    public function index()
    {
        if (!$this->params->get('product')) {
            return;
        }

        $variants = $this->fetchProductVariants($this->params->get('product'));

        if (!$variants) {
            return null;
        }

        $html = '';

        $stock = 0;
        $deny = false;

        foreach ($variants as $variant) {
            $stock += $variant['inventory_quantity'];
            $deny = $variant['inventory_policy'] === 'deny';
        }

        if ($stock === 0 and $deny) {
            return 'Out of Stock';
        }

        $pricePluck = $variants->pluck('price');

        if ($pricePluck->count() > 1 && $this->params->get('show_from') === true) {
            $html .= 'From ';
        }

        $html .= config('shopify.currency') . $pricePluck->sort()->splice(0, 1)[0];

        return $html;
    }
}
