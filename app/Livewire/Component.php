<?php

namespace App\Livewire;

use Livewire\Component as LivewireComponent;
use TallStackUi\Traits\Interactions;

/**
 * Base dos componentes que usam as interações do TallStackUI (toast, dialog).
 */
abstract class Component extends LivewireComponent
{
    use Interactions;
}
