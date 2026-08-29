<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Response;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Services\NotificationService;
use Monoverse\Services\ProfileService;
use Monoverse\Services\SimosNapService;
use Monoverse\Core\Blocks\BlockManager;
use Monoverse\Services\SettingsService;

class LandingChatController extends BaseController
{
    public function __construct(
        View $view,
        Response $response,
        Session $session,
        NotificationService $notifications,
        private ProfileService $profiles,
        private SimosNapService $simosnap,
        private BlockManager $blockManager,
        SettingsService $settings
    ) {
        parent::__construct(
            $view,
            $response,
            $session,
            $notifications,
            $settings
        );
    }

    public function index(): void
    {
        $settings = $this->settings->all();

        $user = $this->session->get('auth.user');

        if (!is_array($user)) {
            $user = [];
        }

        $profile = !empty($user['sub'])
            ? $this->profiles->findBySub((string) $user['sub'])
            : false;

        $defaultChannel = (string) (
            $settings['chat_default_channel'] ?? '#chat'
        );

        $chatTitle = (string) (
            $settings['chat_title'] ?? $defaultChannel . ' - Chat'
        );

        $channelInfo = $this->simosnap->getChannel(
            $defaultChannel
        );

        if (!is_array($channelInfo)) {
            $channelInfo = [];
        }

        $channelFeatures = $this->simosnap->getChannelFeatures(
            $channelInfo
        );
        
        $channelModes = (string) (
            $channelInfo['modes']
                ?? ''
        );
        
        $registeredOnly = str_contains(
            $channelModes,
            'R'
        );

        $blocksBeforeEntry = $this->blockManager->renderArea(
            'landing-chat',
            'before-entry'
        );
        
        $blocksEntryLeftBefore = $this->blockManager->renderArea(
            'landing-chat',
            'entry-left-before'
        );
        
        $blocksEntryLeftAfter = $this->blockManager->renderArea(
            'landing-chat',
            'entry-left-after'
        );

        $blocksAfterEntry = $this->blockManager->renderArea(
            'landing-chat',
            'after-entry'
        );

        $blocksBeforeFooter = $this->blockManager->renderArea(
            'landing-chat',
            'before-footer'
        );

        $this->render('landing-chat', [
            'title' => $chatTitle,

            'profile' => $profile ?: [],

            'chatTitle' => $chatTitle,
            'defaultChannel' => $defaultChannel,
            'chatTheme' => (string) (
                $settings['chat_theme'] ?? 'Osprey'
            ),
            'channelInfo' => $channelInfo,
            'channelFeatures' => $channelFeatures,
            'registeredOnly' => $registeredOnly,
            'blocksBeforeEntry' => $blocksBeforeEntry,
            'blocksEntryLeftBefore' => $blocksEntryLeftBefore,
            'blocksEntryLeftAfter' => $blocksEntryLeftAfter,
            'blocksAfterEntry' => $blocksAfterEntry,
            'blocksBeforeFooter' => $blocksBeforeFooter,
            'blockCssFiles' => $this->blockManager->stylesheets(),
            'blockJsFiles' => $this->blockManager->scripts(),
            'stateKey' => (string) (
                $settings['chat_state_key'] ?? ''
            ),
            'kiwiUrl' => 'https://kiwiirc.simosnap.com/login.php',
        ]);
    }
}
