<?hh
    /**
     * Hooks
     */
    $hydraCoreSettings['hooks'] = [
        'preReceive' => [
            'HC\Hooks\PreReceive\Lock' => true,
        ],
        'postReceive' => [
            'HC\Hooks\PostReceive\CompileResources' => [
                'languages' => [
                    'js' => true,
                    'scss' => true,
                    'less' => true
                ],
                'path' => '/resources/'
            ],
            'HC\Hooks\PostReceive\Unlock' => true
        ]
    ];
