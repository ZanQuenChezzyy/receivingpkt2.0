<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Welcome - Receiving PKT')]
class Home extends Component
{
    public function render()
    {
        return view('livewire.frontend.home')->layout('components.layouts.frontend');
    }
}
