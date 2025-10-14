<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MainLayout extends Component
{
    public $titlePage;

    public function __construct($titlePage = null)
    {
        $this->titlePage = $titlePage;
    }

    public function render(): View
    {
        return view('layouts.main');
    }
}
