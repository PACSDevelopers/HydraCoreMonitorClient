<?hh
    $hydraCoreSettings['pages'] = [];

    /**
     * Hooks
     */
    $hydraCoreSettings['hooks'] = [
        'preReceive' => [
            'HC\Hooks\PreReceive\Lock' => true,
        ],
        'postReceive' => [
            'HC\Hooks\PreReceive\Lock' => true,
            'HC\Hooks\PostReceive\UpdateComposer' => true,
            'HC\Hooks\PostReceive\UpdateBower' => true,
            'HC\Hooks\PostReceive\CompileResources' => [
                'languages' => [
                    'js' => true,
                    'scss' => true,
                    'less' => true
                ],
                'path' => '/resources/'
            ],
            'HC\Hooks\PostReceive\GenerateErrorPages' => true,
            'HC\Hooks\PostReceive\Unlock' => true,
        ]
    ];
