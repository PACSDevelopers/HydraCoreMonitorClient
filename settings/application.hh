<?hh
    // This file should contain all your application specific settings, such as resource handling, module configuration etc.
    /**
     * Constants
     */
    const SITE_NAME = 'HydraCore Monitor Client';
    const AUTHOR = 'Ryan Howell';
    const REGISTER_SHUTDOWN = true;
    const MODE = 'API';

    /**
     * Hooks
     */
    $hydraCoreSettings['hooks'] = [
        'preReceive' => [
            'HC\Hooks\PreReceive\Lock' => true,
        ],
        'postReceive' => [
            'HC\Hooks\PostReceive\UpdateComposer' => true,
            'HC\Hooks\PostReceive\UpdateBower' => true,
            'HC\Hooks\PostReceive\Unlock' => true
        ],
        'cron' => [
            'HCMC\Hooks\Cron\ProcessBackups' => [
                'microtime' => 3600
            ],
            'HCMC\Hooks\Cron\ProcessActions' => [
                'microtime' => 60
            ],
        ]
    ];