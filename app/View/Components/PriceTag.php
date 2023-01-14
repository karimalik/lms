<?php

namespace App\View\Components;

use Illuminate\View\Component;

class PriceTag extends Component
{
    public $price, $discount;

    public function __construct($price, $discount = null)
    {

        $this->price = $price;
        $this->discount = $discount;
    }


    public function render()
    {
        return view(theme('components.price-tag'));
    }
}
