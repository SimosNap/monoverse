<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks;

final class BlockRegistry
{
    /**
     * @var array<string, BlockInterface>
     */
    private array $blocks = [];

    public function register(BlockInterface $block): void
    {
        $this->blocks[$block->type()] = $block;
    }

    /**
     * @return array<string, BlockInterface>
     */
    public function all(): array
    {
        ksort($this->blocks);

        return $this->blocks;
    }

    public function get(string $type): ?BlockInterface
    {
        return $this->blocks[$type] ?? null;
    }

    public function available(): array
    {
        $blocks = [];

        foreach ($this->blocks as $block) {

            $blocks[] = [
                'type' => $block->type(),
                'label' => $block->label(),
                'category' => $block->category(),
                'icon' => $block->icon(),
                'description' => $block->description(),
                'configurable' => $block->configurable(),
            ];

        }

        usort(
            $blocks,
            static fn(array $a, array $b) =>
                [$a['category'], $a['label']]
                <=>
                [$b['category'], $b['label']]
        );

        return $blocks;
    }

    public function categories(): array
    {
        $categories = [];

        foreach ($this->blocks as $block) {
            $categories[$block->category()][] = $block;
        }

        ksort($categories);

        return $categories;
    }
}
