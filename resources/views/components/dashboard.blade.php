<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Dashboard')]
#[Layout('layouts::app', ['description' => 'Overview.'])]
class extends Component {};
?>

<div>
    DASHBOARD
</div>
