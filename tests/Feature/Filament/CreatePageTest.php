<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\CreatePage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreatePageTest extends TestCase
{
    #[Test]
    public function it_registers_a_create_page_navigation_entry(): void
    {
        $this->assertSame('Create Page', CreatePage::getNavigationLabel());
        $this->assertSame('Pages', CreatePage::getNavigationGroup());
        $this->assertSame('heroicon-o-plus-circle', CreatePage::getNavigationIcon());
    }
}
