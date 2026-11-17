<?php

namespace App\Livewire;

use Livewire\Component;

class Hello extends Component
{
    public function render()
    {
        return view('livewire.hello')
            ->layout('components.layouts.app')
            ->title('PASKA JAYA — Setup Berhasil');
    }
}
