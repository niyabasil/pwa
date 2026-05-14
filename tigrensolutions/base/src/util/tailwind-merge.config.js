import {
    createTailwindMerge,
    getDefaultConfig,
    mergeConfigs
} from 'tailwind-merge';

const extendThemeMerge = {
    // ↓ Add values to existing theme scale or create a new one
    theme: {
        spacing: [
            '2xs',
            'xs',
            'sm',
            'md',
            'lg',
            'DEFAULT',
            'filterSidebarWidth'
        ],
        // TODO @TW: review. Use the abstracted values in code.
        opacity: ['disabled', 'mask-dark', 'mask-light']
    }
};

const customTwMerge = createTailwindMerge(getDefaultConfig, () =>
    mergeConfigs(getDefaultConfig(), {
        ...extendThemeMerge,
        // ↓ Add values to existing theme scale or create a new one
        classGroups: {
            shadow: [
                {
                    shadow: [
                        'buttonFocus',
                        'dialog',
                        'headerTrigger',
                        'inputFocus',
                        'menu',
                        'modal',
                        'radioActive',
                        'radioFocus',
                        'thin',
                        'search'
                    ]
                }
            ],
            'align-content': ['stretch'],
            'bg-color': ['body', 'header', 'subtle', 'disabledTile'],
            'bg-image': [
                {
                    bg: ['gradient-radial', 'swatch', 'swatch-selected']
                }
            ],
            'border-color': [
                'button',
                'error',
                'info',
                'input',
                'light',
                'shaded',
                'strong',
                'subtle',
                'success',
                'warning'
            ],
            rounded: [
                'radius1',
                'radius2',
                'radius3',
                'default',
                'radiusInput'
            ],
            flex: ['textInput'],
            'font-family': [
                {
                    font: ['sans', 'serif', 'theme', 'inherit']
                }
            ],
            'font-size': [
                {
                    text: [
                        '2xs',
                        '2xl',
                        '3xl',
                        '4xl',
                        '5xl',
                        'inherit',
                        'err-size',
                        'input-size',
                        'unset'
                    ]
                }
            ],
            'font-weight': [
                {
                    font: ['DEFAULT']
                }
            ],
            'col-end': [
                {
                    'col-end': ['span1', 'span2']
                }
            ],
            'grid-cols': [
                {
                    'grid-cols': [
                        'auto',
                        'autoAuto',
                        'autoFirst',
                        'autoLast',
                        'carouselThumbnailList'
                    ]
                }
            ],
            'grid-rows': [
                {
                    'grid-rows': ['auto', 'autoFirst', 'autoLast']
                }
            ],
            h: [
                {
                    h: [
                        'fitContent',
                        'minContent',
                        'unset',
                        'toolbar',
                        'input',
                        'input-small',
                        'input-search',
                        'search-button'
                    ]
                }
            ],
            'justify-content': [
                {
                    justify: ['stretch']
                }
            ],
            leading: [
                {
                    leading: ['DEFAULT']
                }
            ],
            'max-h': [
                {
                    'max-h': ['modal']
                }
            ],
            'max-w': [
                {
                    'max-w': ['modal', 'site']
                }
            ],
            'min-h': [
                {
                    'min-h': ['auto']
                }
            ],
            'min-w': [
                {
                    'min-w': ['auto', 'unset', 'search']
                }
            ],
            order: [
                {
                    order: ['unset']
                }
            ],
            'text-color': [
                {
                    text: ['colorDefault', 'subtle', 'DEFAULT']
                }
            ],
            z: [
                {
                    z: [
                        'behind',
                        'surface',
                        'foreground',
                        'button',
                        'buttonHover',
                        'buttonFocus',
                        'dropdown',
                        'header',
                        'headerDropdown',
                        'mask',
                        'menu',
                        'dialog',
                        'toast'
                    ]
                }
            ],
            display: ['webkit-box']
        },
        conflictingClassGroups: {}
    })
);

export default customTwMerge;
