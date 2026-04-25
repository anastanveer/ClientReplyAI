<?php

namespace App\Livewire\Pages;

use App\Models\Template;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class TemplatesPage extends Component
{
    public string $activeCategory = 'all';

    public function useTemplate(int $id): void
    {
        $template = Template::findOrFail($id);

        session()->put('apply_template', [
            'content' => $template->content,
            'use_case' => $template->use_case,
            'tone' => $template->tone,
            'language' => $template->language,
        ]);

        $this->redirect(route('dashboard'));
    }

    public function setCategory(string $category): void
    {
        $this->activeCategory = $category;
    }

    public function render(): View
    {
        $categories = Template::distinct()
            ->orderBy('category')
            ->pluck('category');

        $templates = Template::when(
            $this->activeCategory !== 'all',
            fn ($q) => $q->where('category', $this->activeCategory)
        )
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return view('livewire.pages.templates-page', [
            'categories' => $categories,
            'templates' => $templates,
        ]);
    }
}
