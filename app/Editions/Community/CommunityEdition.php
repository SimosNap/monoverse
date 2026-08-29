<?php
declare(strict_types=1);

namespace Monoverse\Editions\Community;

class CommunityEdition
{
    public function navigation(): array
    {
        return [
            [
                'title' => 'Chat',
                'translation_key' => 'admin.navigation.chat',
                'url' => '/admin/chat',
            ],
            [
                'title' => 'Moderatori',
                'translation_key' => 'admin.navigation.moderators',
                'url' => '/admin/moderators',
            ],
            [
                'title' => 'Chanzine',
                'translation_key' => 'admin.navigation.chanzine',
                'url' => '/admin/articles',
            ],
            [
                'title' => 'Categorie',
                'translation_key' => 'admin.navigation.categories',
                'url' => '/admin/categories',
            ],
        ];
    }
}
